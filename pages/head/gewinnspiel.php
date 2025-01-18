<?php
$title = "Gewinnspiel";

if(!$player->IsLogged())
{
    header("location:index.php");
    exit;
}