<?php


function printl($s, $t = 'info', $sp = "")
{
  switch ($t) {
    case 'info':
      $t = "•• INFO ••";
      break;
    case 'warn':
      $t = "•• WARNING ••";
      break;
    case 'event':
      $t = "•• EVENT ••";
      break;
    case 'error':
      $t = "•• ERROR ••";
      break;
    case 'ferror':
      $t = "•• FATAL ERROR ••";
      break;
  }
  echo $sp.$t."  →  ".$s."\n";
}


function channelExist($channel) // If channel exist, return 1, else return 0.
{
	global $srv;
	try
	{
		$channel_f = $srv->channelGetByName($channel);
		return 1;
	}
	catch (Exception $e) { return 0; }
}



function isServergroupMember(TeamSpeak3_Node_Client $cl, array $sgs = array()) // If client is in the server groups list return 1, else return 0.
{
  try
  {
    $cl_sgs = explode(',', (string)$cl->client_servergroups);

    foreach ($cl_sgs as $cl_sg)
      if (in_array($cl_sg, $sgs)) return 1;

    return 0;
  }
  catch (Exception $e) {
    printl("Client SG Member Check failed: ".$e->getMessage(), 'warn');
    return 0;
  }
}


function isClientsUID(TeamSpeak3_Node_Client $cl, $uid)
{
  try
  {
    if ((string)$cl->client_unique_identifier == $uid) return 1;
    else return 0;
  }
  catch (Exception $e) {
    printl("Client UID Check failed: ".$e->getMessage(), 'warn');
    return 0;
  }
}


function isGoodToReceiveSGS($c_tocheck, $rules, $mode)
{
  if ($mode === "ignore")
  {
    foreach ($c_tocheck as $id)
    {
      if (in_array($id, $rules))
        return 0;
    }
    return 1;
  }
  elseif ($mode === "only")
  {
    foreach ($c_tocheck as $id)
    {
      if (in_array($id, $rules))
        return 1;
    }
    return 0;
  }
  else
    return 1;
}






?>
