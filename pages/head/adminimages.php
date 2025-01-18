<?php

    $title = 'Bilderverwaltung';
if (!isset($player) || !$player->IsValid() || $player->GetArank() < 2)
{
  header('Location: ?p=news');
  exit();
}

function AddToLog($database, $ip, $accs, $log)
{
  $timestamp = date('Y-m-d H:i:s');
  $insert = '"' . $ip . '","' . $accs . '","' . $database->EscapeString($log) . '","' . $timestamp . '"';
  $result = $database->Insert('ip,accounts,log,time', $insert, 'adminlog');
}

$ip = $account->GetIP();
$accs = $player->GetName() . ' (' . $player->GetID() . ')';
$log = '';


if (isset($_GET['a']) && $_GET['a'] == 'delete')
{
  $path = 'img/' . $_GET['directory'] . '/';
  $fileWithPath = $path . $_POST['file'];
  if (!unlink($fileWithPath))
  {
    $message = 'Das Bild ' . $_POST['file'] . ' in ' . $path . ' konnte nicht gelöscht werden.';
  }
  else
  {
    $message = 'Das Bild ' . $_POST['file'] . ' in ' . $path . ' wurde gelöscht.';

    $log = 'Das Bild <b>' . $_POST['file'] . '</b> in <b>' . $path . '</b> wurde gelöscht.';
    AddToLog($database, $ip, $accs, $log);
  }
}
else if (isset($_GET['a']) && $_GET['a'] == 'upload' && isset($_GET['directory']))
{
  if (isset($_FILES['file_upload']) && $_FILES['file_upload']['size'] != 0)
  {
      $path = 'img/'.$_GET['directory'].'/';
      $imgHandler = new ImageHandler($path);
      $uploadedimages = array();

      $result = array();
      foreach($_FILES as $name => $fileArray) {
          if (is_array($fileArray['name'])) {
              foreach ($fileArray as $attrib => $list) {
                  foreach ($list as $index => $value) {
                      $result[$name][$index][$attrib] = $value;
                  }
              }
          } else {
              $result[$name][] = $fileArray;
          }
      }
      $array = $result;

      $counter = 0;
      for ($i = 0; $i < count($array['file_upload']); $i++)
      {
          $result = $imgHandler->Upload($array['file_upload'][$i], $image, 2000, 2000, 999999, false);

          switch($result)
          {
              case -1:
                  $message = 'Die Datei ist zu groß.';
                  break;
              case -2:
                  $message = 'Die Datei ist ungültig.';
                  break;
              case -3:
                  $message = 'Es ist nur jpg, jpeg und png erlaubt.';
                  break;
              case -4:
                  $message = 'Der Name ist schon vergeben.';
                  break;
              case -5:
                  $message = 'Es gab ein Problem beim hochladen.';
                  break;
          }
          if($result == 1)
          {
              $imagename = $array['file_upload'][$i]['name'];
              $log = 'Bild <img src="'.$path.'/'.$imagename.'" style="width: 50px; height: 50px;"/> wurde erfolgreich hochgeladen.';
              AddToLog($database, $ip, $accs, $log);
              $counter++;
              if($_GET['directory'] == 'storyimages' || $_GET['directory'] == 'marketing')
              {
                  $imagestring = '[img]<a href="'.$serverurl.$path.$imagename.'" target="_blank">'.$serverurl.$path.$imagename.'</a>[/img]';
              }
              else
              {
                  $imagestring = '<a href="'.$path.$imagename.'" target="_blank">'.$path.$imagename.'</a>';
              }

              array_push($uploadedimages, $imagestring);
          }
      }
      if($message == '')
        $message = $counter . ' Bilder wurden hochgeladen.';
  }
}
