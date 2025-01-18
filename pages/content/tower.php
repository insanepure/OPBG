<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/tower/tower.php';
?>
<div class="spacer"></div>
<div class="catGradient borderB borderT" style="width:500px">
  Turm
</div>
<div class="borderL borderR borderB" style="width:500px; text-align:left;">
  <img src="https://image.jimcdn.com/app/cms/image/transf/dimension=300x1024:format=png/path/s98ac4cdb8680edc6/image/i6335889f25a78129/version/1353864207/image.png" width="500px" height="400px"></img>
  <div class="spacer"></div>
  <center>
  <form method="POST" action="?p=tower&a=start">
    <?php
  $towerfloors = new Generallist($database, 'towerfloors', '*', '', '', 999999, 'ASC');
  ?>
 Floor<br /> <select class="select" name="towerfloor" size="1">
  <?php
  $i = 0;
      for($i = $player->GetTowerFloor(); $i >= 1; $i--)
      {
        echo "<option>".$i."</option>";
      }
  ?>
  </select><br/><br />
    <button>
      Beginnen
    </button>
</form>
<div class="spacer"></div>
</center>
</div>