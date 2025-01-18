<?php
if(isset($_GET['p']) == 'blackboard' && isset($_GET['create']) == 'blackboardmassage')
{
    $massage = $database->EscapeString($_POST['massage']);
    if($player->GetBlackBoard() != 'nichts')
    {
        $message = "Du hast bereits ein Blackboard Eintrag drin";
    }
    else if(empty($massage))
    {
        $massage = "Bitte fülle das Feld aus";
    }
    else if($database->HasBadWords($message))
    {
        $message = "Dein Text enthält unerlaubte Wörter";
    }
    else if($player->GetLevel() < 5)
    {
        $message = "Du kannst erst mit Level 5 einen Eintrag erstellen";
    }
    else if(!$player->IsVerified())
    {
        $message = "Du musst erst von einem Admin verifiziert werden bevor du einen Eintrag erstellen kannst!";
    }
    else
    {
        $player->SetBlackBoardText($massage);
        $message = "Dein Eintrag war erfolgreich!";
    }
}

if(isset($_GET['p']) == 'blackboard' && isset($_GET['blackboardmessage']) == 'adelete' && isset($_GET['id']) && is_numeric($_GET['id']))
{
    if($player->GetArank() < 2)
    {
        $message = "Du bist dazu nicht berechtigt.";
    }
    else
    {
        $player->DeleteAdminBlackboard($_GET['id']);
        $message = "Du hast dein Eintrag erfolgreich gelöscht";
    }
}

if(isset($_GET['p']) == 'blackboard' && isset($_GET['blackboardmessage']) == 'delete' && isset($_GET['id']) && is_numeric($_GET['id']))
{
    if($player->GetBlackBoard() == 'nichts')
    {
        $message = "Du hast keinen Eintrag";
    }
    else if($_GET['id'] != $player->GetID() && $player->GetArank() < 2)
    {
        $message = "Die ID ist ungültig";
    }
    else
    {
        $player->DeleteBlackBoard();
        $message = "Du hast dein Eintrag erfolgreich gelöscht";
    }
}

if(isset($_GET['p']) == 'blackboard' && isset($_GET['edit']) == 'blackboardmessage' && isset($_GET['id']) && is_numeric($_GET['id']))
{
    $editmassage = $database->EscapeString($_POST['editmassage']);
    if($player->GetID() != $_GET['id'] && $player->GetArank() < 2)
    {
        $message = "Die ID ist ungültig";
    }
    else if(empty($editmassage))
    {
        $message = "Der Eintrag ist ungültig";
    }
    else
    {
        $player->SetBlackBoardText($editmassage);
        $message = "Du hast deinen Eintrag erfolgreich geändert";
    }
}
?>