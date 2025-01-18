<?php
include_once $_SERVER['DOCUMENT_ROOT'].'../../main/www/classes/session.php';
    include_once '../../../classes/header.php';

    $now = new Datetime('now');
    $weekday = date("l");

    $min = '00:01';
    $max = '23:59';

    $canDoMirrorFight = true;
    $fresult = $database->Select('acc', 'fighters', 'acc != -1');
    if($fresult && $fresult->num_rows > 0)
    {
        while($frow = $fresult->fetch_assoc())
        {
            $fPlayer = new Player($database, $frow['acc']);
            if($fPlayer->GetArank() == 0)
            {
                $fFight = new Fight($database, $fPlayer->GetFight(), $fPlayer, $actionManager);
                if(!$fFight->IsStarted() && $fFight->GetType() == $fight->GetType() && (($player->GetClan() != $fPlayer->GetClan() && $player->GetClan() != 0) || $player->GetClan() == 0))
                {
                    $canDoMirrorFight = false;
                }
            }
        }
    }
    //<button onclick="OpenPopupPage('Spiegelkampf', 'fight/mirror.php');">test</button>
    $mirrorTime = 5;
    if($player->IsDonator())
        $mirrorTime = 3;
    if((date('H:i') < $min || date('H:i') >= $max) && $player->GetArank() < 3 || abs(DateTime::createFromFormat('Y-m-d H:i:s', $fight->GetTime())->getTimestamp() - $now->getTimestamp()) / 60  < $mirrorTime && $player->GetArank() < 3 || !$canDoMirrorFight)
    {
        $player->SetMirror(0);
        ?>
            <script>
                ClosePopup();
            </script>
        <?php
    }
?>

<style>
    .popup-container {
        width: 683px;
    }

    .popup-container > .catGradient {
        display: none;
    }

    .smallBG {
        background-color: unset;
    }

    .boxSchatten {
        box-shadow: unset;
    }

    .footer {
        display: none;
    }


</style>
<div style="width: 100%; height: 500px; font-weight: bold; background-repeat: no-repeat; background-image: url('img/marketing/mirrorfight.png');">
    <div style="position: relative; top: 172px; left: 9px; width: 130px; height: 130px; border-radius: 50%; opacity: 0.5; background-repeat: no-repeat; background-size: 100% 100%; background-image: url('<?php echo $player->GetImage(); ?>');"></div>
    <div style="position: relative; top: -130px; width: 100%; height: 500px; background-repeat: no-repeat; background-image: url('img/marketing/mirror.png');"></div>
    <div style="position: relative; top: -278px; height: 80px; width: 80%;">
        Es hat sich seit mehr als <?= $mirrorTime ?> Minuten kein Gegner für dich finden lassen, möchtest du stattdessen gegen einen Spiegelgegner kämpfen?
    </div>
    <a class="ja" href="?p=fight&a=mirror&t=1" onmouseover="ja.style.color = '#305925'; ja.style.fontWeight = 'bold';" onmouseleave="ja.style.color = 'white';">
        <div id="ja" style="float:left; position: relative; top: -315px; left:180px; height: 40px; width: 125px; padding-top: 8px; background-repeat: no-repeat; background-image: url('img/marketing/button.png');">
            Ja
        </div>
    </a>
    <a href="?p=fight&a=mirror&t=0" onmouseover="nein.style.color = '#6d0000'; nein.style.fontWeight = 'bold';" onmouseleave="nein.style.color = 'white';">
        <div id="nein" style="float: right; position: relative; top: -315px; left:-180px; height: 40px; width: 125px; padding-top: 8px; background-repeat: no-repeat; background-image: url('img/marketing/button.png');">
            Nein
        </div>
    </a>
</div>