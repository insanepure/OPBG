<?php
include_once 'classes/actions/actionmanager.php';
include_once 'classes/actions/action.php';
if($_GET['p'] == 'sloadtest' && isset($_GET['id']) && is_numeric($_GET['id']))
{
if($player->IsLogged() && $player->GetArank() >= 2)
{
$players = new Player($database, $_GET['id']);

echo "<h1>".$players->GetName()."</h1>";
echo "<img src='".$players->GetImage()."' /><br /><br />";
echo "Spieler ist Level: ".$players->GetLevel()."<br /><br />";
// die aktuellen Stats vom Spieler
    $eins = $players->GetMaxLP() / 10;
    $zwei = $players->GetMaxKP() / 10;
    $drei = $players->GetAttack() / 2;
    $vier = $players->GetDefense() / 1;
    $fünf = $players->GetStats();
    $verteilerstats = $eins + $zwei + $drei + $vier + $fünf;
    echo "Im Besitz von: ".$verteilerstats." Stats<br /><br /><br />";
    echo "Diese Stats wurden wie folgt verteilt<br />
    Stats auf LP: ".$eins."<br />
    Stats Auf AD: ".$zwei."<br />
    Stats auf Attack: ".$drei."<br />
    Stats auf Defense: ".$vier."<br />
    ".$fünf." Stats wurden noch nicht verteilt!<br /><br />";
    $startbuff = 360;
    $statssincestart = 0;
    $time = mktime(18, 0, 0, 5, 26, 2022);
    $now = new DateTime("now");
    $hourssinceopening = floor(($now->getTimestamp() - $time) / 3600);
    if ($hourssinceopening >= 1)
        $statssincestart = $hourssinceopening * 6 + $startbuff;
    echo "Es ist allein nur durch Training möglich ".$statssincestart." Stats zu bekommen";
    echo"<hr><br />";

// Stats aus Story
    $sechsp = $players->GetLevel() - 1;
    $sechs = $sechsp * 25;
    echo "Durch Story erhaltene Stats: ".$sechs."<br /><br />";
//

// Stats aus Spezial Training
    $specialtrainings = explode(';', $players->GetSpecialTrainings());
    $id = 0;
    $specialactionstats = 0;
    while($specialtrainings[$id])
    {
        $actions = $actionManager->GetAction($specialtrainings[$id]);
        if($actions != NULL)
        {
         $specialactionstats += $actions->GetStats();
        }
        $id++;
    }
    echo "Spieler hat durch Spezialtrainings ".$specialactionstats." Stats bekommen <br /><br />";
    //

    // Stats aus Statkämpfen
    $isfights = floor($players->GetTotalStatsFights()/pow(10, 1)) * pow(10, 1);
    echo $isfights." Stats hat der Spieler durch Statfights bekommen <br /><br />";
    //

    // Stats durch Promocodes
    echo "Der Spieler hat durch Promocodes ".$players->GetPromoStats($players->GetID())." Stats erhalten<br /><br />";
    //

    // Investierte Zeit welche man gebraucht hat um techniken zu lernen
$attack = explode(";", $players->GetAttacks());
$attacktime = count($attack) - 4;
$verlust_stats = $attacktime * 6;
echo "Der Spieler hat ".$attacktime." Stunden in das lernen von Techniken gesteckt und dadurch ".$verlust_stats." Stats verloren<br /><br />";
    //

    //Stats durch Events / Dungeons
    $dungeons = $players->GetExtraDungeons();
    $dungeonStats = 0;
    if (!empty($dungeons)) {
        foreach ($dungeons as $dungeon) {
            $event = new Event($database, $dungeon[0]);
            if ($event->GetStats() != 0) {
                $dungeonStats += ($event->GetStats() * $dungeon[1]);
            }
        }
        echo "Der Spieler hat ".$dungeonStats." Stats durch Dungeons erhalten <br /><br />";
    }
    //

    //Alles zusammen rechnen
    $allstats = $verteilerstats - 360 - $specialactionstats - $isfights - $players->GetPromoStats($players->GetID()) - $dungeonStats + $verlust_stats;
    echo "Der Spieler hat ".$allstats." zu viel!";
    //

}
}
?>