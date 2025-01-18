<?php
include_once $_SERVER['DOCUMENT_ROOT'].'classes/bbcode/bbcode.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/news/newsmanager.php';
$newsManager = new NewsManager($database, 5);
if (isset($_GET['a']) && $_GET['a'] == 'revive')
{
    $message = 'Du wurdest befreit.';
    ?>
    <script>
        window.location.href = '?p=news';
    </script>
    <?php
}

if (isset($_GET['a']) && $_GET['a'] == 'like' && isset($_GET['id']) && is_numeric($_GET['id']))
{
  $newsManager->Like($player->GetName(), $_GET['id']);
}
else if (isset($_GET['a']) && $_GET['a'] == 'dislike' && isset($_GET['id']) && is_numeric($_GET['id']))
{
  $newsManager->DisLike($player->GetName(), $_GET['id']);
}
else if (isset($_GET['a']) && $_GET['a'] == 'removelikes' && isset($_GET['id']) && is_numeric($_GET['id']))
{
  $newsManager->RemoveLikes($player->GetName(), $_GET['id']);
}
else if (isset($_GET['a']) && $_GET['a'] == 'delete' && isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['comment']) && is_numeric($_GET['comment']))
{
    if($player->GetArank() < 2)
    {
        $message = 'Du hast nicht die nötigen Rechte!';
    }
    else
    {
        if($newsManager->RemoveComment($_GET['id'], $_GET['comment']))
        {
            $message = 'Du hast den Kommentar gelöscht.';
        }
        else
        {
            $message = 'Der Kommentar konnte nicht gelöscht werden.';
        }
    }
}
else if (isset($_GET['a']) && $_GET['a'] == 'post')
{

  if (isset($_GET['id']) && is_numeric($_GET['id']) && isset($_POST['text']) && $_POST['text'] != '')
  {
    $hasBadWords = 0;
    $text = $database->EscapeString($_POST['text']);
    if ($database->HasBadWords($text))
    {
      $message = 'Der Text enthält ungültige Wörter.';
    }
    else
    {
      $return = $newsManager->Post($player, $_GET['id'], $text);
      if ($return == 0)
      {
        $message = 'Die News wurde nicht gefunden.';
      }
      else if ($return == -1)
      {
        $message = 'Der Text darf nur aus Buchstaben und Zahlen bestehen.';
      }
      else
      {
        $message = 'Du hast einen Kommentar gepostet.';
      }
    }
  }
}
