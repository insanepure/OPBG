<div class="spacer2"></div>
<form method="POST" action="?p=rundmail&a=send">
  <select class="select" name="sender" style="min-width:200px;max-width:200px;width:200px;">
    <option value="System">System</option>
    <option value="<?php echo $player->GetName(); ?>"><?php echo $player->GetName(); ?></option>
  </select>
  <div class="spacer"></div>
  <select class="select" name="title" style="min-width:200px;max-width:200px;width:200px;">
    <option value="Ankündigung">Ankündigung</option>
    <option value="Bugfix">Bugfix</option>
    <option value="Event">Event</option>
    <option value="Neuerungen">Neuerungen</option>
    <option value="Turnier">Turnier</option>
    <option value="Regel Update">Regel Update</option>
    <option value="Sonstiges">Sonstiges</option>
  </select>

  <div class="spacer"></div>
  <textarea class="textfield" name="text" maxlength="300000" style="width:400px; height:200px; resize: vertical;"></textarea>
  <div class="spacer"></div>
  <input type="submit" value="Senden">
</form>