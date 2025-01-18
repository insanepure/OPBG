<div class="spacer"></div>
<h2>Bande Beitreten</h2>
<?php
$clans = new Generallist($database, 'clans', '*', '', '', 999999, 'ASC');
?>
<form method="POST" action="?p=clanjoin&a=join">
  <select class="select" name="clanname">
    <?php
    $id = 0;
    $entry = $clans->GetEntry($id);
    while ($entry != null)
    {
      $displayClan = new Clan($database, $entry['id']);
      $leader = new Player($database, $displayClan->GetLeader());
      if ($leader->IsValid() && $leader->IsBanned() || $displayClan->GetMembers() >= $displayClan->GetMaxMembers())
      {
        ++$id;
        $entry = $clans->GetEntry($id);
        continue;
      }

      echo "<option>" . $displayClan->GetName() . "</option>";
      ++$id;
      $entry = $clans->GetEntry($id);
    }
    ?>

  </select>
  <div class="spacer"></div>
  <textarea class="textfield" name="text" maxlength="300000" style="width:500px; height:200px; resize: vertical;"></textarea>
  <div class="spacer"></div>
  <input type="submit" value="Beitreten">
</form>
<?php
if ($clanapplication != null)
{

?>
  <div class="spacer2"></div>
  Du hast eine Beitrittsanfrage an die Bande <b><a href="?p=clan&id=<?php echo $clanapplication->GetID(); ?>">[<?php echo $clanapplication->GetTag(); ?>] <?php echo $clanapplication->GetName(); ?></a></b> gesendet.<br />
  Möchtest du die Anfrage zurücknehmen?<br />
  <div class="spacer"></div>
  <form method="POST" action="?p=clanjoin&a=delete">
    <input type="submit" value="Zurücknehmen">
  </form>
  <div class="spacer2"></div>
  Dein Text lautet:
  <div class="spacer2"></div>
<?php
  echo $bbcode->parse($player->GetClanApplicationText());
}
?>