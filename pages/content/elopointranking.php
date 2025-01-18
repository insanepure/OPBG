<style>
.rank1{
    top: 131px;
    left: 98px;
}

.rank1name{
    top: 260px;
    left: 120px;
    width: 118px;
    white-space: nowrap;
    font-weight: bold;
}

.rank1clan{
    top: 280px;
    left: 120px;
    width: 118px;
    font-weight: bold;
    white-space: nowrap;
    font-size: 12px;
}

.rank2{
    top: 131px;
    left: 271px;
}

.rank2name{
    top: 260px;
    left: 298px;
    width: 118px;
    white-space: nowrap;
    font-weight: bold;
}

.rank2clan{
    top: 280px;
    left: 298px;
    width: 118px;
    white-space: nowrap;
    font-weight: bold;
    font-size: 12px;
}

.rank3{
    top: 131px;
    left: 452px;
}

.rank3name{
    top: 260px;
    left: 470px;
    width: 118px;
    white-space: nowrap;
    font-weight: bold;
}

.rank3clan{
    top: 280px;
    left: 470px;
    width: 118px;
    white-space: nowrap;
    font-weight: bold;
    font-size: 12px;
}

.rank4{
    top: 352px;
    left: 80px;
}
.rank5{
    top: 352px;
    left: 186px;
}
.rank6{
    top: 352px;
    left: 292px;
}
.rank7{
    top: 352px;
    left: 397px;
}
.rank8{
    top: 352px;
    left: 501px;
}
.rank9{
    top: 457px;
    left: 80px;
}
.rank10{
    top: 457px;
    left: 186px;
}
.rank11{
    top: 457px;
    left: 292px;
}
.rank12{
    top: 457px;
    left: 397px;
}
.rank13{
    top: 457px;
    left: 501px;
}
.rank14{
    top: 560px;
    left: 80px;
}
.rank15{
    top: 560px;
    left: 186px;
}
.rank16{
    top: 560px;
    left: 292px;
}
.rank17{
    top: 560px;
    left: 397px;
}
.rank18{
    top: 560px;
    left: 501px;
}
.rank19{
    top: 667px;
    left: 80px;
}

.rank20{
    top: 667px;
    left: 186px;
}
.rank21{
    top: 667px;
    left: 292px;
}
.rank22{
    top: 667px;
    left: 397px;
}

.rank23{
    top: 667px;
    left: 501px;
}

.rank24{
    top: 772px;
    left: 80px;
}

.rank25{
    top: 772px;
    left: 186px;
}

.rank26{
    top: 772px;
    left: 292px;
}

.rank27{
    top: 772px;
    left: 397px;
}

.rank28{
    top: 772px;
    left: 501px;
}

.rank29{
    top: 877px;
    left: 80px;
}

.rank30{
    top: 877px;
    left: 186px;
}

.rank31{
    top: 877px;
    left: 292px;
}

.rank32{
    top: 877px;
    left: 397px;
}

.rank33{
    top: 877px;
    left: 501px;
}

.rank34{
    top: 982px;
    left: 80px;
}

.rank35{
    top: 982px;
    left: 186px;
}

.rank36{
    top: 982px;
    left: 292px;
}

.rank37{
    top: 982px;
    left: 397px;
}

.rank38{
    top: 982px;
    left: 501px;
}

.rank39{
    top: 1087px;
    left: 80px;
}

.rank40{
    top: 1087px;
    left: 186px;
}

.rank41{
    top: 1087px;
    left: 292px;
}

.rank42{
    top: 1087px;
    left: 397px;
}

.rank43{
    top: 1087px;
    left: 501px;
}

.rank44{
    top: 1192px;
    left: 80px;
}

.rank45{
    top: 1192px;
    left: 186px;
}

.rank46{
    top: 1192px;
    left: 292px;
}

.rank47{
    top: 1192px;
    left: 397px;
}

.rank48{
    top: 1192px;
    left: 501px;
}

.rank49{
    top: 1297px;
    left: 80px;
}

.rank50{
    top: 1297px;
    left: 186px;
}

.lastrank1{
    top: 2600px;
    left: 276px;
}

.lastrank1name {
    top: 2730px;
    left: 298px;
    width: 118px;
    white-space: nowrap;
    font-weight: bold;
}

.lastrank1clan{
    top: 2750px;
    left: 298px;
    width: 118px;
    white-space: nowrap;
    font-weight: bold;
    font-size: 12px;
}

.lastrank2{
    top: 2705px;
    left: 102px;
}

.lastrank2name {
    top: 2830px;
    left: 125px;
    width: 118px;
    white-space: nowrap;
    font-weight: bold;
}

.lastrank2clan{
    top: 2855px;
    left: 125px;
    width: 118px;
    white-space: nowrap;
    font-weight: bold;
    font-size: 12px;
}

.lastrank3{
    top: 2760px;
    left: 447px;
}

.lastrank3name {
    top: 2890px;
    left: 467px;
    width: 118px;
    white-space: nowrap;
    font-weight: bold;
}

.lastrank3clan{
    top: 2910px;
    left: 467px;
    width: 118px;
    white-space: nowrap;
    font-weight: bold;
    font-size: 12px;
}
</style>
<img src="img/marketing/Rangliste.png" style="position: relative; z-index: 2;"/>
<?php
$Rank_Check = $database->Select('*', 'accounts', 'rank < 51', 50, 'rank');
if($Rank_Check)
{
    while($Rank_User = $Rank_Check->fetch_assoc())
    {
        $rankuser = new Player($database, $Rank_User['id']);
        if($rankuser->GetRank() == 1 || $rankuser->GetRank() == 2 || $rankuser->GetRank() == 3)
        {
            $size = "width='120' height='120'";
            echo "<div class='rank".$rankuser->GetRank()."name' style='position: absolute; z-index: 3;'><a href='?p=profil&id=".$rankuser->GetID()."'>".$rankuser->GetName()."</a></div>";
            if($rankuser->GetClanName() != '')
                echo "<div class='rank".$rankuser->GetRank()."clan' style='position: absolute; z-index: 3;'><a href='?p=clan&id=".$rankuser->GetClan()."'>".$rankuser->GetClanName()."</a></div>";
        }
        else
        {
            $size = "width='95' height='95' style='border-radius: 5px;'";
        }
        echo "<div class='rank".$rankuser->GetRank()."' style='position: absolute; z-index: 3;'><a href='?p=profil&id=".$rankuser->GetID()."' style='z-index: 10;'><img ".$size." src='".$rankuser->GetImage()."' alt='".$rankuser->GetName()."' title='".$rankuser->GetName()."'/></a></div>";
    }
}
echo "<div class='spacer'></div><img src='img/marketing/BelohnungenElo2.png' />";
?>
<div class="spacer"></div>
<img src="img/marketing/onepieceopwxreturnchamion.png" style="position: relative; z-index: 4;" /><br />
<?php
$lastcheck = $database->Select('*', 'accounts', 'last_elo_rank < 4', 3);
if($lastcheck)
{
    while($lasts = $lastcheck->fetch_assoc())
    {

        $size = "width='120' height='120'";
        echo "<div class='lastrank".$lasts['last_elo_rank']."name' style='position: absolute; z-index: 5;'><a href='?p=profil&id=".$lasts['id']."'>".$lasts['name']."</a></div>";
        if($lasts['clanname'] != '')
            echo "<div class='lastrank".$lasts['last_elo_rank']."clan' style='position: absolute; z-index: 5;'><a href='?p=clan&id=".$lasts['clan']."'>".$lasts['clanname']."</a></div>";
        echo "<div class='lastrank".$lasts['last_elo_rank']."' style='position: absolute; z-index: 5;'><a href='?p=profil&id=".$lasts['id']."' style='z-index: 11;'><img ".$size." src='".$lasts['charimage']."' alt='".$lasts['name']."' title='".$lasts['name']."'/></a></div>";
    }
}
?>
