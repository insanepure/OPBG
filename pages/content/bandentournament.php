<style>
.clanbild{
    width: 10%;
    height: 5%;
    border-radius: 100%;
    -webkit-border-radius: 100%;
    -moz-border-radius: 100%;
}
.rank1{
    z-index: 1;
position: absolute;
    left: 200px;
}

.rank2{
    z-index: 1;
    position: absolute;
    left: 180px;

}

.rank3{
    z-index: 1;
    position: absolute;
    left: 160px;

}

.rank4{
    z-index: 1;
    position: absolute;
    left: 140px;

}

.rank5{
    z-index: 1;
    position: absolute;
    left: 120px;

}

.rank6{
    z-index: 1;
    position: absolute;
    left: 110px;

}

.rank7{
    z-index: 1;
    position: absolute;
    left: 100px;

}

.rank8{
    z-index: 1;
    position: absolute;
    left: 90px;

}

.rank9{
    z-index: 1;
    position: absolute;
    left: 80px;

}

.rank10{
    z-index: 1;
    position: absolute;
    left: 70px;

}

.rank11{
    z-index: 1;
    position: absolute;
    left: 60px;

}

.rank12{
    z-index: 1;
    position: absolute;
    left: -50px;

}

.rank13{
    z-index: 1;
    position: absolute;
    left: -40px;

}

.rank14{
    z-index: 1;
    position: absolute;
    left: -30px;

}

.rank15{
    z-index: 1;
    position: absolute;
    left: -20px;

}

.rank16{
    z-index: 1;
    position: absolute;
    left: -100px;

}

.rank17{
    z-index: 1;
    position: absolute;
    left: -120px;

}

.rank18{
    z-index: 1;
    position: absolute;
    left: -140px;

}

.rank19{
    z-index: 1;
    position: absolute;
    left: -160px;

}

.rank20{
    z-index: 1;
    position: absolute;
    left: -180px;

}

.rank15{
    z-index: 1;
    position: absolute;
    left: -260px;

}

.rankonetafel{
z-index: 1;
    position: absolute;
    left: 500px;
    top: 85px;
}
</style>
<?php
include_once 'classes/header.php';
# var $database
if($player->GetArank() >= 0)
{
    $ClanManager = $database->Select('*', 'clans', 'members >= 3');
    if($ClanManager)
    {
        $max = 0;
        $maxx = 0;
        while($clan = $ClanManager->fetch_assoc())
        {
            $run = floor(1450 - $clan['runnpoints']);
            if($run < 50)
            {
                $run = 50;
            }
            if($clan['runnpoints'] > $max)
                {
                    $max = $clan['runnpoints'];
                }
            $maxx += $clan['runnpoints'];
            if($maxx == 0)
            {
                $max = 0;
            }
            if($clan['id'] == $player->GetClan())
            {
              ?>
                <div class="rankonetafel">
                    <?php
                    echo "<b>".number_format($clan['runnpoints'], 0, '', '.')."</b>";
                    ?>
                </div>
                    <?php
            }
                ?>

                <div style="top: <?= $run; ?>px;" class="rank<?= $clan['rang']; ?>">
            <a href="?p=clan&id=<?= $clan['id']; ?>"><img  class="clanbild" title="<?= $clan['name']; ?>" src="<?= $clan['image']; ?>" /></a>
                </div>
            <?php
            ++$max;
        }
    }
    ?>
<?php
}
?>