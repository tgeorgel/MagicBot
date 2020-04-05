<?php

/**
 * Print server output
 * 
 * @param string $message
 * @param string $type
 * @param string $prefix
 * 
 * @return void
 * @since 1.0
 */
function printl($message, $type = 'info', $prefix = '')
{
    switch ($type) {
        case 'info':
            $type = "•• INFO ••";
            break;
        case 'warn':
            $type = "•• WARNING ••";
            break;
        case 'event':
            $type = "•• EVENT ••";
            break;
        case 'error':
            $type = "•• ERROR ••";
            break;
        case 'ferror':
            $type = "•• FATAL ERROR ••";
            break;
    }
    echo $prefix . $type . "  →  " . $message . "\n";
}

/**
 * TS3 Hooks
 */

/**
 * Called on bot timeout
 */
function onTimeout($seconds, TeamSpeak3_Adapter_ServerQuery $adapter)
{
    $bot = \App\Classes\Bot::getinstance();

    echo "•••••••••• Timeout sent by TS3 connector\n";

    $bot->query->getAdapter()->wait();
}


/**
 * Dispatch the event to the bot class
 */
function onEvent(TeamSpeak3_Adapter_ServerQuery_Event $event, TeamSpeak3_Node_Host $host)
{
    $bot = \App\Classes\Bot::getinstance();

    try {
        $bot->dispatchEvent($event);
    } catch (Exception $e) {
        printl("An event were not treaten correctly: " . $e->getMessage(), 'warn', "\n");
    }
}


/**
 * Called on bot connect
 */
function onConnect(TeamSpeak3_Adapter_ServerQuery $adapter)
{
    printl("Server is running with version " . $adapter->getHost()->version('version') . " on " . $adapter->getHost()->version('platform'));
}


/**
 * Called on bot authentication
 */
function onLogin(TeamSpeak3_Node_Host $host)
{
    printl("Authenticated as user \"" . $host->whoamiGet('client_login_name') . "\"");
}


/**
 * Called on server selection
 */
function onSelect(TeamSpeak3_Node_Host $host)
{
    printl("Selected virtual server Id → " . $host->serverSelectedId());

    $host->serverGetSelected()->notifyRegister('server');
    $host->serverGetSelected()->notifyRegister('channel');
    $host->serverGetSelected()->notifyRegister('textserver');
    $host->serverGetSelected()->notifyRegister('textchannel');
    $host->serverGetSelected()->notifyRegister('textprivate');
}
