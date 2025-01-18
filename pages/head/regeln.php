<?php

if(isset($_GET['p']) == 'regeln' && isset($_GET['regeln']) == 'read')
{
   if(isset($_POST['read']))
   {
       $check = $_POST['read'];
   }
   else if($player->GetReadRules() == 1)
   {
       $message = "Du hast die Regeln bereits bestätigt";
   }
   if(empty($check))
   {
       $message = "Du musst die Regeln bestätigen!";
   }
   else
   {
     if($player->GetReadRules() == 0)
     {
         $player->SetReadRules(1);
         $result = $database->Update('read_rules=1', 'accounts', 'id="'.$player->GetID().'"');
       
         $message = "Du hast die Regeln erfolgreich bestätigt";
         header('Refresh: 2; url=?p=regeln');
     }
   }
}
