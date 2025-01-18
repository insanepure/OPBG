<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
    include_once '../../../classes/header.php';
    include_once '../../../classes/places/place.php';
    if($player->GetClan() == 0 || $clan->GetChallengeFight() == 0)
    {
        header('Location: ?p=news');
        exit();
    }
    $challengeFight = new Fight($database, $clan->GetChallengeFight(), $player, $actionManager);
    if(!$challengeFight)
    {
        header('Location: ?p=news');
        exit();
    }
    $place = new Place($database, $challengeFight->GetPlace(), $actionManager);
    $planet = new Planet($database, $challengeFight->GetPlanet());

    if($place->GetTerritorium() != $clan->GetID())
    {
        header('Location: ?p=news');
        exit();
    }

    $gegner = new Player($database, $challengeFight->GetTeams()[0][0]->GetAcc());
    $gegnerBande = new Clan($database, $gegner->GetClan());
    ?>
    <div style="height: 250px;">
        <img src="img/offtopic/bandenchallenge.png" style="width: 100%; height: 250px;"/>
        <div style="position: relative; top: -240px; left: -196px;">
            <img src="<?php if($clan->GetImage() == '') echo "img/clannoimage.png"; else echo $clan->GetImage(); ?>" style="width: 175px; height: 130px;"/>
        </div>
        <div style="position: relative; top: -326px; left: 196px;">
            <img src="<?php if($gegnerBande->GetImage() == '') echo "img/clannoimage.png"; else echo $gegnerBande->GetImage(); ?>" style="width: 175px; height: 130px;"/>
        </div>
        <img src="img/offtopic/bandenchallangeoverlay.png" style="position:relative; top: -525px; width: 100%; height: 250px;"/>
        <div style="display:flex; justify-content: center; align-items: center; position: relative; left: -197px; top: -636px; width: 200px; height: 53px;">
            <a href="?p=clan&id=<?php echo $clan->GetID(); ?>">
                <span style="font-family: 'Playfair Display SC', serif; font-weight:bold; font-size:24px; color:#5e4c46; text-shadow: 0.5px  0.5px 0.5px black,0.5px -0.5px 0.5px black,-0.5px  0.5px 0.5px black,-0.5px -0.5px 0.5px black;"><b><?php echo $clan->GetName(); ?></b></span>
            </a>
        </div>
        <div style="display:flex; justify-content: center; align-items: center; position: relative; left: 197px; top: -636px; width: 200px; height: 53px;">
            <a href="?p=clan&id=<?php echo $gegnerBande->GetID(); ?>">
                <span style="font-family: 'Playfair Display SC', serif; font-weight:bold; font-size:24px; color:#5e4c46; text-shadow: 0.5px  0.5px 0.5px black,0.5px -0.5px 0.5px black,-0.5px  0.5px 0.5px black,-0.5px -0.5px 0.5px black;"><b><?php echo $gegnerBande->GetName(); ?></b></span>
            </a>
        </div>
    </div>
    <hr/>
    <div>
        <div style="float:left; width: 20%; height: 150px;">
            <img src="img/places/<?php echo $place->GetImage(); ?>.png" alt="<?php echo $place->GetName(); ?>" title="<?php echo $place->GetName(); ?>" style="width: 100%; height: 70%;"/>
            <?php echo $place->GetName(); ?><br/>
            <?php echo $planet->GetName(); ?>
        </div>
        <div style="float:right; width: 70%; padding: 10px;">
            Deine Bande hat <span id="fighttimer"><?php echo $clan->GetChallengeCountdown();?></span>
            Minuten Zeit,<br/> nach <?php echo $place->GetName(); ?> - <?php echo $planet->GetName(); ?> zu reisen und 3 Mitglieder dem Bandenkampf beitreten zu lassen.
            Ansonsten erhält <a href="?p=clan&id=<?php echo $gegnerBande->GetID(); ?>"><?php echo $gegnerBande->GetName(); ?></a> den Ort: <?php echo $place->GetName(); ?> - <?php echo $planet->GetName(); ?> als Territorium.
        </div>
        <div class="spacer"></div>
        <form method="post" action="?p=clanmanage&a=read">
            <button class="button" value="Gelesen">Gelesen</button>
        </form>
    </div>
