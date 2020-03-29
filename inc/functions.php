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
    echo $sp . $t . "  →  " . $s . "\n";
}


function channelExist($channel)
{
    global $srv;
    try {
        $channel_f = $srv->channelGetByName($channel);
        return 1;
    } catch (Exception $e) {
        return 0;
    }
}



function isServergroupMember(TeamSpeak3_Node_Client $cl, array $sgs = array()) // If client is in the server groups list return 1, else return 0.
{
    try {
        $cl_sgs = explode(',', (string) $cl->client_servergroups);

        foreach ($cl_sgs as $cl_sg)
            if (in_array($cl_sg, $sgs)) return 1;

        return 0;
    } catch (Exception $e) {
        printl("Client SG Member Check failed: " . $e->getMessage(), 'warn');
        return 0;
    }
}


function isClientsUID(TeamSpeak3_Node_Client $cl, $uid)
{
    try {
        if ((string) $cl->client_unique_identifier == $uid) return 1;
        else return 0;
    } catch (Exception $e) {
        printl("Client UID Check failed: " . $e->getMessage(), 'warn');
        return 0;
    }
}


function isGoodToReceiveSGS($c_tocheck, $rules, $mode)
{
    if ($mode === "ignore") {
        foreach ($c_tocheck as $id) {
            if (in_array($id, $rules))
                return 0;
        }
        return 1;
    } elseif ($mode === "only") {
        foreach ($c_tocheck as $id) {
            if (in_array($id, $rules))
                return 1;
        }
        return 0;
    } else
        return 1;
}


function onTimeout($seconds, TeamSpeak3_Adapter_ServerQuery $adapter)
{
    $bot = \App\Classes\Bot::getinstance();

    echo "•••••••••• Timeout sent by TS3 connector\n";

    $bot->query->getAdapter()->wait();
}


function onEvent(TeamSpeak3_Adapter_ServerQuery_Event $event, TeamSpeak3_Node_Host $host)
{
    $bot = \App\Classes\Bot::getinstance();

    try {
        $bot->dispatchEvent($event);
    } catch (Exception $e) {
        printl("An event were not treaten correctly: " . $e->getMessage(), 'warn', "\n");
    }
}

function onConnect(TeamSpeak3_Adapter_ServerQuery $adapter)
{
    printl("Server is running with version " . $adapter->getHost()->version('version') . " on " . $adapter->getHost()->version('platform'));
}


function onLogin(TeamSpeak3_Node_Host $host)
{
    printl("Authenticated as user \"" . $host->whoamiGet('client_login_name') . "\"");
}


function onSelect(TeamSpeak3_Node_Host $host)
{
    printl("Selected virtual server Id → " . $host->serverSelectedId());

    $host->serverGetSelected()->notifyRegister('server');
    $host->serverGetSelected()->notifyRegister('channel');
    $host->serverGetSelected()->notifyRegister('textserver');
    $host->serverGetSelected()->notifyRegister('textchannel');
    $host->serverGetSelected()->notifyRegister('textprivate');
}
