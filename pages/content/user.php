<div class="spacer"></div>
<div class="catGradient borderT borderB" style="width:95%;">
    <b>User Suche</b>
</div>
<?php
$start = 0;
$limit = 30;
$timeOut = 30;
if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0)
{
    $start = $limit * ($_GET['page'] - 1);
}

$where = 'banned = 0 AND deleted = 0';
$sortQ = 'rank';
$desc = 'ASC';
if (isset($_GET['user']) && $_GET['user'] != '')
{
    $username = $database->EscapeString($_GET['user']);

    if ($where != '')
        $where = $where . ' AND ';
    $where = 'name LIKE "%' . $username . '%"';
}
if (isset($_GET['race']) && $_GET['race'] != '')
{
    $race = $database->EscapeString($_GET['race']);

    if ($where != '')
        $where = $where . ' AND ';
    $where = $where . 'race LIKE "%' . $race . '%"';
}
if (isset($_GET['sort']) && $_GET['sort'] != '')
{
    $sort = $database->EscapeString($_GET['sort']);
    $sortQ = $sort;

    if($sort != 'rank' && $sort != 'id')
        $desc = 'DESC';

}

$sortQ = 'arank ASC, ' . $sortQ;

$list = new Generallist($database, 'accounts', 'id,name,rank,race,level,titel,arank,clan,clanname,banned,deleted, fakeki, kopfgeld,(((mlp/10)+(mkp/10)+(attack/2)+defense)/4) as douriki', $where, $sortQ, $start . ',' . $limit, $desc);

?>

<form method="GET" action="?p=user">
    <table width="95%" cellspacing="0" border="0" class="borderT borderR borderL borderB">
        <tr>
            <input type="hidden" name="p" value="user">
            <td width="40%" align="left"><input style="width:90%;" type="text" placeholder="Name" name="user" value="<?php if (isset($_GET['user'])) echo $_GET['user']; ?>"></td>
            <td width="20%" class="boxSchatten">
                <select style="width: auto;" class="select" name="race" id="racelist">
                    <option value="" <?php if (isset($_GET['race']) && $_GET['race'] == '') echo 'selected'; ?>>Alle</option>
                    <option value="Pirat" <?php if (isset($_GET['race']) && $_GET['race'] == 'Pirat') echo 'selected'; ?>>Pirat</option>
                    <option value="Marine" <?php if (isset($_GET['race']) && $_GET['race'] == 'Marine') echo 'selected'; ?>>Marine</option>
                </select>
            </td>
            <td width="20%">
                <select style="width: auto;" class="select" name="sort">
                    <option value="rank" <?php if ($sort == 'rank') echo 'selected'; ?>>Rang</option>
                    <option value="douriki" <?php if ($sort == 'douriki') echo 'selected'; ?>>Douriki</option>
                    <option value="level" <?php if ($sort == 'level') echo 'selected'; ?>>Level</option>
                    <option value="kopfgeld" <?php if ($sort == 'kopfgeld') echo 'selected'; ?>>Kopfgeld</option>
                    <?php if($player->GetArank() >= 2)
                    {
                        ?>
                        <option value="id" <?php if($sort == 'id') echo 'selected'; ?>>UserID</option>
                        <?php
                    }
                    ?>
                </select>
            </td>
            <td width="20%">
                <center> <input type="submit" style="width:100%" value="Suchen"></center>
            </td>
        </tr>
    </table>
</form>

<div class="spacer"></div>
<table width="95%" cellspacing="0" border="0">
    <tr class="catGradient borderT borderB">
        <td width="5%"><b>Rang</b></td>
        <td width="30%"><b>User</b></td>
        <td width="5%"><b>Level</b></td>
        <td width="10%"><b>Douriki</b></td>
        <td width="10%"><b>Kopfgeld</b></td>
        <td width="10%"><b>Fraktion</b></td>
        <td width="15%"><b>Bande</b></td>
    </tr>
    <?php

    if (!isset($titelManager)) $titelManager = new TitelManager($database);

    $id = 0;
    $entry = $list->GetEntry($id);
    while ($entry != null)
    {
        $ans = 0;
        if($entry['fakeki'] > 0)
        {
            $ans = $entry['fakeki'];
        }
        else
        {
            $ans = $entry['douriki'];
        }
        $titel = $titelManager->GetTitel($entry['titel']);
        $titelText = '';
        if ($titel != null)
        {
            $titelText = $titel->GetName();
            if ($titel->GetColor() != '')
            {
                $titelText = '<span style="color: #' . $titel->GetColor() . '">' . $titelText . '</span>';
            }
        }
        ?>
        <tr>
            <td width="5%"><?php echo number_format($entry['rank'],'0', '', '.'); ?></td>
            <?php
            if($entry['id'] == 934 && $entry['titel'] == 110)
            {
                echo "<td width='30%'><a href='?p=profil&id=" . $entry['id'] . "'>" . $entry['name'] . " " . $titelText . "</a></td>";
            }
            else
            {
                echo "<td width='30%'><a href='?p=profil&id=" . $entry['id'] . "'>" . $titelText . " " . $entry['name'] . "</a></td>";
            }
            ?>
            <td width="10%"><?php echo number_format($entry['level'],'0', '', '.'); ?></td>
            <td width="10%"><?php echo number_format($ans, '0', '', '.'); ?></td>
            <td width="10%"><?php echo number_format($entry['kopfgeld'], '0', '', '.'); ?></td>
            <td width="10%"><?php echo $entry['race']; ?></td>
            <td width="15%">
                <?php
                if ($entry['clan'] == 0)
                {
                    ?><b>Bandenlos</b><?php
                }
                else
                {
                    ?><a href="?p=clan&id=<?php echo $entry['clan']; ?>"><?php echo $entry['clanname']; ?></a><?php
                }
                ?></td>
        </tr>
        <?php
        $id++;
        $entry = $list->GetEntry($id);
    }
    ?>
</table>
<?php
$result = $database->Select('COUNT(id) as total', 'accounts', $where);
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
        <a href="?p=user&page=<?php echo $i + 1;
        if (isset($_GET['race'])) echo '&race=' . $_GET['race'];
        if (isset($_GET['user'])) echo '&user=' . $_GET['user'];
        if (isset($_GET['sort'])) echo '&sort=' . $_GET['sort'];

        ?> ">Seite <?php echo number_format($i + 1,'0', '', '.'); ?></a>
        <?php
        ++$i;
    }
}
?>