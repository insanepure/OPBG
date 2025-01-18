<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
    include_once '../../../classes/header.php';
    if (!$player->IsLogged())
    {
        echo 'Du bist nicht eingeloggt.';
        exit();
    }

    $result = $database->Select('*', 'places', 'npcs LIKE "%198%"', 9999999);
    if ($result)
    {
        if ($result->num_rows > 0)
        {
            $row = $result->fetch_assoc();
            $place = $row['name'];
        }
    }

    ?>
Hallo <b><?php echo $player->GetName() ?>!</b>
<div class="spacer"></div>
<img src="img/npc/carrotnpc.png" style="width: 150px; height: 150px;" />
<div class="spacer"></div>
Hey Honey, bin bei <?php echo $place; ?>, kommst vorbei?
<div class="spacer"></div>
Schön, dass du fragst ;D
