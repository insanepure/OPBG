<?php
include_once $_SERVER['DOCUMENT_ROOT'].'../../main/www/classes/session.php';
ini_set("display_errors", 1);
ini_set("track_errors", 1);
ini_set("log_errors", 0);
ini_set("html_errors", 1);
error_reporting(E_ALL ^ E_DEPRECATED);
require_once "recaptchalib.php";
include_once 'classes/header.php';
include_once 'pages/main.php';
include_once 'pages/dailyupdate.php';

if (isset($_GET['p']) && file_exists('pages/head/' . $_GET['p'] . '.php'))
{
    include_once 'pages/head/' . $_GET['p'] . '.php';
}
else if (!isset($_GET['p']) || isset($_GET['p']) && !file_exists('pages/content/' . $_GET['p'] . '.php'))
{
    include_once 'pages/head/news.php';
}

if ($account->IsLogged() && $player->IsLogged() && !$player->IsAdminLogged())
{
    LoginTracker::TrackUser($accountDB, $account->Get('id'), $player->GetName(), 'opbg', $account->Get('password'), $account->Get('email'), session_id(), $account->GetIP(), $account->GetRealIP(), isset($_COOKIE['userTracking']));
}

//var_dump($_SESSION);
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;
?>
    <!DOCTYPE html>
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">

    <head>


        <?php
        if($account->IsLogged())
        {
            ?>
            <script>
                var update = true;
                var BreakException = {};
                if(localStorage.getItem('chars') === null || localStorage.getItem('chars') === '')
                {
                    var char = Array();
                    char.push('<?php echo $player->GetName(); ?>');
                    localStorage.chars = JSON.stringify(char);
                }
                else
                {
                    var chars = JSON.parse(localStorage.chars);
                    if(Array.from(chars).length >= 1)
                    {
                        chars.forEach(function(item, index) {
                            if(item.toString() === '<?php echo $player->GetName(); ?>')
                            {
                                update = false;
                            }
                        });
                    }
                    if(update)
                    {

                        chars.push('<?php echo $player->GetName(); ?>');
                        localStorage.chars = JSON.stringify(chars);
                    }
                }
            </script>
            <?php
        }
        ?>
        <link rel="shortcut icon" href="img/favicon.ico">
        <link rel="icon" type="image/png" sizes="32x32" href="img/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="img/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="img/favicon-16x16.png">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="Author" content="PuRe">
        <meta name="Page-topic" content="Browsergame, One Piece, Onlinespiel">
        <meta name="Keywords" content="OPBG,Das,Online,One Piece,Browsergame,opbg,das,online,one piece,browsergame,PuRe,umfrage,alpha,one piece,google,twitter,aniflix,anime">
        <meta name="Description" content="OPBG - Das One Piece Browsergame, OPBG ist ein kostenloses One Piece Browsergame, Du kannst trainieren, kämpfen, unterhalten, und stärker werden.">
        <meta name="Content-language" content="DE">
        <meta name="Page-type" content="HTML-Formular">
        <meta name="Robots" content="INDEX,FOLLOW">
        <meta name="Audience" content="Alle">
        <meta name="viewport" content="width=device-width, initial-scale=0.41">
        <meta property="og:title" content="OPBG - Das One Piece Browsergame" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="" />
        <meta property="og:image" content="img/defaultBanner.png" />
        <meta property="og:description" content="OPBG - Das One Piece Browsergame, OPBG ist ein kostenloses One Piece Browsergame, Du kannst trainieren, kämpfen, unterhalten, und stärker werden." />
        <meta property="og:site_name" content="OPBG - Das One Piece Browsergame" />
        <meta name="twitter:card" content="summary" />
        <meta name="twitter:site" content="" />
        <meta name="twitter:title" content="OPBG - Das One Piece Browsergame" />
        <meta name="twitter:description" content="OPBG - Das One Piece Browsergame, OPBG ist ein kostenloses One Piece Browsergame, Du kannst trainieren, kämpfen, unterhalten, und stärker werden." />
        <meta name="twitter:image" content="img/defaultBanner.png" />
        <title>
            OPBG
            <?php
            if (isset($_GET['p']) && file_exists('pages/content/' . $_GET['p'] . '.php'))
            {
                ?>
                - <?php
                if (isset($title))
                    echo $title;
                else
                    echo mb_convert_case($_GET['p'], MB_CASE_TITLE, 'UTF-8');
                ?>
                <?php
            }
            else
            {
                ?>
                - Das One Piece Browsergame
                <?php
            }
            ?>
        </title>
        <link rel="stylesheet" href="css/main.css?050">
        <?php
        if (false)
        {
            ?>
            <link rel="stylesheet" href="css/designs/<?php echo $player->GetDesign(); ?>/main.css?1365">
            <style>
                header {
                    background-image: url("img/header/<?php echo $player->GetHeader(); ?>.png");
                    background-size: 100% 100%;
                }
                @media ( min-width: 1200px ) {
                    body {
                        background-image: url("img/backgrounds/<?php echo $player->GetBackground(); ?>.jpg");
                        background-size: 100% 100%;
                    }
                }
            </style>
            <?php
        }
        else
        {
            ?>
            <link rel="stylesheet" href="css/designs/Enel/main.css?012">
            <style>
                header {
                    background-image: url("img/header/default.png?7");
                    background-size: 100% 100%;
                }
            </style>
            <?php
        }

        if (isset($_GET['p']) && file_exists('css/pages/' . $_GET['p'] . '.css'))
        {
            ?>
            <link rel="stylesheet" href="css/pages/<?php echo $_GET['p']; ?>.css?018">
            <?php
        }
        ?>

        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    </head>
    <script type="text/javascript" src="js/main.js?00561"></script>
    <script type="text/javascript" src="js/timer.js?07"></script>
    <?php
    if ($player->GetChatActive())
    {
        echo '<script type="text/javascript" src="chat/chat.js?00015"></script>';
    }
    if ($player->GetClan() != '')
    {
        echo '<script type="text/javascript" src="js/clan.js?019"></script>';
    }
    ?>

    <body>
    <center>
        <div id="popup" class="popup">
            <div class="popup-container smallBG boxSchatten">
                <div class="catGradient borderT borderB">
                    <div class="schatten">
                        <span id="popup-header">Herausforderung</span>
                        <div id="popup-close" class="popup-close"><a onclick="ClosePopup()">X</a></div>
                    </div>
                </div>
                <div class="spacer"></div>
                <div id="popup-content">
                    Test
                </div>
                <div class="spacer"></div>
                <div class="footer borderT" style="width:100%;">
                    <Button onclick="ClosePopup(<?php echo $player->GetID(); ?>)">Schließen</Button>
                </div>
            </div>
        </div>
        <div id="popup2" class="popup" style="z-index: 5000000000;">
            <div class="popup-container2 smallBG boxSchatten">
                <div class="catGradient borderT borderB">
                    <div class="schatten">
                        <span id="popup-header2">Herausforderung</span>
                        <div id="popup-close2" class="popup-close"><a onclick="ClosePopup2()">X</a></div>
                    </div>
                </div>
                <div class="spacer"></div>
                <div id="popup-content2">
                    Test
                </div>
                <div class="spacer"></div>
                <div class="footer borderT" style="width:100%;">
                    <Button onclick="ClosePopup2()">Schließen</Button>
                </div>
            </div>
        </div>
        <div class="maincontainer borderBigL borderBigR" style="position:relative">
            <?php
            if ($player->IsLogged() && $eventItemsData)
            {
                foreach ($eventItemsData as $eventItem) {
                    if (!$eventItems->HasItem($player->GetID(), $eventItem->GetID()) && (time() >= strtotime($eventItem->GetStartTime()) && time() < strtotime($eventItem->GetEndTime())))
                    {
                        ?>
                        <div style="z-index:1000000; position:absolute; top:<?php echo $eventItem->GetY(); ?>px; left:<?php echo $eventItem->GetX(); ?>px;">
                            <?php
                            if($eventItem->GetActive() && time() > strtotime($eventItem->GetStartTime()))
                            {
                            ?>
                            <a href="?<?php echo $eventItemURL . $eventItemPickURL . '&eventid=' . $eventItem->GetID(); ?>">
                                <?php
                                }
                                ?>
                                <img src="<?php echo $eventItem->GetImage(); ?>" style="width: 75px; height: 100px;">
                                <?php
                                if($eventItem->GetActive())
                                {
                                ?>
                            </a>
                        <?php
                        }
                        ?>
                        </div>
                        <?php
                    }
                }
            }
            ?>
            <header>
                <div class="header" role="banner" itemscope itemtype="" style="overflow: hidden;">
                    <?php
                    $wartung = 0;
                    if ($wartung == 1)
                    {
                        if ($player->IsLogged() && $player->GetCanJoin() == '0')
                        {
                            //$player->Logout();
                        }
                        ?>
                        <div class="logo2"></div>
                        <?php
                    }
                    ?>
                </div>
            </header>
            <nav>
                <div class="navigationbar catGradient borderB borderT">
                    <ul class="navigationbarlist">
                        <?php
                        if($player->GetArank() < 2)
                        {
                            ?>
                            <li class = "navigationbarbutton borderR"><a class="navigationbarbuttontext" href="index.php">News</a></li>
                            <li class = "navigationbarbutton borderR"><a class="navigationbarbuttontext" id="no-link" target="_blank" href="https://animebg.de/" rel="nofollow">BGs</a></li>
                            <li class = "navigationbarbutton borderR"><a class="navigationbarbuttontext" id="no-link" target="_blank" href="https://discord.gg/Nw9f8cJawv" rel="nofollow">Discord</a></li>
                            <li class = "navigationbarbutton borderR"><a class="navigationbarbuttontext" id="no-link" href="?p=info" >Infos</a></li>
                            <li class = "navigationbarbutton borderR"><a class="navigationbarbuttontext" id="no-link" href="?p=partner" >Partner</a></li>
                            <?php
                            if ($account->IsLogged() && $player->IsLogged())
                            {
                                ?>
                                <li class = "navigationbarbutton borderR"><a class="navigationbarbuttontext" id="no-link" href="?p=ticketsystem" ><?php if($player->TicketAnswer() == 1){echo "<font color='red'>Ticketsystem</font>"; }else{echo "Ticketsystem";} ?></a></li>
                                <?php
                            }

                            ?>
                    <li class = "navigationbarbutton borderR"><a class="navigationbarbuttontext" id="no-link" href="?p=clans">&nbsp;&nbsp;
                            <?php $clanCount = $gameData->GetClans(); if($clanCount == 1) echo $clanCount.' Clan'; else echo $clanCount.' Banden'; ?>&nbsp;&nbsp;</a></li>
                    <li class = "navigationbarbutton borderR"><a class="navigationbarbuttontext" id="no-link" href="?p=online">&nbsp;&nbsp;<?php echo $gameData->GetOnline(); ?>&nbsp;Online&nbsp;&nbsp;</a></li>
                            <?php
                            if ($account->IsLogged())
                            {
                                ?>
                                <?php
                                if (!$player->IsLogged())
                                {
                                    ?>
                                      <li class = "navigationbarbutton borderL" style="float:right"><a class="navigationbarbuttontext" href="?p=login&a=userlogout">Logout</a></li>
                                      <li class = "navigationbarbutton borderL" style="float:right"><a class="navigationbarbuttontext" title="dragonball,browsergame" href="?p=charalogin">Charaktere</a></li>
                                    <?php
                                }
                                else
                                {
                                    ?>
                            <li class = "navigationbarbutton borderL" style="float:right"><a class="navigationbarbuttontext" href="?p=login&a=charlogout">Logout</a></li>
                                    <?php
                                }
                            }
                            else
                            {
                                ?>
                        <li class = "navigationbarbutton borderL" style="float:right"><a class="navigationbarbuttontext" title="dragonball,browsergame" href="?p=login">Login</a></li>
                        <li class = "navigationbarbutton borderL" style="float:right"><a class="navigationbarbuttontext" title="dragonball,browsergame" href="?p=register">Registrieren</a></li>
                                <?php
                            }
                        }
                        else
                        {
                            ?>
                            <li class="navigationbarstyle borderR">
                                <a class="navigationbarbuttontext" href="?p=news">
                                    News
                                </a>
                            </li>
                            <li class = "navigationbarbutton borderR"><a class="navigationbarbuttontext" id="no-link" target="_blank" href="https://animebg.de/" rel="nofollow">BGs</a></li>
                            <li class = "navigationbarbutton borderR"><a class="navigationbarbuttontext" id="no-link" target="_blank" href="https://discord.gg/Nw9f8cJawv" rel="nofollow">Discord</a></li>
                            <li class="navigationbarstyle borderR">
                                <a class="navigationbarbuttontext" href="?p=info">
                                    Infos
                                </a>
                            </li>
                            <li class="navigationbarstyle borderR">
                                <a class="navigationbarbuttontext" href="?p=changelog">
                                    <?php
                                    $changelog = new Generallist($database, 'changelog', '*', '', 'time', 1, 'DESC');
                                    $lastchangelog = $changelog->GetEntry(0);
                                    $lastchangelogtime = $lastchangelog['time'];
                                    if($player->IsLogged() && strtotime($player->GetLastTimePatchnotesVisited()) < strtotime($lastchangelogtime))
                                    {
                                        echo '<span style="color: red;">';
                                    }
                                    ?>
                                    Patchnotes
                                    <?php
                                    if($player->IsLogged() && strtotime($player->GetLastTimePatchnotesVisited()) < strtotime($lastchangelogtime))
                                    {
                                        echo '</span>';
                                    }
                                    ?>
                                </a>
                            </li>
                            <li class="navigationbarstyle borderR">
                                <a class="navigationbarbuttontext" href="?p=partner">
                                    Partner
                                </a>
                            </li>
                            <?php
                            if ($account->IsLogged() && $player->IsLogged())
                            {
                              /*
                                ?>
                                <li class="navigationbarstyle borderR">
                                    <a class="navigationbarbuttontext" href="?p=ticketsystem">
                                        Ticketsystem
                                    </a>
                                </li>
                                <?php
                                */
                            }
                            ?>


                            <li class="navigationbarstyle borderR">
                                <a class="navigationbarbuttontext" href="?p=clans">
                                    <?php $clanCount = $gameData->GetClans();
                                    if ($clanCount == 1) echo $clanCount . ' Bande';
                                    else echo number_format($clanCount, '0', '', '.') . ' Banden'; ?>
                                </a>
                            </li>
                            <li class="navigationbarstyle borderR">
                                <a class="navigationbarbuttontext" href="?p=online">
                                    <?php echo number_format($gameData->GetOnline(), '0', '', '.'); ?> Online
                                </a>
                            </li>
                            <?php
                            if ($account->IsLogged())
                            {
                                ?>
                                <?php
                                if (!$player->IsLogged())
                                {
                                    ?>
                                    <li class="navigationbarstyle borderL" style="float:right">
                                        <div class="navigationbarstyletext" onmousedown="location.replace('?p=login&a=userlogout')">Logout</div>
                                    </li>
                                    <li class="navigationbarstyle borderL" style="float:right">
                                        <div class="navigationbarstyletext" onmousedown="location.replace('?p=charalogin')">Charaktere</div>
                                    </li>
                                    <?php
                                }
                                else
                                {
                                    ?>
                                    <li class="navigationbarstyle borderL" style="float:right">
                                        <a class="navigationbarbuttontext" href="?p=login&a=charlogout">
                                            Logout
                                        </a>
                                    </li>
                                    <?php
                                }
                            }
                            else
                            {
                                ?>
                                <li class="navigationbarstyle borderL" style="float:right">
                                    <div class="navigationbarstyletext" onmousedown="location.replace('?p=login')">Login</div>
                                </li>
                                <li class="navigationbarstyle borderL" style="float:right">
                                    <div class="navigationbarstyletext" onmousedown="location.replace('?p=register')">Registrieren</div>
                                </li>
                                <?php
                            }
                        }
                        ?>
                    </ul>
                </div>
            </nav>
            <div class="contentcontainer">
                <div class="leftbar sidebar menuBG">
                    <?php
                    if (isset($_GET['p']) && file_exists('pages/left/' . $_GET['p'] . '.php'))
                    {
                        include 'pages/left/' . $_GET['p'] . '.php';
                    }
                    else
                    {
                        include 'pages/left/main.php';
                    }
                    ?>
                </div>
                <div class="content menuBG borderL borderR">
                    <!--<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>-->
                    <!-- AboveContent -->
                    <!--<ins class="adsbygoogle"
                         style="display:inline-block;width:667px;height:90px"
                         data-ad-client="ca-pub-7145796878009968"
                         data-ad-slot="2481574054"></ins>
                    <script>
                         (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>-->
                    <!-- ADC Rota 202960 PRE BK 468x60 -->
                    <!--<script type="text/javascript" language="Javascript" src="https://bk.adcocktail.com/pre_bk_rota.php?format=468x60&uid=89196&wsid=202960"></script> -->
                    <!-- ADC Rota 202960 PRE BK 468x60 -->
                    <?php

                    if (isset($_GET['p']) && file_exists('pages/content/' . $_GET['p'] . '.php'))
                    {
                        if (isBetween("23:55", "00:00", date("H:i")) && $player->GetArank() < 2)
                        {
                            include 'pages/content/dailyupdate.php';
                        }
                        else {
                            include 'pages/content/' . $_GET['p'] . '.php';
                        }
                    }
                    else
                    {
                        if (isBetween("23:55", "00:00", date("H:i")) && $player->GetArank() < 2)
                        {
                            include 'pages/content/dailyupdate.php';
                        }
                        include 'pages/content/news.php';
                    }
                    ?>
                    <br />
                </div>
                <div class="rightbar sidebar menuBG">
                    <?php
                    if (isset($_GET['p']) && file_exists('pages/right/' . $_GET['p'] . '.php'))
                    {
                        include 'pages/right/' . $_GET['p'] . '.php';
                    }
                    else
                    {
                        include 'pages/right/main.php';
                    }
                    ?>
                </div>

            </div>
            <?php
            if ($player->IsLogged())
            {
                if ($player->GetChatActive() && (!isset($_GET['p']) || $_GET['p'] != 'infight' || $player->GetArank() >= 2 || $player->GetFight() == 0) && $player->GetChatban() == 0)
                {

                    echo '<div class="chatcontainer menuBG borderT">';
                }
                else
                {
                    echo '<div class="chatcontainer menuBG borderT" style="display: none; visibility: hidden; min-height: 0; height: 0;">';
                }
                include 'pages/chat.php';
                echo '</div>';
            }
            ?>
            <footer class="footer borderT" style="font-size:14px;">
                <?php
                $time = microtime();
                $time = explode(' ', $time);
                $time = $time[1] + $time[0];
                $finish = $time;
                $total_time = round(($finish - $start), 3);
                echo '<span id="loadingtime">' . $total_time . '</span> Sekunden. ';
                if ($player->IsLogged() && $player->GetArank() == 3)
                {
                    $selects = $database->GetSelects();
                    if ($selects != 0)
                    {
                        echo number_format($selects, '0', '', '.') . ' Selects ';
                    }
                    $updates = $database->GetUpdates();
                    if ($updates != 0)
                    {
                        echo number_format($updates, '0', '', '.') . ' Updates ';
                    }
                    $inserts = $database->GetInserts();
                    if ($inserts != 0)
                    {
                        echo number_format($inserts, '0', '', '.') . ' Inserts ';
                    }
                    $deletes = $database->GetDeletes();
                    if ($deletes != 0)
                    {
                        echo number_format($deletes, '0', '', '.') . ' Deletes ';
                    }
                }
                ?>
                Alle Urheberrechte zu One Piece liegen bei Shukan Shonen Jump, Toei Animation, Asatsu DK, TAP, Eiichiro Oda und Shueisha.
            </footer>
        </div>
    </center>
    <script type="text/javascript">
        const timerStart = Date.now();
    </script>
    <?php
    $googleChara = 595;
    if ($player->GetID() != $googleChara)
    {
        ?>
        <script>
            LoadPopup();
        </script>
        <?php
    }
    ?>

    <?php
    if($player->GetFight() != 0)
    {
        $startedFight = new Fight($database, $player->GetFight(), $player, new ActionManager($database));
    }

    if ($player->GetChatActive() && $player->GetFight() == 0 || $player->GetChatActive() && !$startedFight->IsStarted() || $player->GetArank() >= 2)
    {
        ?>
        <script>
            InitChat('');
        </script>
        <?php
    }
    if ($player->GetID() != $googleChara)
    {
        if($player->IsLogged() && $player->GetReadRules() == 0)
        {
            if(!isset($_GET['p']) || $_GET['p'] != 'regeln')
            {
                ?>
                <script>
                    OpenPopupPage("Regelwerk Akzeptieren", "profil/rules.php");
                </script>
            <?php
            }
        }
        else if (isset($message))
        {
        ?>
            <script>
                OpenPopupMessage('System', '<?php echo $message; ?>');
            </script>
        <?php
        }
        else if ($player->GetFriendRequests() != '')
        {
        ?>
            <script>
                OpenPopupPage('Freundschaftsanfrage', 'profil/friendrequest.php');
            </script>
        <?php
        }
        else if ($player->GetFight() == 0 && $player->GetStatsPopup())
        {
        ?>
            <script>
                OpenPopupPage('Skillpunkte Verteilen', 'skill/edit.php');
            </script>
        <?php
        }
        else if ($player->GetFight() == 0 && $player->GetChallengeFight() != 0)
        {
        ?>
            <script>
                OpenPopupPage('Herausforderung', 'profil/challenged.php');
            </script>
        <?php
        }
        else if ($player->GetMirror() == 1 && isset($fight) && !$fight->IsStarted())
        {
        ?>
            <script>
                OpenPopupPage('Spiegelkampf', 'fight/mirror.php');
            </script>
        <?php
        }
        else if ($player->GetClanWarPopup() == 1 && $player->GetClan() != 0 && $player->GetFight() == 0 && $player->GetChallengedPopUp() == 0 && $clan->GetChallengeFight() != 0 && time() < strtotime($clan->GetChallengeTime()))
        {
        ?>
            <script>
                OpenPopupPage('Herausforderung', 'clan/challenged.php');
            </script>
        <?php
        }
        else if ($player->GetFight() == 0 && $player->GetEventInvite() != 0)
        {
        ?>
            <script>
                OpenPopupPage('Event Einladung', 'profil/eventinvite.php');
            </script>
        <?php
        }
        else if ($player->GetGroupInvite() != 0)
        {
        ?>
            <script>
                OpenPopupPage('Gruppeneinladung', 'profil/groupinvite.php');
            </script>
        <?php
        }
        else if ($player->GetNPCWonItems() != '')
        {
        ?>
            <script>
                OpenPopupPage('Gewinn', 'profil/wonitem.php');
            </script>
        <?php
        }
        else if ($player->GetImpelDownPopUp() == 1 && $player->GetPlanet() == 2)
        {
        ?>
            <script>
                OpenPopupPage('Impel Down', 'map/impeldown.php');
            </script>
        <?php
        }
        else if ($player->GetCapchaCount() >= 20)
        {
        ?>
            <script>
                OpenPopupPage('Captcha Code Kolosseum', 'kolocapcha/kolocapcha.php');
            </script>
            <?php
        }
        else if ($account->IsLogged() && $account->Get('email') == "" && $account->Get('code') == "")
        {
            ?>
            <script>
                OpenPopupPage('Email Adresse Eintragen', 'profil/mail.php', 'id=<?= $account->Get('id') ?>');
            </script>
            <?php
        }
    }
    ?>
    <script>
        document.getElementById('loadingtime').innerHTML = String((Date.now() - timerStart) / 1000);
    </script>
    </body>
    </html>

<?php
function isBetween($from, $till, $input): bool
{
    $f = DateTime::createFromFormat('!H:i', $from);
    $t = DateTime::createFromFormat('!H:i', $till);
    $i = DateTime::createFromFormat('!H:i', $input);
    if ($f > $t) $t->modify('+1 day');
    return ($f <= $i && $i <= $t) || ($f <= $i->modify('+1 day') && $i <= $t);
}
?>