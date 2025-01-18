<?php
    if($player->GetArank() < 3)
    {
        header('Location: ?p=news');
        exit();
    }
    if($_GET['p'] == "itemforall" && $_GET['use'] == 'inventory')
        {
    $database->Debug();
    $itemID = $_POST['item'];
    $amount = $_POST['amount'];
    $userID = $_POST['userID'];
    $LoadItem = $database->Select('*', 'items', 'id="'.$itemID.'"');
    $item = $LoadItem->fetch_assoc();
    $LoadUser = $database->Select('*', 'accounts', 'id="'.$userID.'"');
    $row = $LoadUser->fetch_assoc();

    if($userID != 'Leer')
    {
        if($LoadItem->num_rows == 0)
        {
           $message = "Das Item existiert nicht";
        }
        else if(empty($itemID) OR empty($amount) OR empty($userID))
        {
            $message = "Bitte fülle alle Felder aus";
        }
        else if(!is_numeric($itemID) OR !is_numeric($userID) OR !is_numeric($amount))
        {
            $message = "Die Felder dürfen nur aus zahlen bestehen!";
        }
        else if($LoadUser->num_rows == 0);
        {
            $message = "Dieser User existiert nicht";
        }
        $LoadInventar = $database->Select('*', 'inventory', 'statsid='.$itemID.' AND visualid='.$itemID.' AND ownerid='.$userID.'');
        if($LoadInventar)
        {
            while($inventory = $LoadInventar->fetch_assoc())
            {
                if($LoadInventar->num_rows == 0)
                {
                    $anzahl = $amount;
                    $result = $database->Insert('amount, statsid, visualid, ownerid', '"'.$anzahl.'", "'.$itemID.'", "'.$itemID.'", "'.$userID.'"', 'inventory');
                    $message = "Du hast das Item: ".$item['name']." dem User ".$row['name']." eingefügt";
                }
                else
                {
                    $update = $inventory["amount"] + $amount;
                    $result = $database->Update('amount="' .$update. '"', 'inventory', 'id = ' . $inventory['id'] . '', 1);
                    $message = "Du hast das Item: ".$item['name']." dem User ".$row['name']." ".$amount."x gegeben!";
                }
            }
        }
    }
    else if($LoadItem->num_rows == 0)
        {
            $message = "Das Item existiert nicht";
        }
        else if($itemID == 0 OR $amount == 0)
        {
            $message = "Du musst etwas in das Feld eintragen oder der Eintrag ist ungültig";
        }
        else if(empty($itemID) OR empty($amount))
        {
            $message = "Bitte trage etwas bei Item ID und Menge ein";
        }
        else if(!is_numeric($itemID) OR !is_numeric($amount))
        {
            $message = "Es müssen in beiden Feldern zahlen eingetragen werden";
        }
        else
        {
            $LoadAllUser = $database->Select('*', 'accounts', '');
            if($LoadAllUser)
            {
                while($IsUser = $LoadAllUser->fetch_assoc())
                    { 
                        $LoadAllInventory = $database->Select('*', 'inventory', 'statsid='.$itemID.' AND visualid='.$itemID.' AND ownerid='.$IsUser['id'].'');
                        $AllInventory = $LoadAllInventory->fetch_assoc();
                        if($LoadAllInventory->num_rows == 0)
                            {
                            $anzahl = $amount;
                            $result = $database->Insert('amount, statsid, visualid, ownerid', '"'.$anzahl.'", "'.$itemID.'", "'.$itemID.'", "'.$IsUser['id'].'"', 'inventory');
                            }
                            else
                            {
                             $update = $amount + $AllInventory['ammount'];
                             $result = $database->Update('amount="'.$update.'"', 'inventory', 'id="'.$AllInventory['id'].'"');    
                            }
                    }
        
                $message = "Du hast allen Usern das Item: ".$item['name']." ".number_format($amount,0, ',','.')."x gegeben";
            }
        }
    }