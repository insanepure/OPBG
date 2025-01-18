<?php
$capcha = $_POST['capchacode'];
$koloplayer = $player->GetID();
if($_GET['p'] == 'kolocapcha' && $_GET['code'] == 'send')
{
    if(!is_numeric($capcha))
    {
        $message = "<b>!!! FEHLER !!!</b> Darf nur zahlen enthalten";
    }
    else if($capcha != $player->GetKoloCode())
    {
        $message = "<b>!!! FEHLER !!!</b> Der Code ist falsch!";
    }
    else if($player->GetCapchaCount() < 20)
    {
        $message = "<b>!!! FEHLER !!!</b> Du musst noch kein Captcha bestätigen";
    }
    else
    {
        $message = "Vielen Dank und viel Spaß im Kolosseum";
        $player->SetCaptchaCount(0);
        $player->SetKoloCode(0);
        $database->Update('capchacount="0", kolocode="0"', 'accounts', 'id="'.$koloplayer.'"');
    }
}
?>