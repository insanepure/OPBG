<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
include_once '../../../classes/header.php';
if (true)
{
  exit();
}

if (!$player->IsLogged())
{
  echo 'Du bist nicht eingeloggt.';
  exit();
}

if ($_GET['id'] == $player->GetID())
{
  echo 'Du kannst kein Sparring mit dir selber betreiben.';
  exit();
}
if ($player->GetARank() > 0)
{
  echo 'Als Admin kannst du kein Sparring machen.';
  exit();
}
$otherPlayer = new Player($database, $_GET['id'], $actionManager);
if (!$otherPlayer->IsValid())
{
  exit();
}
if ($player->GetRace() != $otherPlayer->GetRace())
{
  echo 'Du kannst als ' . $player->GetRace() . ' kein Sparring mit einem Spieler der Gegenseite machen.';
  exit();
}
?>
<div style="height:225px;">
  <div class="spacer"></div>

  <div class="bplayer1">
    <div class="bplayer1name smallBG">
        <b><?php echo $player->GetName(); ?></b>
        <img class="bplayer1image" src="<?php echo $player->GetImage(); ?>">
    </div>
  </div>
  <div class="bplayer2">
    <div class="bplayer2name smallBG">
        <b><?php echo $otherPlayer->GetName(); ?></b>
        <img class="bplayer2image" src="<?php echo $otherPlayer->GetImage(); ?>">
    </div>
  </div>

  <div class="bplayerkampf boxSchatten borderB borderR borderT borderL">
    <form method="POST" action="?p=profil&id=<?php echo $_GET['id']; ?>&a=sparring">
      <select class="select" style="width:200px" name="hours">
        <?php
        $statsWin = Player::CalculateSparringWin($database, $player, $otherPlayer);
        $hours = 1;
        $maxHours = 24 * 7;
        //$berrys = 100;
        while ($hours <= $maxHours)
        {
          if ($hours == 1)
          {
            $time = $hours . " Stunde";
          }
          else
          {
            $time = number_format($hours,'0', '', '.') . " Stunden";
          }
        ?><option value="<?php echo $hours; ?>"><?php echo $time; ?></option><?php
                                                                              $hours++;
                                                                            }
                                                                              ?>
      </select>
      <input type="submit" class="ja" value="Starten">
    </form>
  </div>
  <div class="spacer"></div>
</div>
<?php
?>
Gewinn pro Stunde bei gleichbleibender Douriki: <b><?php echo number_format($statsWin,'0', '', '.'); ?></b> Stats.
<div class="spacer2"></div>

<fieldset>
  <legend><b>Was ist Sparring:</b></legend>
  <table>
    <tr>
      <td> Sparring ist eine Möglichkeit für schwächere Spieler, durch gemeinsames Training stärker zu werden und die stärkeren Spieler einzuholen.</td>
    </tr>
    <tr>
      <td>Du erhältst pro Stunde Douriki, abhängig von der Douriki des stärksten Spieler.</td>
    </tr>
    <tr>
      <td>Die Douriki des stärksten Spieler ist abzüglich der Kopfgeld- und abzüglich der Level-Stats berechnet.</td>
    </tr>
    <tr>
      <td>Wenn du das Sparring länger als eine Stunde setzt, wird die gewonnene Douriki für jede Stunde neu berechnet.</td>
    </tr>
    <tr>
      <td>Die Formel lautet: (Top Douriki - Deine Douriki), maximal 100 (oder 10% der Douriki des Top Spielers), * 0.015 = Gewonnene Stats</td>
    </tr>
    <tr>
      <td>Beispiel: (300-100)=200, maximal 100, also 100*0.015 = 1.5 Stats, gerundet = 2 Stats</td>
    </tr>
    <tr>
      <td>Beispiel2: (333-150)=183*0.015 = 2,745, gerundet = 3 Stats</td>
    </tr>
  </table>
</fieldset>