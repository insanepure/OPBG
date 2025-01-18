<div class="spacer"></div>
<table width="98%" cellspacing="0" border="1" class="borderB borderR borderL">
    <tr>
        <td class="catGradient borderB borderT" colspan="3" align="center"><b>User Meldungen</b></td>
    </tr>
    <tr>
        <td width="10%" style="text-align:center">
            <b>Zeit</b>
        </td>
        <td width="60%" style="text-align:center">
            <b>Text</b>
        </td>
        <td width="30%" style="text-align:center">
            <b>Aktion</b>
        </td>
    </tr>
    <?php
        $start = 0;
        $limit = 30;
        $timeOut = 30;
        if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0)
        {
            $start = $limit * ($_GET['page'] - 1);
        }
        $reports = new Generallist($database, 'meldungen', '*', 'status=0', 'id', $start . ',' . $limit, 'DESC');
        $id = 0;
        $entry = $reports->GetEntry($id);
        while ($entry != null)
        {
            $result = $database->Select('id', 'accounts', 'id = '.$entry['receiver'], 1);
            $otherPlayer = false;
            if ($result)
            {
                if ($result->num_rows > 0)
                {
                    $row = $result->fetch_assoc();
                    $otherPlayer = new Player($database, $row['id']);
                    $result->close();
                }
            }
            if ($otherPlayer && $otherPlayer->isValid())
            {
                if(!$otherPlayer->IsBanned() && $entry['type'] == 1 || $entry['type'] != 1)
                {
                    ?>
                    <tr>
                        <td>
                            <?php echo date('d.m.Y H:i:s', strtotime($entry['date'])); ?>
                        </td>
                        <td>
                            <?php echo $entry['text']; ?>
                        </td>
                        <td>
                            <?php
                                if ($entry['type'] == 1)
                                {
                                    ?>
                                    <a href='?p=report&id=<?php echo $entry['id']; ?>&a=verify&u=<?php echo $otherPlayer->GetID(); ?>'>Verifizieren</a>,
                                    <a href='?p=adminmod&user=<?php echo $otherPlayer->GetID(); ?>&report=<?php echo $entry['id']; ?>'>Sperren</a>,
                                    <a href='?p=multicheck&chara=<?php echo $otherPlayer->GetName(); ?>&score=50' target='_blank'>MultiCheck</a>
                                    <?php
                                }
                                else if($entry['type'] == 2)
                            {
                                ?>
                                <a href='?p=report&id=<?= $entry['id'] ?>&a=close'>Bestätigen</a>
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                }
            }
            $id++;
            $entry = $reports->GetEntry($id);
        }
    ?>
</table>
<?php
$result = $database->Select('COUNT(id) as total', 'meldungen', 'status=0');
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
        <a href="?p=report&page=<?php echo $i + 1;?>">Seite <?php echo number_format($i + 1,'0', '', '.'); ?></a>
        <?php
        ++$i;
    }
}
if ($reports->GetCount() == 0)
{
	echo "<br />";
	echo "Derzeit sind keine Meldungen vorhanden.";
}
?>