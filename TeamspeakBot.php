<?php

require_once 'lib/TeamSpeak3/TeamSpeak3.php';
require_once 'cfg/config.php';
require_once 'inc/functions.php';
require_once 'inc/core.php';

TeamSpeak3::init();

$last_info;
$last_check = time();
$in_timeout_check = false;
$doubleECheck = 0;


echo "\n\n";
echo "•••|    THIS IS NOT A RELASE!! This bot may contain bugs     |•••\n\n";

try
{
  // Init the connection announcement
  printl("Init Connection..");

  // Events
  TeamSpeak3_Helper_Signal::getInstance()->subscribe('serverqueryConnected', 'onConnect');
  TeamSpeak3_Helper_Signal::getInstance()->subscribe('serverqueryWaitTimeout', 'onTimeout');
  TeamSpeak3_Helper_Signal::getInstance()->subscribe('notifyLogin', 'onLogin');
  TeamSpeak3_Helper_Signal::getInstance()->subscribe('notifyEvent', 'onEvent');
  TeamSpeak3_Helper_Signal::getInstance()->subscribe('notifyServerselected', 'onSelect');

  $uri = "serverquery://" . $srv_cfg['q_user'] . ":" . $srv_cfg['q_pass']
                          . "@" . $srv_cfg['s_address'] . ":" . $srv_cfg['q_port']
                          . "/?server_port=" . $srv_cfg['s_port']
                          . "&nickname=" . $cfg['bot_nickname']
                          . "&blocking=0";

  // Connecting
  $srv = TeamSpeak3::factory($uri);


  // Echo the connection informations.
  printl("Connected on ".$srv_cfg['s_address'].":".$srv_cfg['q_port'].". Query user is ".$srv_cfg['q_user']." on port ".$srv_cfg['q_port']);
  $srv->logAdd($cfg['onconnect_log_msg']);

  // If set, switching to default channel.
  if ($cfg['bot_default_channel_id'] != "")
  {
    $bot_id = (int) $srv->whoamiGet("client_id");
    $srv->clientMove($bot_id, $cfg['bot_default_channel_id'], $cfg['bot_default_channel_pass']);
    printl("Switched to default channel (id=" . $cfg['bot_default_channel_id'] . ")");
  }

  // Ready for actions
  printl("All good! waiting now for event or command..");
}
catch (Exception $e)
{
  die("\n•• FATAL ERROR ••  →  Bot Connection Exception: " . $e->getMessage() . "\n•• INFO ••  →  Stopping now..\n\n");
}


// Keep the Bot watching for infos..
while (1) {
    try                   { $srv->getAdapter()->wait(); }
    catch (Exception $e)  { die ("\n•• FATAL ERROR ••  →  Connection Timeout: → " . $e->getMessage() . "\n"); }
}




// ====== [ CALLBACKS FUNCTIONS ] ====== //




function onConnect(TeamSpeak3_Adapter_ServerQuery $adapter)
{
	printl("Server is running with version " . $adapter->getHost()->version('version') . " on " . $adapter->getHost()->version('platform'));
}


function onLogin(TeamSpeak3_Node_Host $host)
{
	printl("Authenticated as user \"".$host->whoamiGet('client_login_name')."\"");
}


function onTimeout($seconds, TeamSpeak3_Adapter_ServerQuery $adapter)
{
	global $cfg, $srv, $last_check, $in_timeout_check;
	if(floor(time() - $last_check) >= $cfg['anti_timeout'])
	{
		$last_check = time();
		if(!empty($cfg['hooks']['onTimeout']))
			foreach($cfg['hooks']['onTimeout'] as $hook)
			{
				if($in_timeout_check)
				{
          printl("onTimeout - Already in Timeout Hook exiting this loop");
					break;
				}
				$in_timeout_check = true;
        printl("onTimeout - Starting Hook - ".$hook);
				$hook();
				$in_timeout_check = false;
			}
	}
	if($adapter->getQueryLastTimestamp() < time()-300)
	{
		printl("Sending keep-alive command");
		$adapter->request('clientupdate');
	}
}


function onEvent(TeamSpeak3_Adapter_ServerQuery_Event $event, TeamSpeak3_Node_Host $host)
{
	global $cfg, $srv, $doubleECheck;

  try
  {
    if ($doubleECheck == 0)
  	{
      $doubleECheck = 1;
  		$type = $event->getType();
  		$data = $event->getData();

  		if (($type == 'textmessage') && ($srv->whoamiGet('client_login_name') != $data['invokername']))
        	onTextMessage($event);
  		elseif($type == 'clientmoved' && $cfg['modules']['sub_channel']['enabled'] == true)
  			onClientMoved($event);
      elseif($type == 'cliententerview' && $cfg['modules']['welcome_pm']['enabled'] == true && ($srv->whoamiGet('client_login_name') != $data['client_nickname']))
        onEnteredView($event);
      else
        printl("Notification: ".$type.": ".$event->getMessage()."\n");

      unset($type, $data, $event, $host);
  	}
  	else
  		$doubleECheck = 0;
  }
  catch (Exception $e)
  {
    printl("An event were not treaten correctly: ".$e->getMessage(), 'warn', "\n");
  }
}


function onClientMoved(TeamSpeak3_Adapter_ServerQuery_Event $e)
{
  global $srv, $cfg;
  $data = $e->getData();
	printl("onMoved Check Begins");

  //plugin_ChannelNotify($e);
  plugin_SGonChannelJoin($e);
  plugin_SubChannels($e);
}


function onTextMessage(TeamSpeak3_Adapter_ServerQuery_Event $e)
{
  global $srv;
  $info   = $e->getData();

  // Print info
  switch ($info["targetmode"]) {
      case 1:
          printl("New Private Message from " . $info["invokername"]->toString() . ": " . $info["msg"], 'info', "\n");
          break;
      case 2:
          printl("New Message from " . $info["invokername"]->toString() . " in Channel " . $srv->channelGetById($srv->whoamiGet("client_channel_id"))->toString() . ": " . $info["msg"], 'info', "\n");
          break;
      case 3:
          printl("New Server Message from " . $info["invokername"]->toString() . ": " . $info["msg"], 'info', "\n");
          break;
  }

  // If it's a command, we call the function in Core.php
  if (substr($info["msg"], 0, 1) === '!')
  {
    printl("Message from " . $info["invokername"]->toString() . " is a command.");
    core_BotCommands($e);
  }
}




function onEnteredView(TeamSpeak3_Adapter_ServerQuery_Event $e)
{
  printl("onEnteredView Check Begins");
  plugin_WelcomePm($e);
}


function onSelect(TeamSpeak3_Node_Host $host)
{
	printl("Selected virtual server Id → ".$host->serverSelectedId());

	$host->serverGetSelected()->notifyRegister('server');
	$host->serverGetSelected()->notifyRegister('channel');
	$host->serverGetSelected()->notifyRegister('textserver');
	$host->serverGetSelected()->notifyRegister('textchannel');
	$host->serverGetSelected()->notifyRegister('textprivate');
}

?>
