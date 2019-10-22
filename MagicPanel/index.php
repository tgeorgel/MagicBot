<?php

session_start();

$_SESSION['user_id'] = 123;

if (!isset($_SESSION['user_id']))
{
  header('Location: login.php');
  exit();
}

ini_set('display_errors', 'On');
error_reporting(E_ERROR | E_WARNING | E_PARSE);

require_once('TeamspeakClass.php');
require_once('../inc/functions.php');
require_once('cfg/config.php');
require_once('scripts.php');

$ts3      = new ts3();
$scripts  = new Scripts();

//$mysql  = new mysqlf();

?>

<!DOCTYPE html>
<html lang="fr-FR">
	<head>
		<title>MagicInterface</title>

		<meta charset="UTF-8">
		<meta http-equiv="content-type" content="text/html;charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<link href="style/style.css" rel="stylesheet" type="text/css">
    <link href="style/buttons.css" rel="stylesheet" type="text/css">
		<link href="style/navbar.css" rel="stylesheet" type="text/css">
    <link href="style/general.css" rel="stylesheet" type="text/css">

    <link href='https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,300italic,400italic,600italic,700italic' rel='stylesheet' type='text/css'>
	</head>

	<body>
		<header>
			<?php
				include_once("inc/navbar.php");
        //include_once("inc/header.php");
			?>
		</header>

    <div id="divider-50" class="set-white"></div>

    <?php
      if (isset($_REQUEST['p'])) include_once($scripts->checkIfPageExist($_REQUEST['p']));
      else include_once("pages/home.php");
    ?>

    <div id="divider-50" class="set-white"></div>

    <footer>
      <?php
        //include_once("inc/footer.php");
      ?>
    </footer>

    <!--<script src="http://code.jquery.com/jquery-2.2.0.min.js"></script>-->
    <script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>
	</body>
</html>
