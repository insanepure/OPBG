<?php
$inventory = $player->GetInventory();
$title = 'Kleiderschrank';

if(isset($_GET['a']) && $_GET['a'] == 'store')
{
    if(!isset($_POST['action']) || $_POST['action'] != 0 && $_POST['action'] != 1)
    {
        $message = "Ungültige Aktion.";
    }
    else if(!isset($_POST['id']) || !is_numeric($_POST['id']))
    {
        $message = "Ungültiges Item.";
    }
    else
    {
        $item = $inventory->GetItem($_POST['id']);
        if ($item == NULL) {
            $message = "Dieses Item ist ungültig!";
        }
        else
        {
            if($_POST['action'] == 0)
            {
                $item->SetStored(1);
                $database->Update('isstored=1', 'inventory', 'id='.$item->GetID());
                $message = "Du hast den Ausrüstungsgegenstand ".$item->GetName()." in dein Kleiderschrank geräumt.";
            }
            else if($_POST['action'] == 1)
            {
                $item->SetStored(0);
                $database->Update('isstored=0', 'inventory', 'id='.$item->GetID());
                $message = "Du hast den Ausrüstungsgegenstand ".$item->GetName()." aus dein Kleiderschrank geholt.";
            }
        }
    }
}