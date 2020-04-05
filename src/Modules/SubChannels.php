<?php

namespace App\Modules;

use stdClass;
use App\Classes\Bot;
use App\Classes\Config;

class SubChannels
{
    protected static $conf  = null;
    protected static $bot   = null;


    /**
     * Module entry point
     * 
     * @param App\Classes\Bot $bot
     * @param array           $event
     * 
     * @return void
     * @since 2.0
     */
    public static function run($event)
    {
        $cfg    = Config::getinstance();
        $opts   = $cfg->subchannels;

        if ($opts === false) {
            printl(
                'Could not load subchannels module config. Make sure `config/subchannels.json` exists.',
                'error'
            );
            return;
        }

        if ($opts->enabled !== true) {
            return;
        }

        $invoker = $event->getData()['clid'];
        $e_chan  = $event->getData()['ctid'];

        static::$conf  = $opts;
        static::$bot   = Bot::getinstance();


        foreach ($opts->rules as $rule) {
            if ($rule->channel_id === $e_chan) {
                try {
                    static::fire($rule, (int) $invoker);
                } catch (\Exception $e) {
                    static::error($e->getMessage());
                }
                return;
            }
        }
    }


    /**
     * Module main worker. Create the sub-channel with properties and move the client into it
     * Send a message to the client if configured
     * 
     * @param stdClass $rule The current rule applied
     * @param int      $invoker Id of the user tht triggered the event
     * @since 2.0
     */
    protected static function fire(stdClass $rule, int $invoker)
    {
        $max_subchans   = static::getMaxSubChan($rule);
        $chan_groups    = static::getOnJoinChannelsGroups($rule);
        $c_name         = static::getCustomChanName($invoker, $rule->names_custom);

        if (!$c_name) {
            $c_name = static::getChanName($rule, $max_subchans);
        }

        if (!$c_name) {
            return static::failure((int) $rule->channel_id);
        }

        $props = static::mergeProperties($rule);

        if (static::getGeneratePassword($rule)) {
            $props->password = static::generatePassword();
        }

        $c_properties = [
            'cpid'                   => $rule->channel_id,
            'channel_name'           => $c_name,
            'channel_flag_temporary' => $props->temporary,
            'channel_delete_delay'   => $props->delete_delay,
            'channel_password'       => $props->password,
            'channel_flag_maxclients_unlimited' => $props->max_clients > 0 ? false : true,
        ];

        if ($c_properties['channel_flag_maxclients_unlimited'] === false) {
            $c_properties['channel_maxclients'] = $props->max_clients;
        }

        $created_id = static::$bot->query->channelCreate($c_properties);
        $invoker    = static::$bot->query->clientGetById($invoker);

        $invoker->move($created_id, $props->password);

        if ($rule->on_create_msg !== '') {
            $invoker->message($rule->on_create_msg);
        }

        if (static::getGeneratePassword($rule)) {
            $invoker->message("Channel password : " . $props->password);
        }

        if (!empty($chan_groups)) {
            foreach ($chan_groups as $chan_group) {
                $invoker->setChannelGroup($created_id, $chan_group);
            }
        }

        static::success((string) $c_name);
    }


    /**
     * Merge the rule chan properties with the default chan properties,
     * keeping rules's one in priority
     * 
     * @param object $rule Rule object
     * 
     * @return object
     * @since 2.0
     */
    protected static function mergeProperties(object $rule): object
    {
        return (object) ((array) $rule->properties + (array) static::$conf->properties);
    }


    /**
     * Get the maximum subchannels authorised
     * 
     * @param object $rule Rule object
     * 
     * @return int
     * @since 2.0
     */
    protected static function getMaxSubChan(object $rule): int
    {
        return $rule->max_subchannels !== -1 ? $rule->max_subchannels : static::$conf->max_subchannels;
    }


    /**
     * Get the server groups to add to the client on channel creation
     * 
     * @param object $rule Rule object
     * 
     * @return array
     * @since 2.1
     */
    protected static function getOnJoinChannelsGroups(object $rule): array
    {
        return $rule->on_create_channel_groups ?? static::$conf->on_create_channel_groups;
    }


    /**
     * Get the generate password option
     * 
     * @param object $rule Rule object
     * 
     * @return bool
     * @since 2.1
     */
    protected static function getGeneratePassword(object $rule): bool
    {
        return $rule->generate_password ?? static::$conf->generate_password;
    }


    /**
     * Get the sub-channel name to use, depending on availability
     * 
     * @param object $rule      Rule options
     * @param int    $max_chans Maximum subchannels allowed
     * 
     * @return string|bool false on failure
     * @since 2.0
     */
    protected static function getChanName(object $rule, int $max_chans)
    {
        if ($rule->mode === 'static') {
            foreach ($rule->names as $name) {
                if (!static::$bot->channelExistsName($name)) {
                    return $name;
                }
            }
            return false;
        }

        if ($rule->mode === 'dynamic') {
            $i = 0;

            while ($i++ <= $max_chans) {
                foreach ($rule->names as $name) {
                    $full_name = $i === 1 ? $name : $name . $i;

                    if (!static::$bot->channelExistsName(trim($full_name))) {
                        return $full_name;
                    }
                }
            }
        }
    }


    /**
     * Get the sub-channel custom name to use, depending on availability
     * 
     * @param string $mode  Rule naming mode
     * @param array  $names Names list
     * 
     * @return string|bool false on failure
     * @since 2.0
     */
    protected static function getCustomChanName(int $invoker, array $customs)
    {
        if (!is_array($customs) || empty($customs)) {
            return false;
        }

        foreach ($customs as $custom) {
            $cl_ids = static::$bot->query->clientGetIdsByUid($custom->client_uid);

            foreach ($cl_ids as $cl_id) {
                if ($cl_id['clid'] === $invoker && !static::$bot->channelExistsName($custom->name)) {
                    return $custom->name;
                }
            }
        }

        return false;
    }


    /**
     * Generate a password for channels
     * 
     * @param int $length 
     * @return string
     */
    protected static function generatePassword(int $length = 6)
    {
        if (function_exists('random_bytes')) {
            $bytes = random_bytes($length / 2);
        } else {
            $bytes = openssl_random_pseudo_bytes($length / 2);
        }
        return bin2hex($bytes);
    }


    /**
     * Log failure
     * 
     * @param  int $p_id Parent id of the failure rule
     * 
     * @return void
     * @since 2.0
     */
    protected static function failure(int $p_id): void
    {
        printl("SubChannels : could not find a sub-chan name for parent $p_id, perhaps all names has been used.");
    }


    /**
     * Log success
     * 
     * @param  string $channel_name Created sub channel name
     * 
     * @return void
     * @since 2.0
     */
    protected static function success(string $channel_name): void
    {
        printl("SubChannels : sub-chan created $channel_name");
    }


    /**
     * Log error
     * 
     * @param string $msg Error msg
     * 
     * @return void
     * @since 2.0
     */
    protected static function error(string $msg): void
    {
        printl("SubChannels : an error occured : $msg", 'error');
    }
}
