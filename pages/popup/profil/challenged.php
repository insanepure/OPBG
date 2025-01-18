<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../classes/header.php';
$fightID = $player->GetChallengeFight();
$challengeFight = new Fight($database, $fightID, $player, $actionManager);
if(!$challengeFight->IsValid())
{
    ?>
    Du wurdest herausgefordert, der Kampf existiert jedoch nicht mehr.
    <?php
    exit();
}
if(!$player->IsLogged())
{
    echo 'Du bist nicht eingeloggt.';
    exit();
}

$teams = $challengeFight->GetTeams();
$target = $teams[0][0];
?>
<div style="height:225px;">
    <div class="spacer"></div>

    <div class="bplayer1">
        <div class="bplayer1name smallBG">
            <b><a href="?p=profil&id=<?php echo $target->GetID();?>"><?php echo $target->GetName(); ?></a></b>
            <img class="bplayer1image" src="<?php echo $target->GetImage(); ?>">
        </div>
    </div>
    <div class="bplayervs boxSchatten"></div>
    <div class="bplayer2">
        <div class="bplayer2name smallBG">
            <b><a href="?p=profil&id=<?php echo $player->GetID();?>"><?php echo $player->GetName(); ?></a></b>
            <img class="bplayer2image" src="<?php echo $player->GetImage(); ?>">
        </div>
    </div>

    <div class="bplayerkampf">
        <div class="spacer"></div>
        <div style="position:absolute; left:0px;">
            <form method="POST" action="?p=profil&a=acceptChallenge">
                <input type="submit" class="ja" value="Akzeptieren">
            </form>
        </div>
        <div style="position:absolute; right:0px;">
            <form method="POST" action="?p=profil&a=declineChallenge">
                <input type="submit" class="nein"  value="Ablehnen">
            </form>
        </div>
        <div style="position:absolute; right:0px;">
            <form method="POST" action="?p=profil&a=readChallenge">
                <input type="submit" class="nein"  value="Gelesen">
            </form>
        </div>
        <div class="spacer"></div>
        <?php
        switch($challengeFight->GetType())
        {
            case 0:
                echo 'Spaß';
                break;
            case 13:
                echo 'Elokampf';
                break;
        } ?>
    </div>
    <div class="spacer"></div>
    <div style="position:absolute; height:50px; top:230px; width:100%;">
        <center>
        </center>
    </div>
</div>