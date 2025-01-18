<img width='100%' height='300' src='<?php echo $serverUrl; ?>img/marketing/befreiung.png' />
<div class="catGradient borderB borderT"><b>Befreiung</b></div>
<div class="spacer"></div>
<?php
if ($reviveTime == 0 && $player->GetRace() == "Pirat")
{
?>Du hast deine Zeit abgesessen.<br />
Möchtest du entlassen werden?<br /><br />
<form method="POST" action="?p=profil&a=revive">
  <input type="submit" value="Ja">
</form>
<?php
}
else if($reviveTime == 0 || $player->GetReviveDays() > 0 || $reviveTime != 0 && ($player->GetRace() == "Marine"))
{
    $price = ceil($player->GetPvP() / 2);
    ?>
    Du kannst dich jetzt für <?= number_format($price, 0, '', '.'); ?> Berry befreien
    <br />
    <br />
    <form method="POST" action="?p=profil&a=revive">
        <input type="submit" value="Ja">
    </form>
    <br />
    oder noch <br /><br />
    <?php
    $timeDiffMinutes = $reviveTime / 60;
    $timeDiffHours = $timeDiffMinutes / 60;
    $timeDiffDays = $timeDiffHours / 24;
    if ($reviveTime < 60)
    {
        $timeDiffSeconds = floor($reviveTime);
        echo number_format($timeDiffSeconds,'0', '', '.');
        if ($timeDiffSeconds == 1) echo ' Sekunde';
        else echo ' Sekunden';
    }
    else if ($timeDiffMinutes < 60)
    {
        $timeDiffMinutes = floor($timeDiffMinutes);
        echo number_format($timeDiffMinutes,'0', '', '.');
        if ($timeDiffMinutes == 1) echo ' Minute';
        else echo ' Minuten';
    }
    else if ($timeDiffHours < 24)
    {
        $timeDiffHours = floor($timeDiffHours);
        echo number_format($timeDiffHours,'0', '', '.');
        if ($timeDiffHours == 1) echo ' Stunde';
        else echo ' Stunden';
    }
    else
    {
        $timeDiffDays = floor($timeDiffDays);
        echo number_format($timeDiffDays,'0', '', '.');
        if ($timeDiffDays == 1) echo ' Tag';
        else echo ' Tage';
    }
    ?>
    <br />
    <b>Warten</b>
    <?php
}
else
{
?> Deine Zeit ist noch nicht soweit.<br />
  Du musst noch <b>
    <?php
    $timeDiffMinutes = $reviveTime / 60;
    $timeDiffHours = $timeDiffMinutes / 60;
    $timeDiffDays = $timeDiffHours / 24;
    if ($reviveTime < 60)
    {
      $timeDiffSeconds = floor($reviveTime);
      echo number_format($timeDiffSeconds,'0', '', '.');
      if ($timeDiffSeconds == 1) echo ' Sekunde';
      else echo ' Sekunden';
    }
    else if ($timeDiffMinutes < 60)
    {
      $timeDiffMinutes = floor($timeDiffMinutes);
      echo number_format($timeDiffMinutes,'0', '', '.');
      if ($timeDiffMinutes == 1) echo ' Minute';
      else echo ' Minuten';
    }
    else if ($timeDiffHours < 24)
    {
      $timeDiffHours = floor($timeDiffHours);
      echo number_format($timeDiffHours,'0', '', '.');
      if ($timeDiffHours == 1) echo ' Stunde';
      else echo ' Stunden';
    }
    else
    {
      $timeDiffDays = floor($timeDiffDays);
      echo number_format($timeDiffDays,'0', '', '.');
      if ($timeDiffDays == 1) echo ' Tag';
      else echo ' Tage';
    }
    ?></b>
    <br />
    <br />
  warten.<br />
<?php
}
?>