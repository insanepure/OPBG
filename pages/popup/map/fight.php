<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../classes/header.php';
include_once '../../../classes/places/place.php';

if($player->GetClan() != 0)
{
    $place = new Place($database, $player->GetPlace(), $actionManager);
    $group = $player->GetGroup();

    $weekday = date("l");
    if($weekday == "Saturday" || $weekday == "Sunday")
    {
        $min = '12';
        $max = '22';
    }
    else
    {
        $min = '16';
        $max = '22';
    }
    $groupcheck = true;
    if($group != null && count($group) == 3)
    {

        foreach ($group as $groupmember) {
            $groupplayer = new Player($database, $groupmember);
            if($groupplayer->GetClan() != $player->GetClan()) {
                $groupcheck = false;
                $message = "Mindestens ein Gruppenmitglied ist kein Clanmitglied.";
            }
            if($groupplayer->GetPlace() != $player->GetPlace()) {
                $groupcheck = false;
                $message = "Mindestens ein Gruppenmitglied befindet sich nicht am richtigen Ort.";
            }
            if($groupplayer->InClanSince() > -5) {
                $groupcheck = false;
                $message = "Mindestens ein Gruppenmitglied ist nicht lang genug in der Bande - Alle teilnehmenden Mitglieder müssten mindesten 5 Tage Mitglied der Bande sein.";
            }
        }
        /*if(!$groupcheck)
            $groupcheck = !$groupcheck;*/
    }
    if($place->IsValid() && $place->IsEarnable() && $groupcheck && ($place->GetTerritorium() == 0 || $place->GetTerritorium() != $player->GetClan()) && date('H') >= $min && date('H') < $max)
    {
        if($place->GetTerritorium() == 0)
        {
            ?>
            <img src="img/places/<?php echo $place->GetImage(); ?>.png" alt="<?php echo $place->GetName(); ?>" title="<?php echo $place->GetName(); ?>" style="width: 400px; height: 250px;"/>
            <div class="spacer"></div>
            <?php echo $place->GetName(); ?>
            <div class="spacer"></div>
            Da noch keine Bande diesen Ort beansprucht hat,<br/>
            kann deine Bande ihn einfach beanspruchen.
            <div class="spacer"></div>
            Jeder Spieler, der an diesen Ort reisen möchte, <br/>
            muss deiner Bande dann 10 <img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 5px; height: 20px; width: 20px;"/> Tribut zahlen.
            <div class="spacer"></div>
            Das Beanspruchen kostet der Bande 25.000 <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 5px; height: 20px; width: 13px;"/>
            <form method="post" action="index.php?a=claim">
                <input type="submit" class="ja" value="Beanspruchen">
            </form>
            <?php
        }
        else
        {
            $claimedBande = new Clan($database, $place->GetTerritorium());
            $ownBande = new Clan($database, $player->GetClan());
            ?>
            <div style="height: 250px;">
                <img src="img/offtopic/bandenchallenge.png" style="width: 100%; height: 250px;"/>
                <div style="position: relative; top: -240px; left: -196px;">
                    <img src="<?php if($ownBande->GetImage() == '') echo "img/clannoimage.png"; else echo $ownBande->GetImage(); ?>" style="width: 175px; height: 130px;"/>
                </div>
                <div style="position: relative; top: -326px; left: 196px;">
                    <img src="<?php if($claimedBande->GetImage() == '') echo "img/clannoimage.png"; else echo $claimedBande->GetImage(); ?>" style="width: 175px; height: 130px;"/>
                </div>
                <img src="img/offtopic/bandenchallangeoverlay.png" style="position:relative; top: -525px; width: 100%; height: 250px;"/>
                <div style="display:flex; justify-content: center; align-items: center; position: relative; left: -197px; top: -636px; width: 200px; height: 53px;">
                    <a href="?p=clan&id=<?php echo $ownBande->GetID(); ?>">
                        <span style="font-family: 'Playfair Display SC', serif; font-weight:bold; font-size:30px; color:#5e4c46; text-shadow: 0.5px  0.5px 0.5px black,0.5px -0.5px 0.5px black,-0.5px  0.5px 0.5px black,-0.5px -0.5px 0.5px black;"><b><?php echo $ownBande->GetName(); ?></b></span>
                    </a>
                </div>
                <div style="display:flex; justify-content: center; align-items: center; position: relative; left: 197px; top: -636px; width: 200px; height: 53px;">
                    <a href="?p=clan&id=<?php echo $claimedBande->GetID(); ?>">
                        <span style="font-family: 'Playfair Display SC', serif; font-weight:bold; font-size:30px; color:#5e4c46; text-shadow: 0.5px  0.5px 0.5px black,0.5px -0.5px 0.5px black,-0.5px  0.5px 0.5px black,-0.5px -0.5px 0.5px black;"><b><?php echo $claimedBande->GetName(); ?></b></span>
                    </a>
                </div>
            </div>
            <div class="spacer"></div>
            Fordere <a href="?p=clan&id=<?php echo $claimedBande->GetID();?>"><?php echo $claimedBande->GetName(); ?></a> heraus, <br/>
            wenn deine Gruppe gewinnt muss jeder Spieler, <br/>
            der an diesen Ort reisen möchte, <br/>
            deiner Bande 10 <img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 5px; height: 20px; width: 20px;"/> Tribut zahlen.
            <div class="spacer"></div>
            Nach der Herausforderung hat <a href="?p=clan&id=<?php echo $claimedBande->GetID();?>"><?php echo $claimedBande->GetName(); ?></a> 30 Minuten Zeit dem Kampf beizutreten.
            <div class="spacer"></div>
            <?php
            if((strtotime($place->GetTime())+(7*24*60*60)) <= time() && $place->IsBlocked() == $player->GetClan() || $place->IsBlocked() != $player->GetClan())
            {
                ?>
                <form method="post" action="index.php?a=challenge">
                    <input type="submit" class="nein" value="Herausfordern">
                </form>
                <?php
            }
            else
            {
                echo 'Das Gebiet kann erst am ' . date("d.m.Y \u\m H:i:s", strtotime($place->GetTime()) + (7*24*60*60)) . ' wieder angegriffen werden.';
            }
        }
    }
    else
    {
        if(!$groupcheck)
            echo $message;
        else if(!$place->IsValid())
            echo "Du befindest dich an keinen gültigen Ort.";
        else if(!$place->IsEarnable())
            echo "Das Gebiet kann nicht angegriffen werden.";
        else if(date('H') < $min && date('H') >= $max)
            echo "Das Gebiet kann derzeit nicht angegriffen werden.";
    }
}
else
{
    echo "Du kannst dieses Gebiet nur angreifen wenn du in ein Clan bist.";
}