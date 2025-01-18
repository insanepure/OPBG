<?php
$title = 'Bandenerstellung';
if (isset($_GET['a']) && $_GET['a'] == 'create')
{
  $cost = 2000;
  if ($player->GetClan() != 0)
  {
    $message = 'Du bist bereits in einer Bande.';
  }
  else if ($player->GetClanApplication() != 0)
  {
    $message = 'Du hast noch eine offene Banden-Beitrittsanfrage.';
  }
  else if ($player->GetBerry() < $cost)
  {
    $message = 'Du hast nicht genügend Berry.';
  }
  else if($player->IsMultiChar())
  {
      $message = "Du darfst mit diesem Char keiner Bande beitreten";
  }
  else if (isset($_POST['name']) && isset($_POST['tag']))
  {
    $name = $database->EscapeString($_POST['name']);
    $tag = $database->EscapeString($_POST['tag']);

    if ($database->HasBadWords($name))
    {
      $message = 'Der Name enthält ungültige Wörter.';
    }
    else if ($database->HasBadWords($tag))
    {
      $message = 'Der Tag enthält ungültige Wörter.';
    }
    else if (!$player->IsVerified())
    {
        $message = 'Dein Charakter wurde noch von keinem Admin verifiziert.';
    }
    else if ($player->GetLevel() < 5)
    {
      $message = 'Du musst mindestens Level 5 sein um eine Bande zu gründen.';
    }
    else if ($name == '')
    {
      $message = 'Du hast keinen Namen angegeben.';
    }
    else if ($name == 'Clanlos')
    {
      $message = 'Der Name ist ungültig.';
    }
    else if ($name == 'Bandenlos')
    {
        $message = 'Der Name ist ungültig.';
    }
    else if ($tag == '')
    {
      $message = 'Du hast keinen Tag angegeben.';
    }
    else if (!preg_match("/^[a-zA-Z0-9öäüÖÄÜß ]+$/", $name))
    {
      $message = 'Der Name darf nur aus Buchstaben und Zahlen bestehen.';
    }
    else if (!preg_match("/^[a-zA-Z0-9öäüÖÄÜß]+$/", $tag))
    {
      $message = 'Der Tag darf nur aus Buchstaben und Zahlen bestehen.';
    }
    else
    {
      if (substr($name, 0, 1) === ' ')
      {
        $name = ltrim($name, $name[0]);
      }
      if (!CreateClan($database, $name, $tag, $player))
      {
        $message = 'Der Name oder der Tag ist schon vergeben.';
      }
      else
      {
        $player->RemoveBerry($cost);
        header('Location: ?p=clanmanage');
        exit();
      }
    }
  }
}
