<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../classes/header.php';
include_once '../../../classes/items/itemmanager.php';
$itemManager = new ItemManager($database);
$items = explode(';', $player->GetNPCWonItems());
if($player->GetNPCWonItemsType() == 12 && !$player->IsNPCWonItemsDungeon())
{
    ?>
    <div class="spacer"></div>
    Du findest dich auf einer Insel wieder, <br/>
    wo du einen Schatz findest. <br/>
    Dein Anteil an der Beute beträgt: <br/>
    <ul style="width: 100%; list-style: none;">
    <?php
    $i = 0;
    while (isset($items[$i]))
    {
        $item = explode('@', $items[$i]);
        $itemData = $itemManager->GetItem($item[0]);
        $itemData->SetDefaultStatsType($item[2]);
        ?>
            <li style="position: relative; left: -6.7%;">
                <div style="display: flex; justify-content: center; align-items: center;">
                    <div>
                        <?php if ($itemData->HasOverlay())
                        {
                            ?>
                                <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $itemData->GetOverlay(); ?>.png" alt="<?php echo $itemData->GetName(); ?>" title="<?php echo $itemData->GetName(); ?>" style="width:60px;height:60px; z-index:1; position: absolute;">
                                <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $itemData->GetImage(); ?>.png" alt="<?php echo $itemData->GetName(); ?>" title="<?php echo $itemData->GetName(); ?>" style="width:60px;height:60px; z-index:0; position: relative;">
                            <?php
                        }
                        else
                        {
                            ?>
                                <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $itemData->GetImage(); ?>.png" alt="<?php echo $itemData->GetName(); ?>" title="<?php echo $itemData->GetName(); ?>" style="width:60px;height:60px; z-index:0;">
                            <?php
                        }
                        ?>
                        <span style="position:relative; right:25px; bottom: 8px; font-size:24px; color:#000;
      text-shadow:
        -1px -1px 0 #fff,
        1px -1px 0 #fff,
        -1px 1px 0 #fff,
        1px 1px 0 #fff;"><b><?php echo number_format($item[1], '0', '', '.'); ?></b></span>
                    </div>
                    <span style="padding-left: 20px;"><?php echo $itemData->GetName(); ?></span>
                </div>
            </li>
            <?php
        ++$i;
    }
    echo '</ul>';
}
else if($player->GetNPCWonItemsType() == 99 && !$player->IsNPCWonItemsDungeon())
    {
        ?>
        Du schwimmst mit den Delfinen und dabei findest du:
        <ul style="width: 100%; list-style: none;">
        <?php
        $i = 0;
        while (isset($items[$i]))
        {
            $item = explode('@', $items[$i]);
            $itemData = $itemManager->GetItem($item[0]);
            $itemData->SetDefaultStatsType($item[2]);
            ?>
            <li style="position: relative; left: -6.7%;">
                <div style="display: flex; justify-content: center; align-items: center;">
                    <div>
                        <?php if ($itemData->HasOverlay())
                        {
                            ?>
                            <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $itemData->GetOverlay(); ?>.png" alt="<?php echo $itemData->GetName(); ?>" title="<?php echo $itemData->GetName(); ?>" style="width:60px;height:60px; z-index:1; position: absolute;">
                            <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $itemData->GetImage(); ?>.png" alt="<?php echo $itemData->GetName(); ?>" title="<?php echo $itemData->GetName(); ?>" style="width:60px;height:60px; z-index:0; position: relative;">
                            <?php
                        }
                        else
                        {
                            ?>
                            <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $itemData->GetImage(); ?>.png" alt="<?php echo $itemData->GetName(); ?>" title="<?php echo $itemData->GetName(); ?>" style="width:60px;height:60px; z-index:0;">
                            <?php
                        }
                        ?>
                        <span style="position:relative; right:25px; bottom: 8px; font-size:24px; color:#000;
      text-shadow:
        -1px -1px 0 #fff,
        1px -1px 0 #fff,
        -1px 1px 0 #fff,
        1px 1px 0 #fff;"><b><?php echo number_format($item[1], '0', '', '.'); ?></b></span>
                    </div>
                    <span style="padding-left: 20px;"><?php echo $itemData->GetName(); ?></span>
                </div>
            </li>
            <?php
            ++$i;
        }
        echo '</ul>';
    }
else
{
    ?>
    Du hast
    <ul style="width: 100%; list-style: none;">
    <?php
        $i = 0;
    while (isset($items[$i]))
    {
        $item = explode('@', $items[$i]);
        $itemData = $itemManager->GetItem($item[0]);
        $itemData->SetDefaultStatsType($item[2]);
        ?>
        <li style="position: relative; left: -6.7%;">
            <div style="display: flex; justify-content: center; align-items: center;">
                <div>
                    <?php if ($itemData->HasOverlay())
                    {
                        ?>
                        <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $itemData->GetOverlay(); ?>.png" alt="<?php echo $itemData->GetName(); ?>" title="<?php echo $itemData->GetName(); ?>" style="width:60px;height:60px; z-index:1; position: absolute;">
                        <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $itemData->GetImage(); ?>.png" alt="<?php echo $itemData->GetName(); ?>" title="<?php echo $itemData->GetName(); ?>" style="width:60px;height:60px; z-index:0; position: relative;">
                        <?php
                    }
                    else
                    {
                        ?>
                        <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $itemData->GetImage(); ?>.png" alt="<?php echo $itemData->GetName(); ?>" title="<?php echo $itemData->GetName(); ?>" style="width:60px;height:60px; z-index:0;">
                        <?php
                    }
                    ?>
                    <span style="position:relative; right:25px; bottom: 8px; font-size:24px; color:#000;
      text-shadow:
        -1px -1px 0 #fff,
        1px -1px 0 #fff,
        -1px 1px 0 #fff,
        1px 1px 0 #fff;"><b><?php echo number_format($item[1], '0', '', '.'); ?></b></span>
                </div>
                <span style="padding-left: 20px;"><?php echo $itemData->GetName(); ?></span>
            </div>
        </li>
        <?php
        ++$i;
    }
    ?>
    </ul>
    gewonnen!
    <?php
}

$redirect = 'profil';
if ($player->GetNPCWonItemsType() == 0)
  $redirect = 'profil';
else if ($player->GetNPCWonItemsType() == 1)
  $redirect = 'fight';
else if ($player->GetNPCWonItemsType() == 3)
  $redirect = 'npc';
else if ($player->GetNPCWonItemsType() == 4)
  $redirect = 'story';
else if ($player->GetNPCWonItemsType() == 5 && !$player->IsNPCWonItemsDungeon())
  $redirect = 'event';
else if ($player->GetNPCWonItemsType() == 5 && $player->IsNPCWonItemsDungeon())
  $redirect = 'boss';
else if ($player->GetNPCWonItemsType() == 10)
  $redirect = 'sidestory';
else if ($player->GetNPCWonItemsType() == 12)
    $redirect = 'treasurehunt';
?>
<div class="spacer"></div>
<form method="post" action="?p=<?php echo $redirect; ?>&a=acceptitem">
  <input type="submit" class="ja" value="Annehmen">
</form>