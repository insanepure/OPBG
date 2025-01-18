<?php
    if($treasurehuntprogress->GetIsland1() != 0)
        $island1 = new treasurehuntisland($database, $treasurehuntprogress->GetIsland1());
    if($treasurehuntprogress->GetIsland2() != 0)
        $island2 = new treasurehuntisland($database, $treasurehuntprogress->GetIsland2());
?>
<div class="spacer"></div>
<?php
    if($island1 != null || $island2 != null)
    {
        ?>
            <div class="map boxSchatten borderL borderR borderT borderB" id="test" style="<?php if($treasurehuntprogress->GetTreasurehuntid() == 1) echo 'filter: blur(8px); transition: 0.8s; '; ?>border:1px solid black; background-image: url('img/schatzsuche/<?php echo $treasurehunt->GetBackgroundImage(); ?>.png?002')">

                <?php
                        if($treasurehuntprogress->GetTreasurehuntid() % 5 == 0)
                        {
                            ?>
                            <div class="tooltip" style="position: absolute; left: <?php echo $island1->GetX() ?>px; top: <?php echo $island1->GetY(); ?>px;">
                                <a onclick="OpenPopupPage('Auswahl', 'treasurehunt/item.php');">
                                    <div class="island" style="background-image: url('img/schatzsuche/<?php echo $island1->GetImage(); ?>.png?001'); width: <?php echo $island1->GetWidth(); ?>px; height: <?php echo $island1->GetHeight(); ?>px;" ></div>
                                </a>
                                <span class="tooltiptext" style="width:180px; top:30px; left:110px;">
                                    Reise antreten
                                </span>
                            </div>
                            <?php
                        }
                        else
                        {
                            if($island1 != null)
                            {
                                ?>
                                <div class="tooltip" style="position: absolute; left: 360px; top: 30px;">
                                    <a href="?p=treasurehunt&a=fight1">
                                        <div class="island" style="background-image: url('img/schatzsuche/<?php echo $island1->GetImage(); ?>.png?001'); width: <?php echo $island1->GetWidth(); ?>px; height: <?php echo $island1->GetHeight(); ?>px;" ></div>
                                    </a>
                                    <span class="tooltiptext" style="width:180px; top:30px; left:10px;">
                                    Reise antreten
                                </span>
                                </div>
                                <?php
                            }
                            if($island2 != null)
                            {
                                ?>
                                <div class="tooltip" style="position: absolute; left: 440px; top: 175px;">
                                    <a href="?p=treasurehunt&a=fight2">
                                        <div class="island" style="background-image: url('img/schatzsuche/<?php echo $island2->GetImage(); ?>.png?001'); width: <?php echo $island2->GetWidth(); ?>px; height: <?php echo $island2->GetHeight(); ?>px;" ></div>
                                    </a>
                                    <span class="tooltiptext" style="width:180px; top:30px; left:10px;">
                                    Reise antreten
                                </span>
                                </div>
                                <?php
                            }
                        }
                    ?>
                    <?php
                        if($treasurehuntprogress->GetDolphin1())
                        {
                            ?>
                            <div class="tooltip" style="position: absolute; left: 10px; top: 210px;">
                                <a href="?p=treasurehunt&a=dolphin">
                                    <div class="dolphin" style="background-image: url('img/schatzsuche/dolphin1.png?001');"></div>
                                </a>
                            </div>
                            <?php
                        }
                        if($treasurehuntprogress->GetDolphin2())
                        {
                            ?>
                            <div class="tooltip" style="position: absolute; left: 590px; top: 20px;">
                                <a href="?p=treasurehunt&a=dolphin">
                                    <div class="dolphin" style="background-image: url('img/schatzsuche/dolphin2.png?001');"></div>
                                </a>
                            </div>
                            <?php
                        }
                        if($treasurehuntprogress->GetDolphin3())
                        {
                            ?>
                            <div class="tooltip" style="position: absolute; left: 340px; top: 430px;">
                                <a href="?p=treasurehunt&a=dolphin">
                                    <div class="dolphin" style="background-image: url('img/schatzsuche/dolphin3.png?001');"></div>
                                </a>
                            </div>
                            <?php
                        }
                            ?>
                                <div id="erklaerung" style="position: absolute; left: 0; z-index: 10000000;" hidden>
                                    <img id="erklaerung1" src="img/schatzsuche/Erklarung1.png?100" style="cursor: pointer;" onclick="erklaerung2.hidden = false; this.hidden = true;">
                                    <img id="erklaerung2" src="img/schatzsuche/Erklarung2.png?100" style="cursor: pointer;" onclick="erklaerung3.hidden = false; this.hidden = true;" hidden>
                                    <img id="erklaerung3" src="img/schatzsuche/Erklarung3.png?100" style="cursor: pointer;" onclick="erklaerung4.hidden = false; this.hidden = true;" hidden>
                                    <img id="erklaerung4" src="img/schatzsuche/Erklarung4.png?100" style="cursor: pointer;" onclick="erklaerung5.hidden = false; this.hidden = true;" hidden>
                                    <img id="erklaerung5" src="img/schatzsuche/Erklarung5.png?100" style="cursor: pointer;" onclick="this.hidden = true;" hidden>
                                </div>
                                <a onclick="erklaerung.hidden = false; erklaerung1.hidden = false" style="cursor: pointer;">
                                    <img src="img/schatzsuche/help.png" style="position: absolute; left: 0;" width="75" height="75" />
                                </a>
            </div>
            <?php
            if ($treasurehuntprogress->GetTreasurehuntid() == 1)
            {
                ?>
                    <div class="map" style="cursor: pointer; position: absolute; top: 0;" onclick="test.style.filter = ''; this.hidden = true;">
                        <img style="width: 90%; height: 70%; position: relative; top: 20%;" src="img/schatzsuche/SchatzsucheStart.png" />
                    </div>
                <?php
            }
        }
    else
    {
        ?>
            <div class="map boxSchatten borderL borderR borderT borderB" style="filter: blur(8px); border:1px solid black; background-image: url('img/schatzsuche/schatzsuche.png?002')"></div>
            <div class="map" style="position: absolute; top: 0;">
                <img style="width: 90%; height: 70%; position: relative; top: 20%;" src="img/schatzsuche/SchatzsucheEnde.png" />
            </div>
        <?php
    }
?>
<div class="spacer"></div>
<?php
    if($player->GetArank() >= 2)
    {
        ?>
            <a href="?p=treasurehunt&a=reset">
                <button>Reset</button>
            </a>
            <?php
                if($treasurehuntprogress->GetTreasurehuntid() < $treasurehuntmanager->GetTreasurehuntCount())
                {
                    ?>
                    <a href="?p=treasurehunt&a=skip">
                        <button>Überspringen</button>
                    </a>
                    <?php
                }
            ?>
        <?php
    }
?>