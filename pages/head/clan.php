<?php
include_once $_SERVER['DOCUMENT_ROOT'].'classes/bbcode/bbcode.php';

$title = 'Bande';
$displayClan = null;
if (isset($_GET['id']) && is_numeric($_GET['id']))
{
    $displayClan = new Clan($database, $_GET['id']);
    if (!$displayClan->IsValid())
    {
        $displayClan = null;
    }

    $clan = new Clan($database, $player->GetClan());
}

if ($displayClan == null)
{
    header('Location: ?p=news');
    exit();
}