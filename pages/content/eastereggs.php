<?php
if(!isset($player) || !$player->IsValid() || $player->GetArank() < 2)
    header("Location: index.php");

echo "<br/><br/>Die aufgelisteten Spieler haben das Osterei des ausgewählten Tages gefunden.<br/><br/><br/>";

$result = $database->Select('*', 'eventitemsunlocked', 'eventitem >= 1 AND eventitem < 15');
if($result && $result->num_rows > 0)
{
    ?>
    <a href="?p=eastereggs&sort=1">Tag 1</a> | <a href="?p=eastereggs&sort=2">Tag 2</a> | <a href="?p=eastereggs&sort=3">Tag 3</a> | <a href="?p=eastereggs&sort=4">Tag 4</a> | <a href="?p=eastereggs&sort=5">Tag 5</a> | <a href="?p=eastereggs&sort=6">Tag 6</a> | <a href="?p=eastereggs&sort=7">Tag 7</a> | <a href="?p=eastereggs&sort=8">Tag 8</a> | <a href="?p=eastereggs&sort=9">Tag 9</a> | <a href="?p=eastereggs&sort=10">Tag 10</a><br/>
    <a href="?p=eastereggs&sort=11">Tag 11</a> | <a href="?p=eastereggs&sort=12">Tag 12</a> | <a href="?p=eastereggs&sort=13">Tag 13</a> | <a href="?p=eastereggs&sort=14">Tag 14</a> | <a href="?p=eastereggs&sort=15">Tag 15</a><br/>
    <?php
    $finders = array();
    while($row = $result->fetch_assoc())
    {
        $fnd = new Player($database, $row['acc']);
        if($fnd->IsValid())
        {
            $finders[$row['eventitem']][] = $fnd->GetName();
        }
    }

    echo "<br/><br/>";

    if(isset($_GET['sort']))
    {
        if(isset($finders[$_GET['sort']])) {
            foreach ($finders[$_GET['sort']] as $finder) {
                echo $finder . "<br/>";
            }
        }
        else
        {
            echo "An diesen Tag wurden noch keine Ostereier gefunden.";
        }
    }
}