
<script type="text/javascript">
    function liebe()
    {
        let betreff = document.getElementById('betreffs').value;
        console.log(betreff);
        if(betreff == 'Bug')
        {
            document.getElementById('texts').innerHTML = 'Du hast einen Bug gefunden, schön das du dir die Zeit nimmst ihn zu melden. \n \n \n Wo hast du den Bug gefunden? \n  - \n Wie funktioniert der Bug? \n  - \n Hast du dadurch etwas verloren oder zu viel bekommen: \n  - \n \n Kurze Beschreibung';
        }
        else if(betreff == 'Allgemeine Frage')
        {
            document.getElementById('texts').innerHTML = 'Du hast eine Frage? Kein Problem, wir helfen dir gerne weiter. \n\n\n Nenne uns doch bitte den Inhalt auf welche sich deine Frage bezieht \n  - \n Nun kannst du deine Frage stellen \n  -\n';
        }
        else if(betreff == 'Regelverstoß')
        {
            document.getElementById('texts').innerHTML = 'Schön das du dich meldest, hast du gesehen wie jemand gegen die Regeln verstößt? \n\n\n Erzähl uns doch bitte um welchen Spieler es sich handelt \n  - \n Gegen welche Regel hat er verstoßen? \n  - \n\n Sonstiges: \n';
        }
        else if(betreff == 'Highscore')
        {
            document.getElementById('texts').innerHTML = 'Hey, freut mich das du auch die Minispiele von OPWX nutzt. \n\n\n Welches Spiel hast du gespielt? \n  - \n Wie lautet dein Rekord? \n  -\n Screenshot: \n  -\n\n Bitte achte darauf das dein Username auf dem Screen zu sehen ist!';
        }
        else if(betreff == 'Kein Betreff')
        {
            document.getElementById('texts').innerHTML = 'Hey, schön das du da bist. Bitte wähle deinen Betreff!';
        }

        if(betreff == 'Kein Betreff')
        {
            document.getElementById('texts').readOnly = true;
        }
    }

    function zaehlen()
    {
        max = 750;
        anz = document.ticketsystem.answer.value.length + 1;
        document.ticketsystem.anzeige.value = max - anz;
        if(anz >= max)
        {
            alert("Maximum erreicht !!!");
            document.getElementById("answer").style.color="#FA5858";
        }
        else if(anz <= max)
        {
            document.getElementById("answer").style.color="#3ADF00";
        }
    }
</script>
<?php
$generalcheck = $database->Select('*', 'ticket', 'ersteller="'.$player->GetID().'" AND active=0');
$general = $generalcheck->fetch_assoc();
if($generalcheck->num_rows == 1)
{
    echo "Du hast bereits ein Ticket offen, klick <a href='?p=ticketsystem&id=".$general['id']."'>> Hier <</a> um zum Ticket zu gelangen";
}
else
{
    ?>
    <details>
        <summary class="select">Ticket öffnen</summary><br />
        <form method="post" action="?p=ticketsystem">
            Nenne uns einen Betreff für das Ticket
            <div class="spacer"></div>
            <select onchange="liebe()" class="select" id="betreffs" name="betreff">
                <option value="Kein Betreff">Kein Betreff</option>
                <option value="Bug">Bug</option>
                <option value="Allgemeine Frage">Allgemeine Frage</option>
                <option value="Regelverstoß">Regelverstoß</option>
                <option value="Highscore">Highscore</option>
            </select>
            <div class="spacer"></div>
            <textarea id="texts" name="text" cols="70" rows="15" maxlength="750" style="resize: vertical;"></textarea>
            <div class="spacer"></div>
            <input type="hidden" name="create" value="ticket">
            <button>Absenden</button>
        </form>
    </details>
    <br />
    <br />
    <?php
        if(!$player->IsDonator() && $player->GetArank() != 3)
        {
            ?>
                <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9844662963586162"
                        crossorigin="anonymous"></script>
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-format="fluid"
                     data-ad-layout-key="-dn-b+15-2t+50"
                     data-ad-client="ca-pub-9844662963586162"
                     data-ad-slot="9677433791"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            <?php
        }
    }
if($_GET['p'] == 'ticketsystem' && isset($_GET['id']) && is_numeric($_GET['id']))
{
    $id = $_GET['id'];
    $ticketcheck = $database->Select('*', 'ticket', 'id="'.$id.'"');
    if($ticketcheck)
    {
        $ticket = $ticketcheck->fetch_assoc();
        if($player->GetID() == $ticket['ersteller'] || $player->GetArank() >= 2 || $player->GetTeamUser() >= 2)
        {
            if($ticket['active'] == 0 || $player->GetArank() >= 2 || $player->GetTeamUser() >= 2)
            {
                $creator = new Player($database, $ticket['ersteller']);
                if($player->GetArank() >= 2 || $player->GetTeamUser() >= 2)
                {
                    ?>
                    <div class="spacer"></div>
                    <b>Dieses Ticket wurde von <a href="?p=profil&id=<?= $creator->GetID(); ?>"><?= $creator->GetName(); ?></a> mit dem Betreff <?= $ticket['betreff']; ?> eröffnet.
                    </b>
                    <div class="spacer"></div>
                    <img height="200" width="200" src="<?= $creator->GetImage(); ?>" alt="avatar"/>
                    <div class="spacer"></div>
                    <details>
                        <summary>
                            Ticket wurde mit folgender Nachricht geöffnet.
                        </summary>
                        <div class="spacer"></div>
                        <table width="100%" cellspacing="0" border="0">
                            <tr>
                                <td colspan=6 class="catGradient borderT borderB" align="center">
                                    <?= nl2br($ticket['openmessage']); ?>
                                </td>
                            </tr>
                        </table>
                    </details>
                    <?php
                }
                ?>
                <hr>
                <div class="spacer"></div>
                <?php
                if($ticket['active'] == 0)
                {
                    ?>
                    <form name="ticketsystem" onkeydown="zaehlen()" method="post" action="?p=ticketsystem&id=<?= $ticket['id'] ?>">
                        <b>Antwort</b>
                        <div class="spacer"></div>
                        <textarea id="answer" name="answer" cols="70" rows="8" style="resize: vertical;"></textarea>
                        <div class="spacer"></div>
                        <b>Maximale Zeichen:</b><input type="text" id="anzeige" value="750" name="anzeige" readonly />
                        <div class="spacer"></div>
                        <input type="hidden" name="create" value="answer">
                        <button id="button" name="button">Antworten</button>
                    </form>

                    <?php
                }
                else
                {
                    echo "<font color='red'>Dieses Ticket wurde bereits geschlossen!</font><br /><br />";
                    if($player->GetArank() >= 2 || $player->GetTeamUser() >= 2)
                    {
                        ?>
                        <form method='post' action='?p=ticketsystem&id=<?= $ticket['id'] ?>'>
                            <input type="hidden" name="open" value="ticket" />
                            <button>Öffnen</button>
                        </form>
                        <?php
                    }
                }
                ?>
                <div class="spacer"></div>
                <b>Verlauf</b>
                <table width="100%" cellspacing="0" border="0">
                    <tr>
                        <td colspan=6 class="catGradient borderT borderB" align="center">
                            <div>
                                <?= nl2br($ticket['verlauf']); ?>
                            </div>
                        </td>
                    </tr>
                </table>
                <?php
                if($player->GetArank() >= 2 || $player->GetTeamUser() >= 2)
                {
                    ?>
                    <form method="post" action="?p=ticketsystem&delete=ticket&id=<?php echo $ticket['id']; ?>">
                        <input type="hidden" name="delete" value="ticket"/>
                        <div class="spacer"></div>
                        <button>Löschen</button>
                    </form>
                    <div class="spacer"></div>
                    <form method="post" action="?p=ticketsystem&close=ticket&id=<?php echo $ticket['id']; ?>">
                        <input type="hidden" name="close" value="ticket"/>
                        <div class="spacer"></div>
                        <button>Schließen</button>
                    </form>
                    <?php
                }
            }
        }
        else
        {
            $message = "Auf dieses Ticket hast du keinen Zugriff";
        }
    }
}
?>