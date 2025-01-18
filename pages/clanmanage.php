<?php

include_once '../classes/header.php';
if(isset($_GET['delete']))
{
    ?>
    <div class="tooltip" style="position: relative; top:0; left:0;">
        <button type="button" onclick="RemoveTableRow(this);">X</button>
        <span class="tooltiptext" style="width:180px; top:-30px; left:-70px;">Rang löschen?</span>
    </div>
    <?php
}
$row = $_GET['row'];
if($_GET['cell'] == 0)
{
    ?><input type="text" name="rankname[<?php echo $row; ?>]" placeholder="Rangname" style="height:20px; width: 80px; padding: 5px;"><?php
}
else if($_GET['cell'] == 1)
{
    ?><input type="checkbox" name="canseesk[<?php echo $row; ?>]"><?php
}
else if($_GET['cell'] == 2)
{
    ?><input type="checkbox" name="canchangesk[<?php echo $row; ?>]"><?php
}
else if($_GET['cell'] == 3)
{
    ?><input type="checkbox" name="candorm[<?php echo $row; ?>]"><?php
}
else if($_GET['cell'] == 4)
{
    ?><input type="checkbox" name="canchangepro[<?php echo $row; ?>]"><?php
}
else if($_GET['cell'] == 5)
{
    ?><input type="checkbox" name="canseeintern[<?php echo $row; ?>]"><?php
}
else if($_GET['cell'] == 6)
{
    ?><input type="checkbox" name="candofights[<?php echo $row; ?>]"><?php
}
else if($_GET['cell'] == 7)
{
    ?><input type="checkbox" name="candomanagement[<?php echo $row; ?>]"><?php
}
?>