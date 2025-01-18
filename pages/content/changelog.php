<?php
    include_once 'classes/bbcode/bbcode.php';
    ?>
<div class="spacer"></div>
<table width="100%" cellspacing="0">
    <tr>
        <td class="catGradient borderB borderT" colspan="2" align="center">
            <h2>
                Patchnotes
            </h2>
        </td>
    </tr>
</table>
<div class="spacer"></div>
<table width="95%" cellspacing="0">
<?php
    $start = 0;
    $limit = 30;
    if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0)
    {
        $start = $limit * ($_GET['page']-1);
    }
$changelog = new Generallist($database, 'changelog', '*', '', 'time', $start.','.$limit, 'DESC');
$id = 0;
$entry = $changelog->GetEntry($id);
while ($entry != null)
{
  $dateStr = strtotime($entry['time']);
  $formatedDate = date('d.m.Y H:i', $dateStr);
?>
<tr style="line-height: 20px;">
    <td class="boxSchatten" align="center" width="150"><?php echo $formatedDate; ?></td>
    <td class="boxSchatten" align="center"><hr><br /><?php echo $bbcode->parse($entry['text']); ?><br /><hr></td>
</tr>
<?php
  ++$id;
  $entry = $changelog->GetEntry($id);
}
?>
</table>
<?php
    $result = $database->Select('COUNT(id) as total','changelog',1);
    $total = 0;
    if ($result)
    {
        $row = $result->fetch_assoc();
        $total = $row['total'];
        $result->close();
    }
    $pages = ceil($total / $limit);
    if($pages != 1)
    {
        ?>
        <div class="spacer"></div>
        <?php
        $i = 0;
        while($i != $pages)
        {
            ?>
            <a href="?p=changelog&page=<?php echo $i+1;?>">Seite <?php echo number_format($i+1,0,'','.'); ?></a>
            <?php
            ++$i;
        }
    }