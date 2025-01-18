<div class="spacer"></div>
<div class="catGradient borderT borderB" style="width:95%;">
    <b>Banden Suche</b>
</div>
<?php
$where = '';
$start = 0;
$limit = 30;
if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0)
{
    $start = $limit * ($_GET['page'] - 1);
}
if (isset($_GET['clanname']) && $_GET['clanname'] != '')
{
    $clanname = $database->EscapeString($_GET['clanname']);
    $where = 'name LIKE "%' . $clanname . '%"';
}
$clans = new Generallist($database, 'clans', '*', $where, 'rang',  $start . ',' . $limit, 'ASC');
?>
<form method="GET" action="?p=clans">
    <table width="95%" cellspacing="0" border="0" class="borderT borderR borderL borderB">
        <tr>
            <td> <input type="hidden" name="p" value="clans"></td>
            <td width="45%">
                <span style="text-align: center"><input style="width:100%;" type="text" placeholder="Name" name="clanname" value="<?php if (isset($_GET['clanname'])) echo $_GET['clanname']; ?>"></span>
            </td>
            <td width="45%">
                <center><input type="submit" style="width:50%" value="Suchen"></center>
            </td>
        </tr>
    </table>
</form>
<div class="spacer"></div>
<table width="95%" cellspacing="0" border="0">
    <tr>
        <td colspan=7 class="catGradient borderT borderB">
            <b>
                <span style="text-align: center;">
                    <span style="color: white;">
                        <div class="schatten">Banden</div>
                    </span>
                </span>
            </b>
        </td>
    </tr>
    <tr>
        <td width="10%"><b>Rang</b></td>
        <td width="15%"><b>Stärke</b></td>
        <td width="15%"><b>Logo</b></td>
        <td width="10%"><b>TAG</b></td>
        <td width="10%"><b>Territorien</b></td>
        <td width="30%"><b>Name</b></td>
        <td width="10%"><b>Mitglieder</b></td>
    </tr>
    <?php
    $id = 0;
    $entry = $clans->GetEntry($id);
    if($entry == null)
    {
        ?>
        <tr style="height: 20px;">
            <td></td>
        </tr>
        <tr>
            <td colspan="7" style="text-align: center;">Es wurden noch keine Banden gegründet.</td>
        </tr>
        <?php
    }
    while ($entry != null)
    {
        ?>
        <tr>
            <td><?php echo number_format($entry['rang'], '0', '', '.'); ?></td>
            <td><?php echo number_format($entry['memberki'], '0', '', '.'); ?></td>
            <td>
                <?php if ($entry['banner'] != '')
                {
                    ?>
                    <img src="<?php echo $entry['banner']; ?>" width="30px" height="30px">
                    <?php
                }
                ?>
            </td>
            <td>[<?php echo $entry['tag']; ?>]</td>
            <td>
                <?php
                $territorien = new GeneralList($database, 'places', 'territorium', 'territorium = ' . $entry['id']);
                echo number_format($territorien->GetCount(), 0, '', '.');
                ?>
            </td>
            <td><a href="?p=clan&id=<?php echo $entry['id']; ?>"><?php echo $entry['name']; ?></a></td>
            <td><?php echo number_format($entry['members'], '0', '', '.'); ?></td>
        </tr>
        <?php
        $id++;
        $entry = $clans->GetEntry($id);
    }
    ?>
</table>
<?php
$result = $database->Select('COUNT(id) as total', 'clans', $where);
$total = 0;
if ($result)
{
    $row = $result->fetch_assoc();
    $total = $row['total'];
    $result->close();
}
$pages = ceil($total / $limit);
if ($pages != 1)
{
    ?>
    <div class="spacer"></div>
    <?php
    $i = 0;
    while ($i != $pages)
    {
        ?>
        <a href="?p=clans&page=<?php echo $i + 1;
        if (isset($_GET['clanname'])) echo '&clanname=' . $_GET['clanname']; ?>">Seite <?php echo number_format($i + 1, '0', '', '.'); ?></a>
        <?php
        ++$i;
    }
}
?>
<br />
<hr>
<br />
<?php
echo "Folgende Banden haben heute alle Quests geschafft!<br />";
$BandenCheck = $database->Select('*', 'clans', 'npcquest=1 AND eloquest=1 AND kgquest=1 AND dungeonquest=1', 20);
while($bande = $BandenCheck->fetch_assoc())
{
    if($onlineListEs == '')
        $onlineListEs = "<a target='_blank' href='?p=clan&id=" . $bande['id'] . "'>" . $bande['name'] . "</a>";
    else
        $onlineListEs .= ", <a target='_blank' href='?p=clan&id=" . $bande['id'] . "'>" . $bande['name'] . "</a>";
}
echo $onlineListEs."<br /><br />Du sucht eine aktive Bande? Vielleicht ist hier was dabei für dich!";
?>
