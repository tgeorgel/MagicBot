<?php

namespace App\Classes;

use TeamSpeak3;
use TeamSpeak3_Helper_Signal;
use TeamSpeak3_Node_Abstract;
use TeamSpeak3_Exception;
use TeamSpeak3_Adapter_ServerQuery;
use TeamSpeak3_Adapter_ServerQuery_Event;

use App\Traits\BotChannelsTrait;

class Bot
{
    use BotChannelsTrait;

    private static $_instance = null;

    public $server_infos;
    public $query;


    /**
     * Return bot instance
     */
    public static function getinstance(): Bot
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Bot();

            self::$_instance->registerEvents();
        }

        return self::$_instance;
    }


    /**
     * Init the class
     */
    private function __construct()
    {
        TeamSpeak3::init();
    }


    /**
     * Startup the bot
     */
    public function startup()
    {
        $uri = sprintf(
            "serverquery://%s:%s@%s:%s/?server_port=%s&nickname=%s&timeout=500&blocking=0",
            $_ENV['TS_QUERY_USER'],
            $_ENV['TS_QUERY_PASSWORD'],
            $_ENV['TS_SERVER_ADDRESS'],
            $_ENV['TS_QUERY_PORT'],
            $_ENV['TS_SERVER_PORT'],
            $_ENV['BOT_NICKNAME']
        );

        try {
            $this->query = TeamSpeak3::factory($uri);
        } catch (TeamSpeak3_Exception $e) {
            die("The server connection failed. The message was: " . $e->getMessage());
        }

        $this->finishStartup();

        return $this->query;
    }


    public function finishStartup()
    {
        // Push connection message   
        if (isset($_ENV['BOT_MESSAGE_ON_CONNECT'])) {
            $this->query->logAdd($_ENV['BOT_MESSAGE_ON_CONNECT']);

            printl("Sent bot connection message to your server..");
        }

        // Move to default channel
        if (isset($_ENV['BOT_DEFAULT_CHANNEL'])) {
            try {
                $this->query->clientMove(
                    (int) $this->query->whoamiGet("client_id"),
                    $_ENV['BOT_DEFAULT_CHANNEL'],
                    $_ENV['BOT_DEFAULT_CHANNEL_PASS']
                );

                printl(sprintf("Moved to default channel (id=%d)", intval($_ENV['BOT_DEFAULT_CHANNEL'])));
            } catch (\TeamSpeak3_Adapter_ServerQuery_Exception $e) {
                printl("Could not move the bot to it's default channel due to an error : " . $e->getMessage());
            }
        }

        // All set.
        printl("All good! waiting now for event or command..");
    }


    public function loop()
    {
        while (1) {
            try {
                $this->server_infos = $this->query->getInfo();

                /**
                 * 
                 */

                $this->query->getAdapter()->wait();
            } catch (\Exception $e) {
                die("\n•• FATAL ERROR ••  →  " . $e->getMessage() . "\n");
            }

            sleep(1);
        }
    }


    /**
     * Event registration
     * 
     * @return void
     */
    public function registerEvents()
    {
        $signal_instance = TeamSpeak3_Helper_Signal::getInstance();

        $signal_instance->subscribe('serverqueryConnected', 'onConnect');
        $signal_instance->subscribe('notifyLogin', 'onLogin');
        $signal_instance->subscribe('notifyServerselected', 'onSelect');
        $signal_instance->subscribe('notifyEvent', 'onEvent');
        // $signal_instance->subscribe(strtolower(TeamSpeak3_Adapter_ServerQuery::class) . 'WaitTimeout', 'onTimeout');
    }


    public function dispatchEvent(\TeamSpeak3_Adapter_ServerQuery_Event $event)
    {
        $type = $event->getType();
        $data = $event->getData();

        // var_dump($type, $data);

        switch ($type) {
            case 'clientmoved':
                $this->onClientMoved($event);
                break;
        }

        // if (($type == 'textmessage') && ($srv->whoamiGet('client_login_name') != $data['invokername']))
        //     onTextMessage($event);
        // elseif ($type == 'clientmoved' && $cfg['modules']['sub_channel']['enabled'] == true)
        //     onClientMoved($event);
        // elseif ($type == 'cliententerview' && $cfg['modules']['welcome_pm']['enabled'] == true && ($srv->whoamiGet('client_login_name') != $data['client_nickname']))
        //     onEnteredView($event);
        // else
        //     printl("Notification: " . $type . ": " . $event->getMessage() . "\n");

        unset($type, $data, $event, $host);
    }


    public function onTextMessage()
    {
    }


    public function onClientMoved($event)
    {
        \App\Modules\SubChannels::run($event);
    }


    public function onClientEnterView()
    {
    }
}
