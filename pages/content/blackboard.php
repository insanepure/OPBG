<br />
<center><a class="button" style="cursor: pointer;" onclick="OpenPopupPage('Blackboard Eintrag','blackboard/createblackboardmessage.php?')"><b>Eintrag erstellen</b></a><br /></center>
<br />
<hr>
<br />
<table>
    <tr>
        <td style="border: white solid 1px;" width="25%"><b>Suchender / Anbietender</b></td>
        <td style="border: white solid 1px;" width="50%"><b>Ich suche / Ich biete</b></td>
        <td style="border: white solid 1px;" width="25%"><b>Aktionen</b></td>
    </tr>
    <?php
    $BlackBoardCheck = $database->Select('*', 'accounts', 'blackboard != "nichts"');
    if($BlackBoardCheck)
    {
        while($blackboard = $BlackBoardCheck->fetch_assoc())
        {
            $blackboardplayer = new Player($database, $blackboard['id']);
            ?>
            <tr>
            <td style="border: white solid 0.1px;"><a href="?p=profil&id=<?= $blackboardplayer->GetID(); ?>"><?= $blackboardplayer->GetName(); ?></a></td>
            <td style="border: white solid 0.1px;"><?= $blackboardplayer->GetBlackBoard(); ?></td>
            <td style="border: white solid 0.1px;"><a class="button" style="cursor: pointer;" onclick="OpenPopupPage('Private Nachricht','profil/pmpopup.php?name=<?php echo $blackboardplayer->GetName(); ?>')"><b>Kontaktieren</b></a><br />
            <?php
            if($player->GetID() == $blackboardplayer->GetID())
            {
                ?>
                <a  style="cursor: pointer;" onclick="OpenPopupPage('Eintrag Ändern','blackboard/editblackboardmassage.php?id=<?php echo $blackboardplayer->GetID(); ?>')"><b>Ändern</b></a><br />
                <a href="?p=blackboard&blackboardmessage=delete&id=<?= $blackboardplayer->GetID(); ?>"><b>Löschen</b></a>
                <?php
            }
            if($player->GetArank() >= 2)
            {
                ?>
                <a href="?p=blackboard&blackboardmessage=adelete&id=<?= $blackboardplayer->GetID(); ?>"><b>Löschen</b></a>
                <?php
            }
        }
        ?>
        </tr>
        <?php
    }
    ?>
</table>
<br />
<fieldset>
    <legend>Information</legend>
    1. Du suchst etwas oder bietest etwas an, dann hast du hier die Möglichkeit es anderen mitzuteilen!<br />
    2. Es ist nur 1 Eintrag pro Spieler gestattet! <br />
    3. Beschreibe genau was du suchst oder anbietest und ebenfalls was du dafür möchtest!
</fieldset>
