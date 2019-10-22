<?php

//  --------  [  Vars  ]  --------  //



//  --------  [  Core  ]  --------  //

function core_BotCommands(TeamSpeak3_Adapter_ServerQuery_Event $e)
{
  global $cfg, $bot_id, $srv;
  $info   = $e->getData();
  $client = $srv->clientGetByName($info["invokername"]->toString());

  $adminlevel = core_Authentication($client);
  printl("User's admin level is: ".$adminlevel);

  // $topic = strstr('Some topic :: part to exclude', '::', TRUE);

  try
  {
    $cmd = explode(" ", $info["msg"]);

    if     ($cmd[0] == "!pmme" && $adminlevel >= 0) $client->message("• Hello, it's me, MagicBot! •");
    elseif ($cmd[0] == "!botrestart" && $adminlevel == 3) cmd_botRestart($e);
    elseif ($cmd[0] == "!botreload") echo "reload";
    elseif ($cmd[0] == "!sleep" && $adminlevel >=1) sleep(10);
    elseif ($cmd[0] == "!msg" && $adminlevel >= 1) echo "msg"; //cmd_sendPM($cmd);
    elseif ($cmd[0] == "!accept" && $cfg['modules']['SG_onRulesAccept']['enabled']) plugin_SGonRulesAccept($e);
  }
  catch (Exception $ex)    { printl($ex->getMessage()."\n", 'warn', "\n"); }
}

// Log out and restart the bot
function cmd_botRestart(TeamSpeak3_Adapter_ServerQuery_Event $e)
{
  global $srv;
  $info = $e->getData();
  $srv->clientGetByName($info["invokername"]->toString())->message("• Restarting now, please allow up to 10 seconds •");
  $srv->getParent()->logout();
  shell_exec('php TeamspeakBot.php');
  exit();
}


function cmd_sendPM()
{/*
  global $cfg, $bot_id, $srv;
  $info   = $e->getData();

  try
  {
    $cmd = explode(":", $info["msg"]);
    $srv->clientGetByUid($cmd[1])->message(trim($cmd[2]));
  }
  catch (Exception $e)
  {
  $srv->clientGetByName($info["invokername"]->toString())->message("Error.. Your parameters were incorrect..");
  printl($ex->getMessage()."\n", 'warn', "\n");
  }*/
}


// Anthentificate the user as basic, admin or super-admin
function core_Authentication(TeamSpeak3_Node_Client $user)
{
  global $cfg;

  // Check if super admin.
  foreach ($cfg['super-administrators'] as $info)
  {
    if (preg_matcH('/=/', $info))
      $c = isClientsUID($user, $info);
    else
      $c = isServergroupMember($user, $info);

    if ($c) return 3;
  }


  // Not super admin? Check if admin.
  foreach ($cfg['administrators'] as $info)
  {
    if (preg_matcH('/=/', $info))
      $c = isClientsUID($user, $info);
    else
      $c = isServergroupMember($user, $info);

    if ($c) return 2;
  }


  // Not admin? Check if moderator.
  foreach ($cfg['moderators'] as $info)
  {
    if (preg_matcH('/=/', $info))
      $c = isClientsUID($user, $info);
    else
      $c = isServergroupMember($user, $info);

    if ($c) return 1;
  }

  // None of them? Return a 0.
  return 0;
}



//  --------  [  Plugins  ]  --------  //

function plugin_SubChannels(TeamSpeak3_Adapter_ServerQuery_Event $e)
{
	printl("Enter SubChannel check");
	global $cfg, $bot_id, $srv;
	$data = $e->getData();

  // Check if the client is ignored, if not, continue
  // if (isServergroupMember($srv->clientGetById($data['clid']), $cfg['modules']['sub_channel']['cfg']['ignored_sg'])) return;

  // Personalised channels.
  if ($cfg['modules']['sub_channel_personalised']['enabled'])
  {
    foreach($cfg['modules']['sub_channel_personalised']['cfg']['rules'] as $rule)
    {
      if ($data['ctid'] == $rule[0])
      {
        try
        {
          foreach ($srv->clientGetIdsByUid($rule[2]) as $id)
          {
            if ($id['clid'] == $data['clid'])
            {
              $chan_properties = [
                          "channel_name"                       				=> $rule[3],
                          "channel_flag_temporary"             				=> TRUE,
                          "channel_delete_delay" 		           				=> $cfg['modules']['sub_channel_personalised']['cfg']['channel_delete_delay'],
                          "cpid"                               				=> $rule[0],
                          "channel_password"                   				=> $rule[1],
                          "channel_flag_maxfamilyclients_inherited"		=> $cfg['modules']['sub_channel_personalised']['cfg']['channel_maxfamilyclient_inherited']
                        ];

              if ($cfg['modules']['sub_channel_personalised']['cfg']['channel_max_client'] > 0) {
                $chan_properties['channel_flag_maxclients_unlimited']     = FALSE;
                $chan_properties['channel_maxclients']                    = $cfg['modules']['sub_channel_personalised']['cfg']['channel_max_client'];
              }
              elseif ($cfg['modules']['sub_channel_personalised']['cfg']['channel_max_client'] == 0)
                $chan_properties['channel_flag_maxclients_unlimited']     = FALSE;
              elseif ($cfg['modules']['sub_channel_personalised']['cfg']['channel_max_client'] == -1)
                $chan_properties['channel_flag_maxclients_unlimited']     = TRUE;

              $createdId	= $srv->channelCreate($chan_properties);
              $srv->clientMove($bot_id, $cfg['bot_default_channel_id'], $cfg['bot_default_channel_pass']);
              $srv->clientMove($data['clid'], $createdId, $rule[1]);

              printl("Personalised sub-channel created : ".$rule[3]." (ParentID ".$rule[0].") (ChannelID ".$createdId.")");
              return 1;
            }
          }
        }
        catch (Exception $e) {
          printl("Channel creation is not possible: ".$e->getMessage(), 'warn');
        }
      }
    }
  }


  // Sub-channels channels.
  if ($cfg['modules']['sub_channel']['enabled'])
  {
    foreach($cfg['modules']['sub_channel']['cfg']['rules'] as $rule)
    {
      if ($data['ctid'] == $rule[0])
      {
        $count = count($rule);
        $exist = 1;
        $i = 1;

        if ($count > 3) // Module = static
        {
          while ($exist == 1 && $i < $count)
          {
            ++$i;
            $exist = channelExist($rule[$i]);
          }
          if ($exist == 0)
            $channel_name = $rule[$i];
        }
        elseif ($count == 3) // Module = dynamic
        {
          --$i;
          $max_subchannels_create = $cfg['modules']['sub_channel']['cfg']['max_subchannels_create'];
          if (0 < $max_subchannels_create && $max_subchannels_create <= 200)
          {
            while ($exist == 1 && $i <= $max_subchannels_create)
            {
              ++$i;
              $exist = channelExist($rule[2].$i);
            }
            if ($exist == 0)
              $channel_name = $rule[2].$i;
          }
        }
        else {
          printl("Sub-Channel Module: Rule parameters seems's incorrect", 'warn', "\n");
          return 0;
        }

        if (isset($channel_name))
        {
          try
          {
            $chan_properties = [
                        "channel_name"                       => $channel_name,
                        "channel_flag_temporary"             => TRUE,
                        "channel_delete_delay" 		         => $cfg['modules']['sub_channel']['cfg']['channel_delete_delay'],
                        "cpid"                               => $rule[0],
                        "channel_password"                   => $rule[1]
                      ];
            if ($cfg['modules']['sub_channel']['cfg']['channel_max_client'] > 0)
            {
              $chan_properties['channel_flag_maxclients_unlimited'] = FALSE;
              $chan_properties['channel_maxclients'] = $cfg['modules']['sub_channel']['cfg']['channel_max_client'];
            }
            elseif ($cfg['modules']['sub_channel_personalised']['cfg']['channel_max_client'] == 0)
              $chan_properties['channel_flag_maxclients_unlimited'] = FALSE;
            elseif ($cfg['modules']['sub_channel']['cfg']['channel_max_client'] == -1)
              $chan_properties['channel_flag_maxclients_unlimited'] = TRUE;


            $createdId 	= $srv->channelCreate($chan_properties);
            $srv->clientMove($bot_id, $cfg['bot_default_channel_id'], $cfg['bot_default_channel_pass']);
            $srv->clientMove($data['clid'], $createdId, $rule[1]);

            printl("Channel created : ".$channel_name." (ParentID ".$rule[0].") (ChannelID ".$createdId.")");
            return 1;
          }
          catch (Exception $e) {
            printl("Channel creation is not possible: ".$e->getMessage(), 'warn');
          }
        }
        else
        {
          // Notify the client that the limit has been reached.
          $srv->clientGetById($data['clid'])->message($cfg['modules']['sub_channel']['cfg']['reached_limit_msg']);
          printl("onMoved: Can't create channel because Sub-channels limit has been reached.");
          return 0;
        }
      }
    }
  }
}



function plugin_WelcomePm(TeamSpeak3_Adapter_ServerQuery_Event $e)
{
  global $cfg, $srv;
  $data = $e->getData();

  $cl_sgs = explode(",", $data['client_servergroups']);

  try
  {
    foreach ($cfg['modules']['welcome_pm']['msg']['rules'] as $rule)
    {
      $sgs = explode(",", $rule[0]);

      if (isGoodToReceiveSGS($sgs, $cl_sgs, $rule[1]))
        $srv->clientGetById($data["clid"])->message($rule[2]);

      unset($data, $cl_sgs, $e);
      return;
    }
  }
  catch (Exception $e)
  {
    printl("Welcome PM could not be sent: ".$e->getMessage(), 'warn');
  }
}

function plugin_ChannelNotify(TeamSpeak3_Adapter_ServerQuery_Event $e)
{
  global $cfg, $srv;
  $data = $e->getData();

  $cl_sgs = explode(",", $data['client_servergroups']);


  $srv->clientGetById($data["clid"]);
}


function plugin_SGonRulesAccept(TeamSpeak3_Adapter_ServerQuery_Event $e)
{
  global $cfg, $srv;
  $data = $e->getData();

  $cl = $srv->clientGetByName($data["invokername"]->toString());

  foreach ($cfg['modules']['SG_onRulesAccept']['sgs_togive'] as $id)
  {
    if (!isServergroupMember($cl, array(trim($id)))) $cl->addServerGroup(trim($id));
  }

  $cl->message($cfg['modules']['SG_onRulesAccept']['client_msg']);
}



function plugin_SGonChannelJoin(TeamSpeak3_Adapter_ServerQuery_Event $e)
{
  global $cfg, $srv;

  if ($cfg['modules']['SG_onChannelJoin']['enabled'])
  {
    $data = $e->getData();
    $cl = $srv->clientGetById($data["clid"]);

    foreach ($cfg['modules']['SG_onChannelJoin']['rules'] as $rule)
    {
      $chans  = array_map('trim', explode(",", $rule[1]));

      if (in_array($data['ctid'], $chans))
      {
        $sgs    = array_map('trim', explode(",", $rule[0]));
        foreach ($sgs as $id)
        {
          if (!isServergroupMember($cl, array($id))) $cl->addServerGroup($id);
        }
        if($rule[2]) $cl->message($rule[2]);
        if($rule[3]) $cl->kick(4);
        break;
      }
    }
  }
}



?>
