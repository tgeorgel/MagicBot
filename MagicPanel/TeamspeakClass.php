<?php

require_once('../lib/TeamSpeak3/TeamSpeak3.php');
require_once('cfg/config.php');

class ts3 {

  private $query_user       = "serveradmin";
  private $query_pass       = "qNlHO0S6";
  private $query_nickname   = "NastyBot";
  private $server_address   = "127.0.0.1";
  private $query_port       = "10011";
  private $server_port      = "9987";

  private $srv              = NULL;


  function init()
  {
    return $srv;
  }


  public function connectedClientsCount()
  {
    global $srv;
    return $srv->clientCount();
  }

  public function srvMaxClients()
  {
    global $srv;
    return $srv['virtualserver_maxclients'];
  }

  public function escapeText($text) {
 		$text = str_replace("\t", '\t', $text);
		$text = str_replace("\v", '\v', $text);
		$text = str_replace("\r", '\r', $text);
		$text = str_replace("\n", '\n', $text);
		$text = str_replace("\f", '\f', $text);
		$text = str_replace(' ', '\s', $text);
		$text = str_replace('|', '\p', $text);
		$text = str_replace('/', '\/', $text);
		return $text;
	}

  public function unescapeText($text) {
    $escapedChars = array("\t", "\v", "\r", "\n", "\f", "\s", "\p", "\/");
 		$unEscapedChars = array('', '', '', '', '', ' ', '|', '/');
		$text = str_replace($escapedChars, $unEscapedChars, $text);
		return $text;
	}


}
?>
