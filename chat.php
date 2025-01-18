<?php
include_once $_SERVER['DOCUMENT_ROOT'].'../../main/www/classes/session.php';
ini_set("display_errors", 1);
ini_set("track_errors", 1);
ini_set("html_errors", 1);
error_reporting(E_ALL ^ E_DEPRECATED);
include_once $_SERVER['DOCUMENT_ROOT'].'classes/header.php';
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">

<head title="one piece,browsergame">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>OP-BG - Das One Piece Browsergame zur Serie</title>
  <meta name="Author" content="PuRe">
  <meta name="Page-topic" content="Browsergame, One Piece, Onlinespiel">
  <meta name="Keywords" content="OPBG,Das,Online,One Piece,Browsergame,opbg,das,online,one piece,browsergame,PuRe,umfrage,alpha,one piece,google,twitter,aniflix,anime">
  <meta name="Description" content="OPBG - Das One Piece Browsergame, OPBG ist ein kostenloses One Piece Browsergame, Du kannst trainieren, kämpfen, unterhalten, und stärker werden.">
  <meta name="Content-language" content="DE">
	<meta name="Page-type" content="HTML-Formular">
	<meta name="Robots" content="INDEX,FOLLOW">
	<meta name="Audience" content="Alle">
	<meta name="viewport" content="width=device-width, initial-scale=0.5">
	<meta property="og:title" content="OPBG Das One Piece Browsergame" />
	<meta property="og:type" content="website" />
	<meta property="og:url" content="<?php echo $serverUrl; ?>" />
	<meta property="og:image" content="<?php echo $serverUrl; ?>img/defaultBanner.png" />
	<meta property="og:description" content="One Piece Das One Piece Browsergame, OPBG ist ein kostenloses One Piece Browsergame, Du kannst trainieren, kämpfen, unterhalten, und stärker werden." />
	<meta property="og:site_name" content="OPBG - Das One Piece Browsergame" />
	<meta name="twitter:card" content="summary" />
	<meta name="twitter:site" content="<?php echo $serverUrl; ?>" />
	<meta name="twitter:title" content="OPBG - Das One Piece Browsergame" />
	<meta name="twitter:description" content="OPBG - Das One Piece Browsergame, OPBG ist ein kostenloses One Piece Browsergame, Du kannst trainieren, kämpfen, unterhalten, und stärker werden." />
	<meta name="twitter:image" content="<?php echo $serverUrl; ?>img/defaultBanner.png" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>OPBG
		- Das Online Browsergame
	</title>
	<link rel="stylesheet" href="css/main.css">
	<?php
	if ($player->IsLogged())
	{
	?>
		<link rel="stylesheet" href="css/designs/<?php echo $player->GetDesign(); ?>/main.css">
	<?php
	}
	else
	{
	?>
		<link rel="stylesheet" href="css/designs/default/main.css">
	<?php

	}
	?>
</head>
<script type="text/javascript" src="chat/chat.js?0015"></script>
<script type="text/javascript">
	var timerStart = Date.now();
	window.onload = function() {
		InitChat('');
	}
</script>

<body>
	<div style="text-align: center;">
		<div class="chatcontainer menuBG borderT">
			<?php include 'pages/chat.php'; ?>
		</div>
	</div>
</body>