<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
    include_once '../../../classes/header.php';

    if(!$player->IsLogged())
    {
        header('Location: ?p=news');
        exit();
    }
?>
<style>
    .popup-container > .spacer
    {
        height: 0;
    }
    .popup-container {
        width: 640px;
        height: 425px;
    }
</style>
<div style="width: 100%; height: 425px; background-image: url('/img/offtopic/impeldown.png');">
    <div style="position: relative; top: 21px; left: 117px; width: 355px; height: 225px; padding: 10px;">
        <?php
            if($player->GetRace() == 'Pirat')
            {
                ?>
                <b>Willkommen in Impel Down, Piratenpack!</b>
                <div class="spacer"></div>
                Du wurdest wohl im Kampf besiegt und hergebracht.<br/>
                Wie ich sehe, hast du dir einiges zu schulden kommen lassen.<br/>
                Dafür kannst du jetzt erst einmal deine Zeit in Level 1 absitzen!<br/>
                <div class="spacer"></div>
                Verhalte dich ruhig und sorg nicht für unnötigen Ärger!<br/>
                Denk ja nicht darüber nach, dich mit dem Gefängnisdirektor Magellan anzulegen oder zu fliehen!<br/>
                Es würde dir eh nicht gelingen!
                <?php
            }
            else
            {
                ?>
                <b>Willkommen in Impel Down, <span style="color: red;"><?php echo $player->GetName(); ?></span>!</b>
                <div class="spacer"></div>
                Du bist wohl hergekommen, um das berühmte Unterwassergefängnis zu sehen, was?<br/>
                Oder wurdest du fälschlicherweise für einen Piraten gehalten und nach einem Kampf hergebracht?<br/>
                Hier ist es relativ langweilig für dich, hast du nichts besseres zu tun?<br/>
                <div class="spacer"></div>
                Der Ausgang ist im übrigen dort drüben.<br/>
                Leg dich aber nicht mit dem Gefängnisdirektor Magellan an, der versteht keine Späße!<br/>
                <?php
            }
        ?>
    </div>
    <div style="position: relative; top: 33px; left: 117px; width: 365px; height: 140px;">
        Innerhalb von Impel Down hast du folgende Einschränkungen:
        <div class="spacer"></div>
        Du kannst keine Schätze suchen!<br/>
        Du erhältst von dem NPC hier weniger Berry!<br/>
        Bandenaktivitäten sind nicht möglich!<br/>
        Die Schatzsuche ist von hier aus nicht möglich!<br/>
    </div>
    <div style="position: relative; top: 50px; left: 117px; width: 365px; height: 140px;">
       <form method="post" action="?p=map&a=impeldownpopup">
        <button value="Gelesen!">Gelesen!</button>
       </form>
    </div>
</div>
