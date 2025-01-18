<script>
    grecaptcha.ready(function() {
        grecaptcha.execute('6Lc5RnwbAAAAAF1iOYAEgWqmIesmvK7S-nES_7Pb', {
            action: 'Map'
        });
    });
</script>
<div class="spacer"></div>
<?php
if ($player->GetPlanet() == 3 && $player->HasLogPort() or $player->GetPlanet() == 4 && $player->HasLogPort() or $player->GetPlanet() == 1 && $player->HasEastBlueMap() or $player->GetPlanet() == 2 && $player->HasImpeldownMap() or $player->GetPlanet() == 5 && $player->HasNorthBlueMap() or $player->GetPlanet() == 6 && $player->HasWestBlueMap() or $player->GetPlanet() == 7 && $player->HasSouthBlueMap())
{

    if($player->GetPlanet() == 4 && $player->GetStory() > 210) // Licht der Shandora kommt
        $background = 'skypiea5';
    else if($player->GetPlanet() == 4 && $player->GetStory() > 207 && $player->GetStory() <= 210) // angel island & beach weg
        $background = 'skypiea4';
    else if($player->GetPlanet() == 4 && $player->GetStory() >= 204 && $player->GetStory() <= 207) // Arche Maxim kommt
        $background = 'skypiea2';
    else
        $background = $planet->GetMap();
    ?>
    <div id="map" class="map boxSchatten borderL borderR borderT borderB" style=" border:1px solid black; background-image: url('img/planets/<?php echo $background; ?>.png?006')">
        <div id="placename"></div>
        <?php
        $events = new Generallist($database, 'events', '*', 'isdungeon="0"', '', 9999, 'ASC');


        $story = new Story($database, $player->GetStory());
        $sidestory = new SideStory($database, $player->GetSideStory());

        $places = new Generallist($database, 'places', 'id,name,description,display,x,y,image,planet,travelable, adminplace, storymax, storymin', 'planet = "' . $player->GetPlanet() . '"', 'y', 99999, 'ASC');
        $id = 0;
        $entry = $places->GetEntry($id);
        $x = 0;
        $y = 0;
        $tX = 0;
        $tY = 0;
        $pX = 0;
        $pY = 0;
        while ($entry != null)
        {
            if ($entry['id'] == $player->GetPreviousPlace())
            {
                $pX = $entry['x'];
                $pY = $entry['y'];
            }
            if ($entry['id'] == $player->GetTravelPlace())
            {
                $tX = $entry['x'];
                $tY = $entry['y'];
            }
            if ($entry['id'] == $player->GetPlace())
            {
                $x = $entry['x'];
                $y = $entry['y'];
            }
            if($player->GetPlanet() == 4)
                $placeCSS = 'mapplaceskypiea';
            else
                $placeCSS = 'mapplace';


            $eid = 0;
            $eventEntry = $events->GetEntry($eid);
            while ($eventEntry != null)
            {
                $isPlaceAndPlanet = Event::IsPlaceAndPlanet($entry['planet'], $entry['name'], $eventEntry['placeandtime']);
                if ($isPlaceAndPlanet && $player->GetLevel() >= $eventEntry['level'])
                {
                    $isToday = Event::IsToday($entry['planet'], $entry['name'], $eventEntry['placeandtime']);
                    if ($isToday)
                    {
                        if($player->GetPlanet() == 4)
                            $placeCSS = 'mapplaceskypiea';
                        else
                            $placeCSS = 'mapplace';
                        break;
                    }
                }
                ++$eid;
                $eventEntry = $events->GetEntry($eid);
            }

            if ($sidestory->GetPlace() == $entry['id'] && $sidestory->GetPlanet() == $player->GetPlanet())
            {
                if($player->GetPlanet() == 4)
                    $placeCSS = 'mapplacesidestoryskypiea';
                else
                    $placeCSS = 'mapplacesidestory';
            }
            if ($story->GetPlace() == $entry['id'] && $story->GetPlanet() == $player->GetPlanet())
            {
                if($player->GetPlanet() == 4)
                    $placeCSS = 'mapplacestoryskypiea';
                else
                    $placeCSS = 'mapplacestory';
            }

            if (($entry['display'] == 1 || $player->GetArank() >= 2 && $entry['adminplace'] == 1) && ($player->GetStory() <= $entry['storymax'] || $entry['storymax'] == 0) && $player->GetStory() >= $entry['storymin'])
            {
                ?>

                <div class="tooltip" style="z-index:2; position:absolute; left:<?php echo $entry['x']; ?>px; top:<?php echo $entry['y']; ?>px;">

                    <a style="cursor:pointer;" onclick="OpenPopupPage('<?php echo $entry['name'];?>','map/place.php?id=<?php echo $entry['id']; ?>')">
                        <div class="<?php echo $placeCSS; ?> place" onmouseenter="document.getElementById('placename').innerText = '<?= $entry['name'] ?>'; document.getElementById('placename').style.display = 'block';" onmouseleave="document.getElementById('placename').style.display = 'none';" style="left:59px; top:30px;"></div>
                    </a>
                </div>


                <?php
            }
            ++$id;
            $entry = $places->GetEntry($id);
        }
    $race = $player->GetRace();

    if ($player->GetPreviousPlace() != 0)
    {
        if ($player->GetPreviousPlace() == -1)
        {
            $pX = $player->GetX();
            $pY = $player->GetY();
        }
        $tempX = $tX - $pX;
        $tempY = $tY - $pY;
        $secondsLeft = $player->GetTravelActionCountdown();
        $maxSeconds = $player->GetTravelActionTime() * 60;
        if ($player->GetTravelActionTime() == 0)
        {
            $secondsPassed = 0;
        }
        else
        {
            $secondsPassed = 1 - ($secondsLeft / $maxSeconds);
        }
        $x = $pX + round($tempX * $secondsPassed);
        $y = $pY + round($tempY * $secondsPassed);
    }
    else if ($player->GetX() != 0 && $player->GetY() != 0)
    {
        $place = new Place($database, $player->GetPlace(), new ActionManager($database));
        echo $place->IsValid();
        if($place->IsValid() && $player->GetPlace() != -1)
        {
            $x = $place->GetX();
            $y = $place->GetY();
        }
        else
        {
            $x = $player->GetX();
            $y = $player->GetY();
        }
    }
    ?>
    <img class="mapcharacter" style="left:<?php echo $x + 55; ?>px; top:<?php echo $y + 27; ?>px; height: 20px; width: 20px;" src="img/planets/<?php echo $race; ?>.png">
    </div>
    <?php
    if($player->GetArank() >= 2)
    {
        ?>
        x: <span id="x">0</span> y: <span id="y">0</span>
        <script>
            var map = document.getElementById('map');
            map.onmousemove = function(e) {
                // e = Mouse click event.
                var rect = map.getBoundingClientRect();
                var x = e.clientX - rect.left - 67; //x position within the element.
                var y = e.clientY - rect.top - 36;  //y position within the element.
                document.getElementById('x').innerText = Number(x).toFixed(0);
                document.getElementById('y').innerText = Number(y).toFixed(0);
            }
        </script>
        <?php
    }
    ?>
    <?php
}
else
{
    if($player->GetPlanet() == 3)
        echo 'Du benötigst auf der Grandline einen Logport!';
    else if($player->GetPlanet() == 1)
        echo 'Du benötigst eine Karte East Blue!';
    else if($player->GetPlanet() == 2)
        echo 'Du benötigst eine Karte Impel Down!';
    echo "<img src='img/marketing/onepiecenokarte.png' />";
}
?>

<?php
if ($player->GetARank() >= 2)
{
    $select = "id, name";
    $where = 'planet="' . $player->GetPlanet() . '"';
    $order = 'id';
    $from = 'places';
    $list = new Generallist($database, $from, $select, $where, $order, 1000, 'DESC');
    ?>
    <form method="POST" action="?p=map&a=jump">
        <select class="select" name="placeid" id="placeid">
            <?php
            //preSort the arrays, so that we can easily show them
            $id = 0;
            $entry = $list->GetEntry($id);
            while ($entry != null)
            {
                ?>
                <option value="<?php echo $entry['id']; ?>"><?php echo $entry['name'] . ' (' . $entry['id'] . ')'; ?></option>
                <?php
                ++$id;
                $entry = $list->GetEntry($id);
            }
            ?>
        </select>
        <input type="submit" value="Springen">
    </form>
    <br />
    <?php
}
?>