<div class="spacer"></div>
<div class="catGradient borderT borderB" style="width:90%;">
    <h2>Interaktionen</h2>
</div>
<div class="spacer"></div>
<table style="width:95%;" cellspacing="0" border="0">
    <tr>
        <td colspan=4 height="20px">
        </td>
    </tr>
    <tr>
        <td colspan=4 class="catGradient borderT borderB" align="center">
            <b>
        <span style="color: white;">
          <div class="schatten">Suchen</div>
        </span>
            </b>
        </td>
    </tr>
    <tr class="boxSchatten">
        <td width="30%"><b> Text </b></td>
        <td width="20%"><b> Kategorie </b></td>
        <td width="30%"><b> Interaktion </b></td>
        <td width="20%"><b> Aktion </b></td>
    </tr>
    <tr>
        <form method="GET" action="?p=admininteractions">
            <input type="hidden" name="p" value="admininteractions">
            <td width="20%" class="boxSchatten">
                <input style="width:90%;" type="text" name="searchvalue" value="<?php if (isset($_GET['searchvalue'])) echo htmlentities($_GET['searchvalue']); ?>">
            </td>
            <td width="20%" class="boxSchatten">
                <select style="width:90%;" class="select" name="searchcategory">
                    <option value="Alle" <?php if (isset($_GET['searchcategory']) && $_GET['searchcategory'] == "Alle") echo 'selected'; ?>>Alle</option>
                    <option value="Name" <?php if (isset($_GET['searchcategory']) && $_GET['searchcategory'] == "Name") echo 'selected'; ?>>Name</option>
                </select>
            </td>
            <td width="30%" class="boxSchatten">
                <select style="width:90%;" class="select" name="searchtype">
                    <option value="Alle" <?php if (isset($_GET['searchtype']) && $_GET['searchtype'] == "Alle") echo 'selected'; ?>>Alle</option>
                    <option value="Marktkauf" <?php if (isset($_GET['searchtype']) && $_GET['searchtype'] == "Marktkauf") echo 'selected'; ?>>Marktplatz</option>
                    <option value="Storykampf" <?php if (isset($_GET['searchtype']) && $_GET['searchtype'] == "Storykampf") echo 'selected'; ?>>Storykampf</option>
                    <option value="PvP-Kampf" <?php if (isset($_GET['searchtype']) && $_GET['searchtype'] == "PvP-Kampf") echo 'selected'; ?>>PvP-Kampf</option>
                    <option value="Elokampf" <?php if (isset($_GET['searchtype']) && $_GET['searchtype'] == "Elokampf") echo 'selected'; ?>>Elokampf</option>
                </select>
            </td>
            <td width="30%" class="boxSchatten">
                <input type="submit" style="width:90%" value="Suchen">
            </td>
        </form>
    </tr>
</table>
<?php
$start = 0;
$limit = 30;
$timeOut = 30;
if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0)
{
    $start = $limit * ($_GET['page'] - 1);
}

$where = 'game="opwx"';
if (isset($_GET['searchvalue']) && $_GET['searchvalue'] != '' && $_GET['searchcategory'] == 'Name' || isset($_GET['searchtype']) && isset($_GET['searchvalue']) && $_GET['searchvalue'] != '')
{
    $playerAcc = new Generallist($database, 'accounts', 'id', 'name like "' . $_GET['searchvalue'] . '"', '', 1);
    if ($playerAcc)
    {
        $entry = $playerAcc->GetEntry(0);
        $where .= ' AND (charaids like "' . $entry['id'] . ';%" OR charaids like "%;' . $entry['id'] . '")';
    }
}
if(isset($_GET['searchtype']) && $_GET['searchtype'] != 'Alle')
{
    $where .= ' AND action like "%' . $_GET['searchtype'] . '%"';
}
$list = new Generallist($accountDB, 'interactions', '*', $where, 'ID', $start . ',' . $limit, 'DESC');

?>
<div class="spacer"></div>
<table width="95%" cellspacing="0" border="1">
    <tr class="catGradient borderT borderB">
        <td width="10%"><b>ID</b></td>
        <td width="20%"><b>Zeit</b></td>
        <td width="30%"><b>Charaktere</b></td>
        <td width="10%"><b>Multi</b></td>
        <td width="30%"><b>Aktion</b></td>
    </tr>
    <?php
    ////////// GET PLAYER IDS //////////
    $playerIDs = array();
    $id = 0;
    $entry = $list->GetEntry($id);
    while ($entry != null)
    {
        $charaIDs = explode(';', $entry['charaids']);
        $i = 0;
        while (isset($charaIDs[$i]))
        {
            array_push($playerIDs, $charaIDs[$i]);
            ++$i;
        }
        $id++;
        $entry = $list->GetEntry($id);
    }
    $playerIDs = array_unique($playerIDs);


    ////////// GET PLAYER DATAS //////////
    $i = 0;
    $limit2 = count($playerIDs);
    $where2 = '';
    foreach ($playerIDs as &$playerID)
    {
        if ($where2 == '')
        {
            $where2 = 'id = ' . $playerID . '';
        }
        else
        {
            $where2 = $where2 . ' OR id = ' . $playerID . '';
        }
    }
    $playerAccs = new Generallist($database, 'accounts', 'id, name, userid', $where2, '', $limit2, 'ASC');

    $pNames = array();
    $pMains = array();

    foreach ($playerIDs as &$playerID)
    {
        $j = 0;
        $playerEntry = $playerAccs->GetEntry($j);
        while ($playerEntry != null)
        {
            if ($playerEntry['id'] == $playerID)
            {
                $pNames[$playerID] = $playerEntry['name'];
                $pMains[$playerID] = $playerEntry['userid'];
                break;
            }
            ++$j;
            $playerEntry = $playerAccs->GetEntry($j);
        }
    }

    $id = 0;
    $entry = $list->GetEntry($id);
    while ($entry != null)
    {
        $dateStr = strtotime($entry['time']);
        $formatedDate = date('d.m.Y H:i', $dateStr);

        $multi = false;
        $charaIDs = explode(';', $entry['charaids']);
        $i = 0;
        $multiMain = 0;
        $playersString = '';
        while (isset($charaIDs[$i]))
        {
            $charaID = $charaIDs[$i];
            $pName = $pNames[$charaID];
            $pMain = $pMain[$charaID];

            if ($multiMain == 0)
                $multiMain = $pMain;
            else if ($pMain == $multiMain)
                $multi = true;
            if ($pName == '')
                $pName = 'ID: ' . $charaID . ' (Gelöscht)';
            $playersString .= '<a href="?p=profil&id=' . $charaID . '">' . $pName . '</a><br />';
            ++$i;
        }
        ?>
        <tr>
            <td><?php echo number_format($entry['id'], '0', '', '.'); ?></td>
            <td><?php echo $formatedDate; ?></td>
            <td>
                <?php
                echo $playersString;
                ?>
            </td>
            <td><?php if ($multi)
                {
                    echo '<span "color: red;">Ja</span>';
                }
                else
                {
                    echo '<span "color: green;">Nein</span>';
                } ?></td>
            <td><?php echo $entry['action']; ?></td>
        </tr>
        <?php
        $id++;
        $entry = $list->GetEntry($id);
    }
    ?>
</table>
<br />
<br />
<?php
$result = $accountDB->Select('COUNT(id) as total', 'interactions', $where);
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
        $add = $i + 1;
        if (isset($_GET['searchvalue']) && $_GET['searchvalue'] != '' && $_GET['searchcategory'] != 'Alle')
            $add .= '&searchvalue=' . $_GET['searchvalue'] . '&searchcategory=' . $_GET['searchcategory'];
        if(isset($_GET['searchtype']))
            $add .= '&searchtype='.$_GET['searchtype'];
        ?>
        <a href="?p=admininteractions&page=<?php echo $add; ?>">Seite <?php echo $i + 1; ?></a>
        <?php
        ++$i;
    }
}
?>