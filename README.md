---- How to install ----

Your server needs PHP 5.2+ to be installed
You will also need screen to keep the bot alive
Copy all files to your server, in any directory



----- How to config ----

Config file sample is in 'cfg' folder
You have to create a config.php file, based on the sample (config-default.php)
All the vars have to set, you MUST set off the unused modules


----- How to start -----

Get in MagicBot's Directory via command line
Simply execute `php TeamspeakBot.php` to start the bot in the current screen session



------- FUNCTIONS ------

At this time, here are the working functions:

 * Sub-channels : create automatic temporary sub-channels on join and move client into it
 * Sub-channels Personalised : create automatic temporary sub-channels on join and move client into it, can set a channel Name for the particular client
 * !accept : will set server group(s) on client accept (Only working in Channel Chat/Server chat for the moment)
 * Welcome PM : will send a PM or Poke to a user connecting. You can set server groups that will be targeted or untargeted
 * Server Group assign on Channel join


Working commands :

 * !pmme : will send a PM to the invoker

**CONFIG**

Config file is cfg/cfg.php
Yes a config file in php, i'm lazy.
Everything you need to know is inside this file.


**START**

Better have screen on your server so the bot run in a window.

Start bot: php ./TeamspeakBot.php
With screen: screen -dmS MagicBot php -f TeamspeakBot.php

Find a guide on how to use screen here : http://debian-facile.org/doc:autres:screen
g