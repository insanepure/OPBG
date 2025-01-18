<?php

function CheckDailyUpdate($player, $account): bool
{
    $isUpdate = date("H:i");
    if($isUpdate >= "23:55" && $isUpdate <= "00:00" || isset($_GET['test']))
    {
        return true;
    }
    return false;
}