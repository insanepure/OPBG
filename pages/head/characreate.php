<?php
    $title = 'Charaktererstellung';

    if ($account->IsLogged())
    {
        $charas = new Generallist($database, 'accounts', 'userid, id', 'userid="' . $account->Get('id') . '"', 'id', 999);
        $id = 0;
        $arank = 0;
        $isDonor = 0;
        $discordID = 0;
        $IsMulti = false;
        $entry = $charas->GetEntry($id);
        while ($entry != null)
        {
            $displayedPlayer = new Player($database, $entry['id']);
            $arank = $displayedPlayer->GetARank();
            if(!$isDonor)
                $isDonor = $displayedPlayer->IsDonator();
            if(!$discordID)
                $discordID = $displayedPlayer->GetDiscordID();
            $IsMulti = true;
            ++$id;
            $entry = $charas->GetEntry($id);
        }
        if(isset($_GET['a']) && $_GET['a'] == 'register' && !$charaCreationActive)
        {
            $message = 'Die Charaktererstellung ist noch nicht geöffnet.';
        }
        if (isset($_GET['a']) && !$player->IsLogged() || $charaCreationActive && $player->IsLogged())
        {
            $a = $_GET['a'];
            if ($a == 'register')
            {
                $result = 4;


                $chara = $_POST['chara'];
                $rasse = $_POST['rasse'];
                $raceImage = $_POST['raceimage'];
                $raceImage = str_replace('Pirat', '', $raceImage);
                $raceImage = str_replace('Marine', '', $raceImage);
                $pfad = $_POST['pfad'];

                if ($database->HasBadWords($chara))
                {
                    $message = 'Der Name enthält ungültige Wörter.';
                }
                else if (!preg_match("/^[a-zA-Z0-9]+$/", $chara))
                {
                    $message = 'Der Name darf nur aus Buchstaben und Zahlen bestehen.';
                }
                else if (strtolower($chara) == 'system')
                {
                    $message = 'Den Namen darfst du nicht benutzen.';
                }
                else if (strlen($chara) > 12)
                {
                    $message = 'Der Name darf maximal 12 Zeichen lang sein.';
                }
                else if (strtolower($chara) == 'bot')
                {
                    $message = 'Den Namen darfst du nicht benutzen.';
                }
                else if (strtolower($chara) == 'gott')
                {
                    $message = 'Den Namen darfst du nicht benutzen.';
                }
                else if (strtolower($chara) == 'opbg')
                {
                    $message = 'Den Namen darfst du nicht benutzen.';
                }
                else if (strtolower($chara) == 'opwx')
                {
                    $message = 'Den Namen darfst du nicht benutzen.';
                }
                else if ($rasse != 'Pirat' && $rasse != 'Marine')
                {
                    $message = 'Die Rasse ist ungültig.';
                }
                else if($rasse == 'Marine' && $raceImage > 8)
                {
                    $message = 'Das Bild ist ungültig.';
                }
                else if (!is_numeric($raceImage) || $raceImage < 1 || $raceImage > 12)
                {
                    $message = 'Das Bild ist ungültig.';
                }
                else if ($pfad != 'Schwertkaempfer' && $pfad != 'Schwarzfuss' && $pfad != 'Karatekämpfer')
                {
                    $message = 'Du hast einen ungültigen Pfad ausgewählt';
                }
                else
                {
                    $raceImage = $rasse . $raceImage;
                    $result = $player->CreateCharacter($rasse, $pfad, $chara, $raceImage, $account->Get('id'), $account->Get('email'), $isDonor, $discordID, $IsMulti, $gameStartTime);
                    if ($result == 0)
                    {
                        $id = 0;
                        $image = "img/system.png";
                        $name = 'System';
                        $title = 'Willkommen';
                        $text = 'Willkommen auf OPBG, einem One Piece Browsergame!
				  
					Wir möchten dir in dieser Nachricht die grundlegendsten Dinge erklären.
					
					Wenn du einen Charakter erstellt hast, wirst du überrascht sein wie viele Statpunkte du hast, das liegt daran das OPWX
					möchte das auch ein neuer Spieler direkt zum Start die Möglichkeit bekommt oben mitzuspielen denn ohne diese Punkte könnte
					man bereits existierende Spieler nie wieder einholen.
					
					Kurze Erklärung zu deinen Werten, LP sind die Lebenspunkte, beginnst du einen Kampf und diese fallen auf 0 hast du den Kampf verloren!
					AD sind die Ausdauerpunkte, es gibt Techniken die eine Gewisse Ausdauer Voraussetzen und besitzt du nicht genug Punkte dann sind diese eben nicht
					mehr einsetzbar. Angriff ist ein Indikator für den Schaden den du verursachst und Defense einer welcher den Schaden reduziert
					Douriki ist wie Angrirff ein Wert welcher bestimmt wie viel Schaden du verursachst, die Douriki berechnen sich aus deinen Stats, 4 verteilte Statpunkte = 1 Douriki
					
					Um mit anderen Spielern kommunizieren zu können benötigst du eine Teleschnecke, die erhältst du im [url=?p=shop][color=blue]Shop[/color][/url], hast du dir eine kauft klickst du nun im 
					[url=?p=inventar][color=blue]Inventar[/color][/url] auf benutzen und schon kannst du Nachrichten mit der Community austauschen. 
					
					Kommen wir zum reisen, in der Welt von One Piece ist die Reise eigentlich so zu sagen der Mittelpunkt und so kommt es auch bei uns vor das du viel herumkommst,
					hierfür benötigst du ein Fortbewegungsmittel, zum Start wird es das Boot sein, das musst du dir nicht direkt kaufen du bekommst quasi eines zum Start geschenkt.
					
					Das Boot kannst du 2x nutzen, danach ist es abgenutzt und du musst es reparieren und auch das kannst du nur eine bestimmte an Anzahl durchführen danach muss es weg und man muss
					sich ein neues Boot / Schiff besorgen.
					
					Kämpfe, ja, Kämpfe! Sehr schön, wir haben viele verschiedene Arten von Kämpfe
					PvP-Kampf: In diesen Kämpfen beweist du deine Stärke gegenüber anderen Spielern, du erhältst Items welcher zu deinem Pfad gehören zusätzlich gibt es noch eine Menge an Berry und Gold. In deinem Profil findest du auch die 
					so genannten Statskämpfe, diese erhöhen sich mit jeden durchgeführten PvP-Kampf, deine maximalen Statkämpfe erhöhen sich mit jeden Charakterlevel um 10, für jeden 10 Statkampf erhälst du eine kleine Menge an Statpunkten mir denen du deinen Charakter stärken kannst.
					Elokämpfe: Elopunkte bestimmten das Ranking, bei uns läuft es nach Season, dies bedeutet das sich das Ranking jeden Monat erneuert und jeder die Chance bekommt mal #1 zu werden aber auch hier bekommt man Berry
					Spaßkämpfe: Dies ist einfach nur da um sich mal gegenseitig aus Spaß zu messen oder um Techniken zu testen, Skillung oder ähnliches.
					Kolosseum: Im Kolosseum kämpft ihr gegen die Gladiatoren und erhaltet Kolosseumpunkte, mit diesen Punkten könnt ihr viele verschiedene Sachen im Kolosseumshop kaufen.
					Story: Ich denke bei diesem Kampf kann es sich jeder denken, in der Story levelt man seinen Charakter, steigst du ein Level auf, erhältst du Berry, Gold und Stats mit denen du keinen Charakter stärken kannst.
					
					Du hast direkt zum Start Items bekommen den den [url=?p=skilltree][color=blue]Skilltree[/color][/url] betreffen, dort kannst nun dein ausgewählten Pfad auswählen und anfangen dich darin zu steigern,
					dort lernst du viele verschiedene Techniken!
					
					Ich denke das war genug zum lesen, die Community und das Team hilft dir selbstverständlich gerne weiter sollte eine Frage existieren die hier nicht beantwortet wurde.
					
					Es gibt auch das [url=?p=ticketsystem][color=blue]Ticketsystem[/color][/url] dort kannst du jederzeit nach Hilfe fragen ich denke wir finden auf alles eine Antwort.
					
					Desweiteren wünschen wir dir viel Spaß auf OPBG';
					
                        $PMManager = new PMManager($database, $player->GetID());
                        $PMManager->SendPM($id, $image, $name, $title, $text, $chara);
                        header('Location: ?p=charalogin');
                        exit();
                    }
                    else if ($result == 2)
                    {
                        $message = 'Der Charaktername ist ungültig.';
                    }
                    else if ($result == 1)
                    {
                        $message = 'Der Charaktername existiert bereits.';
                    }
                }
            }
        }
    }

    if ($player->IsLogged())
    {
        header('Location: index.php');
        exit();
    }
