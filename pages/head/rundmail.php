<?php
if (!isset($player) || !$player->IsValid() || $player->GetArank() < 1)
{
  header('Location: ?p=news');
  exit();
}
if (isset($_GET['a']) && $_GET['a'] == 'send')
{
  $sender = $_POST['sender'];
  $senderID = 0;
  if ($sender == 'System')
  {
    $image = "img/system.png";
  }
  else
  {
    $image = $player->GetImage();
    $senderID = $player->GetID();
  }
  $text = $_POST['text'];
  $title = $_POST['title'];
  $PMManager->SendPMToAll($senderID, $image, $sender, $title, $text);
  $message2 = '@everyone ```' . chr(10) . 'Autor:' . $sender . chr(10) . 'Titel:' . $title . chr(10) . 'Nachricht:' . chr(10) . $text . '```';
  //postToDiscord($message2);
}
