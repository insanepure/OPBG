<?php
include_once $_SERVER['DOCUMENT_ROOT'].'classes/bbcode/bbcode.php';

$title = 'Bande beitreten';

if($player->GetClan() != 0)
{
    header('Location: ?p=news');
    exit();
}

$clanapplication = null;
if ($player->GetClanApplication() != 0)
{
  $clanapplication = new Clan($database, $player->GetClanApplication());
}

if (isset($_GET['a']) && $_GET['a'] == 'delete')
{
  $player->DeleteClanApplication();
  $clanapplication = null;
}
else if (isset($_GET['a']) && $_GET['a'] == 'join')
{
    if (!$player->IsVerified())
    {
        $message = 'Dein Charakter wurde noch von keinem Admin verifiziert.';
    }
    else if($player->IsMultiChar())
    {
        $message = "Dieser Charakter darf keiner Bande beitreten!";
    }
    else if ($player->GetLevel() < 5)
    {
        $message = 'Du musst mindestens Level 5 sein um einer Bande beizutreten.';
    }
  else if ($player->GetClan() != 0)
  {
    $message = 'Du bist bereits in einer Bande.';
  }
  else if (!isset($_POST['text']))
  {
    $message = 'Du hast keinen Text angegeben.';
  }
  else if (isset($_POST['clanname']))
  {
    $clanname = $database->EscapeString($_POST['clanname']);
    $clanID = Clan::FindByName($database, $clanname);
    if ($clanID == 0)
    {
      $message = 'Diese Bande gibt es nicht. ' . $clanname;
    }
    else
    {
      $applyClan = new Clan($database, $clanID);
      $player->SendClanApplication($applyClan, $_POST['text']);
      $clanapplication = $applyClan;
      $message = 'Du hast eine Beitrittsanfrage an die Bande ' . $applyClan->GetName() . ' geschickt.';
    }
  }
}
