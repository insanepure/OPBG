<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'classes/bbcode/bbcode.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'classes/items/itemmanager.php';
    $itemManager = new ItemManager($database);
    if($player->GetArank() < 2)
    {
        header('Location: ?p=news');
        exit();
    }

    function ShowSlotEquippedImage($slot, $inventory, $zorders, $zordersOnTop)
    {
        $item = $inventory->GetItemAtSlot($slot);
        if ($item != null)
        {
            if ($item->IsOnTop())
                $zindex = $zordersOnTop[$slot];
            else
                $zindex = $zorders[$slot];
            if($item->GetEquippedImage() != '')
            {
                ?>
                <div class="char2" style="z-index:<?php echo $zindex; ?>; background-image:url('img/ausruestung/<?php echo $item->GetEquippedImage(); ?>.png?005')"></div>
                <?php
            }
        }
    }

    function ShowSlot($player, $slot, $inventory)
    {
        $item = $inventory->GetItemAtSlot($slot);

        if ($item != null)
        {
            global $database;
            $itemManager = new ItemManager($database);
            $statsItem = $itemManager->GetItem($item->GetStatsID());
            ?>
            <div class="tooltip" style="position: relative; top:0; left:0; width: 170px; z-index: 100;">
                <span style="font-size: 12px"><?php echo $item->GetName(); ?></span>
                <div class="spacer"></div>
                <div style="width:50px; height:50px; position:relative; top:-5px; left:-25px;">
                    <?php if ($item->HasOverlay())
                    {
                        ?>
                        <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetOverlay(); ?>.png?002" style="width:50px;height:50px; position:absolute; z-index:1;">
                        <?php
                    }
                    ?>
                    <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetImage(); ?>.png?002" style="width:50px;height:50px; position:absolute; z-index:0;">
                </div>
                <span class="tooltiptext" style="position:absolute; width:200px; height: auto; left: -15px; bottom: 50px;">
                    <?php echo $item->GetName(); ?>
                    <hr/>
                    <?php
                        if($item->GetStatsID() != $item->GetVisualID())
                            echo 'Stats von ' . $statsItem->GetName();
                    ?>
                    <div style="width:100%; height: 90px; top:0; left:-40px; position:relative;">
                        <?php if ($item->HasOverlay())
                        {
                            ?>
                            <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetOverlay(); ?>.png?002" style="width:80px;height:80px; position: absolute; z-index:1;">
                            <?php
                        }
                        ?>
                        <img class="boxSchatten borderT borderR borderL borderB" src="img/items/<?php echo $item->GetImage(); ?>.png?002" style="width:80px;height:80px; position: absolute; z-index:0;">
                    </div>
                    <span>
                        <?php
                            echo $item->DisplayEffect();
                        ?>
                    </span>
                    <?php
                        if($item->GetLevel() > 0)
                        {
                            ?>
                            <span>
                                  Benötigtes Level: <?php echo number_format($item->GetLevel(), 0, '', '.'); ?>
                              </span>
                            <?php
                        }
                    ?>
                </span>
            </div>
            <?php
        }
    }
    $title = 'Detailsuche';
    $shipArray = array(1, 40, 41, 42, 43, 44, 45, 46, 47, 48, 384, 385);
    $sonstigeItems = array(
            36, // Karte Eastblue
            73, // Kiribachi
            37, // Karte Northblue
            38, // Karte Southblue
            39, // Karte Westblue
            72, // Karte Impeldown
            85, // Logport
            10, // Teleschnecke
        //353, // Casino Rabatt Coupon (100%)
        //352, // Casino Rabatt Coupon (75%)
        //351, // Casino Rabatt Coupon (50%)
        //350, // Casino Rabatt Coupon (25%)
            30, // Skill Reset
            9, // Stats Reset
            405, // Halloween Stats Reset
            137, // Kerkerschlüssel
            388, // Impel Down Level 1 Schlüssel
            389, // Impel Down Level 2 Schlüssel
            390, // Impel Down Level 3 Schlüssel
            391, // Impel Down Level 3 Schlüssel
            392, // Impel Down Level 3 Schlüssel
            393, // Impel Down Level 3 Schlüssel
            119, // Rumble Ball
            104, // Stat Geist
        //315, // Einfacher Splitter
        //316, // Seltener Splitter
        //317, // Legendärer Splitter
            302, // Reiseticket
            223, // Star Stats
            222, // Spalter
            224, // Splitter eines Nonosama Bo
            226, // Gan Forts Schlüssel
            399, // South Bird
    );
    $schiffItems = array(
            49, // Holz
            50, // Nägel
            51, // Stoff
            1, // Boot
            40, // Going Merry
            41, // Big Top
            42, // Marine Schiff
            43, // Thousand Sunny
            44, // Nostra Castello
            45, // Red Force
            46, // Die Mobby Dick
            47, // Oro Jackson
            48, // Victoria Punk
            384, // Aokiji's Fahrrad
            385 // Garp's Kampfschiff
    );
    $fightItems = array(
            86, //Seltene Rote Frucht
            87, // Seltene Orangene Frucht
            88, // Seltene Gelbe Frucht
            81, // Testo Booster
            82, // Vitamine
            406, // Grüne Wolke
            407 // Rote Wolke
    );
    $aufwertungsItems = array(
            71, // Schmied W.
            174, // Rüstungskristall
            180, // Halloween Rüstungskristall
            181 // Halloween Schmied W.
    );