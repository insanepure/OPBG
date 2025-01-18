<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
    include_once '../../../classes/header.php';

    if($player->GetAction() == 1)
    {
        $action = $actionManager->GetAction($player->GetAction());

        $actionStart = new DateTime($player->GetActionStart());
        $backupTime = new DateTime("2022-07-08 00:01:00");
        if($actionStart > $backupTime)
        {
            $actionTimes = $player->GetActionTime();
            $leftMinutes = ceil($player->GetActionCountdown() / 60);
            $elapsedMinutes = $actionTimes - $leftMinutes;
            $actionMinutes = $action->GetMinutes();
            $times = floor($elapsedMinutes / $actionMinutes);
            $statpoints = $times * $action->GetStats();
            if ($statpoints > 0)
            {
                ?>
                Möchtest du die Aktion aktualisieren?<br />
                Du erhältst <?php echo number_format($statpoints,'0', '', '.'); ?> Statpunkte.
                <div class="spacer"></div>
                <form method="post" action="?p=profil">
                    <input type="hidden" name="a" value="refreshAction">
                    <input type="submit" value="Aktualisieren">
                </form>
                <div class="spacer"></div>
                <?php
            }
            else
            {
                echo "Die Aktion kann derzeit nicht aktualisiert werden.";
            }
        }
        else
        {
            echo "Die Aktion kann derzeit nicht aktualisiert werden.";
        }
    }
    else if($player->GetAction() == 9 && $player->IsDonator())
    {
        $action = $actionManager->GetAction($player->GetAction());
        $actionStart = new DateTime($player->GetActionStart());
        $backupTime = new DateTime("2022-07-08 00:01:00");
        if($actionStart > $backupTime)
        {
            $actionTimes = $player->GetActionTime();
            $leftMinutes = ceil($player->GetActionCountdown() / 60);
            $elapsedMinutes = $actionTimes - $leftMinutes;
            $actionMinutes = $action->GetMinutes();
            $times = floor($elapsedMinutes / $actionMinutes);
            $statpoints = $times * $action->GetStats();
            if ($statpoints > 0)
            {
                ?>
                Möchtest du die Aktion aktualisieren?<br />
                Du erhältst <?php echo number_format($statpoints,'0', '', '.'); ?> Statpunkte.
                <div class="spacer"></div>
                <form method="post" action="?p=profil">
                    <input type="hidden" name="a" value="refreshAction">
                    <input type="submit" value="Aktualisieren">
                </form>
                <div class="spacer"></div>
                <?php
            }
            else
            {
                echo "Die Aktion kann derzeit nicht aktualisiert werden.";
            }
        }
        else
        {
            echo "Die Aktion kann derzeit nicht aktualisiert werden.";
        }
    }
?>