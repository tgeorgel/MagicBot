<?php

$srv_cfg['q_user']        = "serveradmin";
$srv_cfg['q_pass']        = "mysecret";

$srv_cfg['s_address']     = "127.0.0.1";
$srv_cfg['s_port']        = "9987";
$srv_cfg['q_port']        = "10011";


$cfg['bot_default_channel_id']      = "797";
$cfg['bot_default_channel_pass']    = "";
$cfg['bot_nickname']                = "NastyBot"; // Max: 30 Characters.
$cfg['onconnect_log_msg']           = "MagicBot by Elektro successfully joined the server!";

// [For the 3 next settings] Can be UIDs (ex: "NRSmvjC6kS4jeH9PbVHkHRrIG5A=") or server groups ids (ex: 48). Other users are Level 0.
$cfg['moderators']                  = [""]; // Level 1
$cfg['administrators']              = ["33", "34"];  // Level 2
$cfg['super-administrators']        = ["NESmvjC6kS4xeH??¿¿yHRrIG6A=", 48]; // Level 3

$cfg['date_format']   = 'd-m G:i:s';
$cfg['anti_timeout']  = 30;


// Max lenghts:
  // Nickname: 30 Characters
  // Poke: 100 Characters
  // Client descr: 200 Characters
  // Channel name: 40 Characters
  // PM : 1024 Bytes



/*

// ====== [ MODULES SETTINGS ] ====== //

*/



// afk mover module //
$cfg['modules']['afk_mover']['enabled'] = false;            // TURN ON/OFF THE MODULE ('true' for on, 'false' for off)

$cfg['modules']['afk_mover']['cfg']['chan_id'] = 0;         // AFK CHAN
$cfg['modules']['afk_mover']['cfg']['time'] = 3600;         // AFK MAX TIME IN SECONDS




// Welcome private Message Module //
    // General settings
$cfg['modules']['welcome_pm']['enabled'] = false;            // TURN ON/OFF THE MODULE ('true' for on, 'false' for off)

    // Messages rules definitions
        // ["msg mode, 'poke' or 'pm'", "server groups ids separated by commas (leave blank for everyone)", "groups mode 'ignore' or 'only'", "Your message, BBCode Can be used for PM."]

$cfg['modules']['welcome_pm']['msg']['rules'][] = ["pm", "71", "only", "Hey petit fondateur, tu es beau."];




// Server Group assignement on Rules accepted //
    // General settings
$cfg['modules']['SG_onRulesAccept']['enabled']      = false;     // TURN ON/OFF THE MODULE ('true' for on, 'false' for off)
$cfg['modules']['SG_onRulesAccept']['sgs_togive']   = ['72'];   // Which Server Groups should be applied on accept
$cfg['modules']['SG_onRulesAccept']['client_msg']   = "Merci d'avoir accepté les règles de notre communauté!";




// Server Group assignement on Channel Join //
    // General settings
$cfg['modules']['SG_onChannelJoin']['enabled']      = false;     // TURN ON/OFF THE MODULE ('true' for on, 'false' for off)

    // Rules definitions
      // = ["servergroups_togive", "watched_channels", "msg_to_client", "kick_on"];
      // = ['72,89,16', '849,25', "You're now channel admin!", true];

$cfg['modules']['SG_onChannelJoin']['rules'][]      = ['72', '796,123', "", true];





// Autocreate Sub-channels module //
    // General settings
$cfg['modules']['sub_channel']['enabled'] = true;                         // Turn ON/OFF the module
$cfg['modules']['sub_channel']['cfg']['max_subchannels_create'] = 100;    // Maximum amount of sub channels (must be btwn 1 and 200.) → Dynamic module
$cfg['modules']['sub_channel']['cfg']['reached_limit_msg'] = "Impossible de créer le sous-channel car la limite à été atteinte.";
$cfg['modules']['sub_channel']['cfg']['channel_max_client'] = 10;         // Max clients for subchannels (set -1 for unlimited)
$cfg['modules']['sub_channel']['cfg']['channel_delete_delay'] = 6;        // Channel auto-delete when empty delay in seconds
$cfg['modules']['sub_channel']['cfg']['ignored_sg'] = ['220'];            // Those server groups will be ignored by the modules (sub_channel & sub_channel_personalised)



    // Channels rules definitions
        // = ["channel_id", "channel_pass", "channel name 1", "channel name 2", "channel name 3"];  → Static module: channels name are defined
        // = ["channel_id", "channel_pass", "channel name "];  → Dynamic module: channels name is set once, then each channels increase (chan_name 1, chan_name 2,...)


$cfg['modules']['sub_channel']['cfg']['rules'][] = ["872", "", "Piltover", "Freljord", "Shurima",
                                                    "Demacia", "Bilgewater", "Noxus", "Runeterra",
                                                    "Ionia", "Serpentine River", "Guardian's sea",
                                                    "Lokfar", "Shadow Isles"];


$cfg['modules']['sub_channel']['cfg']['rules'][] = ["989", "carryme", "[Ranked] Piltover", "[Ranked] Freljord", "[Ranked] Shurima",
                                                    "[Ranked] Demacia", "[Ranked] Bilgewater", "[Ranked] Noxus", "[Ranked] Runeterra",
                                                    "[Ranked] Ionia", "[Ranked] Serpentine River", "[Ranked] Guardian's sea",
                                                    "[Ranked] Lokfar", "[Ranked] Shadow Isles"];


$cfg['modules']['sub_channel']['cfg']['rules'][] = ["889", "", "Alpha", "Bravo", "Charlie",
                                                    "Delta", "Echo", "Foxtrot", "Golf",
                                                    "Hotel", "India", "Juliet",
                                                    "kilo", "Lima", "Mike", "November",
                                                    "Oscar", "Papa", "Quebec", "Romeo",
                                                    "Sierra", "Tango", "Uniform", "Victor",
                                                    "Whiskey", "X-Ray", "Yankee", "Zulu"];


$cfg['modules']['sub_channel']['cfg']['rules'][] = ["897", "awp", "[Ranked] Alpha", "[Ranked] Bravo", "[Ranked] Charlie",
                                                    "[Ranked] Delta", "[Ranked] Echo", "[Ranked] Foxtrot", "[Ranked] Golf",
                                                    "[Ranked] Hotel", "[Ranked] India", "[Ranked] Juliet",
                                                    "[Ranked] kilo", "[Ranked] Lima", "[Ranked] Mike", "[Ranked] November",
                                                    "[Ranked] Oscar", "[Ranked] Papa", "[Ranked] Quebec", "[Ranked] Romeo",
                                                    "[Ranked] Sierra", "[Ranked] Tango", "[Ranked] Uniform", "[Ranked] Victor",
                                                    "[Ranked] Whiskey", "[Ranked] X-Ray", "[Ranked] Yankee", "[Ranked] Zulu"]; // CSGO CHANS RANKED



$cfg['modules']['sub_channel']['cfg']['rules'][] = ["1030", "", "Minecraft - "];              // Minecraft #General chans


$cfg['modules']['sub_channel']['cfg']['rules'][] = ["9147", "", "Groupe "];
$cfg['modules']['sub_channel']['cfg']['rules'][] = ["9152", "", "Groupe "];
$cfg['modules']['sub_channel']['cfg']['rules'][] = ["9148", "", "Groupe "];
$cfg['modules']['sub_channel']['cfg']['rules'][] = ["9150", "", "Groupe "];
$cfg['modules']['sub_channel']['cfg']['rules'][] = ["9153", "", "Groupe "];
$cfg['modules']['sub_channel']['cfg']['rules'][] = ["9151", "", "Groupe "];
$cfg['modules']['sub_channel']['cfg']['rules'][] = ["9149", "", "Groupe "];


$cfg['modules']['sub_channel']['cfg']['rules'][] = ["8821", "", "Salle "];                // GMOD serv chans General
$cfg['modules']['sub_channel']['cfg']['rules'][] = ["8819", "", "Staff "];                // GMOD serv chans Staff


$cfg['modules']['sub_channel']['cfg']['rules'][] = ["921", "", "Champs de Bataille "];  // FPS
$cfg['modules']['sub_channel']['cfg']['rules'][] = ["922", "", "Bataillon "];           // MOBA
$cfg['modules']['sub_channel']['cfg']['rules'][] = ["923", "", "Général "];             // GENERAL
$cfg['modules']['sub_channel']['cfg']['rules'][] = ["924", "", "Bataillon "];           // MMORPG

$cfg['modules']['sub_channel']['cfg']['rules'][] = ["1674", "", "Starcraft II #"];
$cfg['modules']['sub_channel']['cfg']['rules'][] = ["1675", "", "Overwatch #"];
$cfg['modules']['sub_channel']['cfg']['rules'][] = ["915", "", "Hearthstone #"];
$cfg['modules']['sub_channel']['cfg']['rules'][] = ["914", "", "World Of Warcraft #"];
$cfg['modules']['sub_channel']['cfg']['rules'][] = ["1673", "", "Heroes of The Storm #"];

$cfg['modules']['sub_channel']['cfg']['rules'][] = ["1811", "", "Bataillon - #"];
$cfg['modules']['sub_channel']['cfg']['rules'][] = ["4303", "nexus", "[Ranked] Bataillon - #"];

$cfg['modules']['sub_channel']['cfg']['rules'][] = ["917", "", "Dofus - #"];
$cfg['modules']['sub_channel']['cfg']['rules'][] = ["1780", "", "Dota II - #"];
$cfg['modules']['sub_channel']['cfg']['rules'][] = ["918", "", "ArmA III - #"];
$cfg['modules']['sub_channel']['cfg']['rules'][] = ["1089", "", "Payday 2 - #"];
$cfg['modules']['sub_channel']['cfg']['rules'][] = ["916", "", "Battlefield - #"];
$cfg['modules']['sub_channel']['cfg']['rules'][] = ["919", "", "Garry's Mod - #"];
$cfg['modules']['sub_channel']['cfg']['rules'][] = ["1155", "", "The Division - #"];
$cfg['modules']['sub_channel']['cfg']['rules'][] = ["10904", "", "Dead by Daylight #"];

$cfg['modules']['sub_channel']['cfg']['rules'][] = ["5471", "", "Monster Hunter - "]; // Monster hunter

$cfg['modules']['sub_channel']['cfg']['rules'][] = ["821", "2016stream", "STREAMING  #"];
$cfg['modules']['sub_channel']['cfg']['rules'][] = ["826", "2016capture", "RECORDING  #"];


$cfg['modules']['sub_channel']['cfg']['rules'][] = ["1023", "", "Channel Privé #"];  // ◄ Channels Temporaires ►


$cfg['modules']['sub_channel']['cfg']['rules'][] = ["801", "choucroute23", "Le Staff • "]; // Le staff

$cfg['modules']['sub_channel']['cfg']['rules'][] = ["865", "flolasticot", "Team "]; // Les teams (events)



// Personalised subchannels module //
    // (example: if X join, channel name will be "Channel of X the great" but when Y does, it'll be "Channel of Y the tall")

// General settings
$cfg['modules']['sub_channel_personalised']['enabled'] = true;                                      // Turn ON/OFF the module
$cfg['modules']['sub_channel_personalised']['cfg']['channel_max_client'] = 1;                       // Max clients for subchannels (set -1 for unlimited)
$cfg['modules']['sub_channel_personalised']['cfg']['channel_maxfamilyclient_inherited'] = TRUE;     // Max family clients for subchannels (set -1 for unlimited)
$cfg['modules']['sub_channel_personalised']['cfg']['channel_delete_delay'] = 3;                     // Channel delay in seconds




// Channels rules definitions
    // = ["parent_channel_id", "channel_pass", "client_uid", "channel name"];

$cfg['modules']['sub_channel_personalised']['cfg']['rules'][] = ["801", "unchammeaubienbo", "NESmvjC6kS4VHyHRr///IG6A=", "Dynaste du peuple Caronastien"];
$cfg['modules']['sub_channel_personalised']['cfg']['rules'][] = ["801", "unchammeaubienbo", "Z/Wl6XJ4bL+oiwcwc///hjLc=", "Le vieux retraité"];
$cfg['modules']['sub_channel_personalised']['cfg']['rules'][] = ["801", "unchammeaubienbo", "yjEowezJpWhDHx7qK///aAQg=", "Booker Dewitt's Office"];
$cfg['modules']['sub_channel_personalised']['cfg']['rules'][] = ["801", "unchammeaubienbo", "jk7//0D3SxHeBMsJk///yGPg=", "Sarayah, un dromadaire flippant"];
$cfg['modules']['sub_channel_personalised']['cfg']['rules'][] = ["801", "unchammeaubienbo", "jHgRB8s5/5d1JNDBA///Vhog=", "Viking's Bar"];
$cfg['modules']['sub_channel_personalised']['cfg']['rules'][] = ["801", "unchammeaubienbo", "NjFFT6LoR4RdqBetL///tdnE=", "Nigiro, ca lui tombe dessus"];






?>
