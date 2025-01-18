<?php
include_once 'classes/header.php';
if($player->GetArank() == 3)
{
    echo "<button><a href='?p=gewinns&create=gewinnspiel'>Gewinnspiel erstellen</a></button";
}
if(isset($_GET['p']) && $_GET['p'] == 'gewinns' && isset($_GET['create']) && $_GET['create'] == 'gewinnspiel' && $player->GetArank() == 3)
{
    if(isset($_GET['p']) && $_GET['p'] == 'gewinns' && isset($_GET['create']) && $_GET['create'] == 'gewinnspiel' && isset($_GET['creat']) && $_GET['creat'] == 'true' && $player->GetArank() == 3)
    {
        $title = $database->EscapeString($_POST['title']);
        $win = $database->EscapeString($_POST['winable']);
        $bedingung = $database->EscapeString($_POST['bedingungen']);
        $time = $database->EscapeString($_POST['zeitraum']);
        $pic = $database->EscapeString($_POST['picture']);
        $active = $database->EscapeString($_POST['active']);
        if(empty($title) && empty($win) && empty($bedingung) && empty($time) && empty($pic) && empty($active))
        {
            $message = "Du hast vergessen ein Feld auszufüllen!";
        }
        else if($active != 0 && $active != 1)
        {
            $message = "Fehler!";
        }
        else
        {
            $result = $database->Insert('titel, gewinn, teilnahmebedingungen, zeitraum, image, active', '"'.$title.'", "'.$win.'", "'.$bedingung.'", "'.$time.'", "'.$pic.'", "'.$active.'"', 'gewinnspiel');
            $message = "Das Gewinnspiel wurde erfolgreich erstellt";
        }
    }
        ?>
    <form method="post" action="?p=gewinns&create=gewinnspiel&creat=true">
    <b>Titel</b>
        <br />
        <input type="text" name="title" placeholder="Titel"/>
        <br />
        <b>Der Gewinn</b>
        <br />
        <textarea cols="50" rows="5" name="winable"></textarea>
        <br />
        <b>Teilnahmebedingungen</b>
        <br />
        <textarea cols="50" rows="10" name="bedingungen"></textarea>
        <br />
        <b>Zeitraum</b>
        <br />
        <input type="text" name="time" placeholder="Zeitraum"/>
        <br />
        <b>Bild</b>
        <br />
        <input type="text" name="picture" placeholder="Bild"/>
        <br />
        Aktiv/Inaktiv
        <br />
        <input type="checkbox" name="active" />
        <br />
        <input type="submit" value="Gewinnspiel Speichern" />
    </form>
<?php
}
?>
