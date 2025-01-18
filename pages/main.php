<?php
//pages you can access when you are not logged in
if (
	!$account->IsLogged() && isset($_GET['p'])
	&& $_GET['p'] != ''
	&& $_GET['p'] != 'news'
	&& $_GET['p'] != 'login'
	&& $_GET['p'] != 'pwforgot'
	&& $_GET['p'] != 'regeln'
	&& $_GET['p'] != 'info'
	&& $_GET['p'] != 'online'
	&& $_GET['p'] != 'register'
	&& $_GET['p'] != 'partner'
	&& $_GET['p'] != 'changelog'
	&& $_GET['p'] != 'verzeichnis'
)
{
	header('Location: ?p=login');
	exit();
}
else if (
	$account->IsLogged() && !$player->IsLogged() && isset($_GET['p'])
	&& $_GET['p'] != ''
	&& $_GET['p'] != 'login'
    && $_GET['p'] != 'news'
	&& $_GET['p'] != 'pwforgot'
	&& $_GET['p'] != 'regeln'
	&& $_GET['p'] != 'info'
	&& $_GET['p'] != 'register'
	&& $_GET['p'] != 'online'
	&& $_GET['p'] != 'characreate'
	&& $_GET['p'] != 'charalogin'
	&& $_GET['p'] != 'partner'
	&& $_GET['p'] != 'changelog'
	&& $_GET['p'] != 'verzeichnis'
)
{
	header('Location: ?p=login');
	exit();
}
else if ($account->IsValid() && !$player->IsValid() && $account->IsBannedInGame('OPBG'))
{
	$message = 'Du wurdest aus folgendem Grund vom Spiel gebannt: ' . $account->GetBanReason() . '.';
	$account->Logout();
}
else if (($account->IsValid() && $player->IsValid() && ($account->IsBannedInGame('OPBG') || $player->IsBanned())) && !$player->IsAdminLogged())
{
	$banReason = "";
	if ($account->GetBanReason() == "")
		$banReason = $player->GetBanReason();
	else
		$banReason = $account->GetBanReason();
	$message = 'Du wurdest aus folgendem Grund vom Spiel gebannt: ' . $banReason . '.';
    $date_of_expiry = time() - 10;
    setcookie("ocharaid", "", $date_of_expiry);
	$player->Logout();
}
else
{
	if (isset($_GET['a']) && $_GET['a'] == 'groupleave' && $player->GetGroup() != null)
	{
		$player->LeaveGroup();
		$message = 'Du hast deine Gruppe verlassen.';
	}
	else if (isset($_GET['a']) && $_GET['a'] == 'acceptitem')
	{
		$player->AcceptNPCWonItems();
	}
    else if (isset($_GET['a']) && $_GET['a'] == 'claim')
    {
        if($player->GetClan() == 0 )
        {
            $message = 'Du gehörst keiner Bande an.';
        }
        else
        {
            $place = new Place($database, $player->GetPlace(), new ActionManager($database));
            $group = $player->GetGroup();
            $groupcheck = false;

            if($group == null)
            {
                $message = 'Du gehörst keiner Gruppe an.';
            }
            else if($place == null)
            {
                $message = 'Dieser Ort existiert nicht.';
            }
            else if(!$place->IsEarnable())
            {
                $message = 'Dieser Ort kann nicht beansprucht werden.';
            }
            else if($place->GetTerritorium() != 0)
            {
                $message = 'Dieser Ort wurde bereits beansprucht.';
            }
            else if(count($group) != 3)
            {
                $message = 'Ihr müsst zu 3. sein in der Gruppe.';
            }
            else
            {
                $groupcheck = true;
                foreach ($group as $groupmember) {
                    if($message == '')
                    {
                        $groupplayer = new Player($database, $groupmember);
                        if ($groupplayer->GetClan() != $player->GetClan())
                        {
                            $message = 'Ihr müsst alle der selben Bande angehören.';
                        }
                        else if ($groupplayer->GetPlace() != $player->GetPlace())
                        {
                            $message = 'Ihr müsst alle am selben Ort sein um ihn zu beanspruchen.';
                        }
                        else if ($groupplayer->GetFight() != 0)
                        {
                            $message = 'Ihr könnte keinen Ort beanspruchen, während jemand Kämpft.';
                        }
                        else if ($groupplayer->GetTournament() != 0)
                        {
                            $message = 'Ihr könnte keinen Ort beanspruchen, während jemand am Turnier teilnimmt.';
                        }
                        else if($groupplayer->GetLP() < $groupplayer->GetMaxLP())
                        {
                            $message = 'Alle Mitglieder müssen volle LP haben.';
                        }
                        else if($groupplayer->GetKP() < $groupplayer->GetMaxKP())
                        {
                            $message = 'Alle Mitglieder müssen volle AD haben.';
                        }
                        else if(!$groupplayer->IsVerified() || $groupplayer->IsBanned())
                        {
                            $message = 'Alle Gruppenmitglieder müssen verifiziert und dürfen nicht gebannt sein.';
                        }
                        else if($groupplayer->GetActiveUserID() != $groupplayer->GetUserID())
                        {
                            $message = 'Es dürfen keine Sitter mit zu sittenden Charaktere an Bandenkämpfe teilnehmen.';
                        }
                    }
                }

                if($message == '')
                {
                    if($clan->GetBerry() < 25000)
                    {
                        $message = 'Deine Bande muss mindestens 25.000 <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 5px; height: 20px; width: 13px;"/> besitzen.';
                    }
                    else if($clan->GetRankPermission($player->GetClanRank(), "challenge") == 0)
                    {
                        $message = 'Du bist nicht dazu berechtigt ein Gebiet anzugreifen.';
                    }
                    else
                    {
                        $winner = array();
                        foreach ($group as $groupmember) {
                            $winner[] = $groupmember;
                        }
                        $winner = implode(';', $winner);
                        $result = $database->Update('territorium='.$player->GetClan().', time=NOW(), sieger="'.$winner.'", gewinn=0, lastfight=NOW(), blocked=0', 'places', 'id='.$place->GetID());
                        $result = $database->Update('zeni=zeni-25000', 'clans', 'id='. $player->GetClan());
                        $message = $place->GetName() . ' wurde von euch beansprucht.';

                        $text = '/system Die Bande '.$clan->GetName().' hat den Ort '.$place->GetName().' beansprucht.';
                        $text = str_replace('{@BGMSG@}', '', $text);
                        $text = str_replace('{@BG@}', '', $text);
                        $chat->SendMessage($text, 1, 1);
                    }
                }
            }
        }
    }
    else if (isset($_GET['a']) && $_GET['a'] == 'challenge')
    {
        $message = '';
        if($player->GetClan() == 0 )
        {
            $message = 'Du gehörst keiner Bande an.';
        }
        else
        {
            $place = new Place($database, $player->GetPlace(), new ActionManager($database));
            $gegnerBande = new Clan($database, $place->GetTerritorium());
            $group = $player->GetGroup();
            $groupcheck = false;

            if($group == null)
            {
                $message = 'Du gehörst keiner Gruppe an.';
            }
            else if($place == null)
            {
                $message = 'Dieser Ort existiert nicht.';
            }
            else if(!$place->IsEarnable())
            {
                $message = 'Dieser Ort kann nicht beansprucht werden.';
            }
            else if($place->GetTerritorium() == 0)
            {
                $message = 'Dieser Ort kann ohne Kampf beansprucht werden.';
            }
            else if($place->GetTerritorium() == $player->GetClan())
            {
                $message = 'Dieser Ort wurde bereits beansprucht.';
            }
            else if(count($group) != 3)
            {
                $message = 'Ihr müsst zu 3. sein in der Gruppe.';
            }
            else if(strtotime($gegnerBande->GetChallengeTime()) > time())
            {
                $message = 'Die Bande befindet sich bereits in einem Bandenkampf.';
            }
            else if((strtotime($place->GetTime())+(7*24*60*60)) > time() && $place->IsBlocked() == $player->GetClan())
            {
                $message = 'Das Gebiet kann erst am ' . date("d.m.Y \u\m H:i:s", strtotime($place->GetTime()) + (7*24*60*60)) . ' wieder von euch angegriffen werden.';
            }
            else
            {
                $groupcheck = true;
                foreach ($group as $groupmember) {
                    if($message == '')
                    {
                        $groupplayer = new Player($database, $groupmember);
                        if ($groupplayer->GetClan() != $player->GetClan())
                        {
                            $message = 'Ihr müsst alle der selben Bande angehören.';
                        }
                        else if ($groupplayer->GetPlace() != $player->GetPlace())
                        {
                            $message = 'Ihr müsst alle am selben Ort sein um ihn zu beanspruchen.';
                        }
                        else if ($groupplayer->GetFight() != 0)
                        {
                            $message = 'Ihr könnte keinen Ort beanspruchen, während jemand Kämpft.';
                        }
                        else if ($groupplayer->GetTournament() != 0)
                        {
                            $message = 'Ihr könnte keinen Ort beanspruchen, während jemand am Turnier teilnimmt.';
                        }
                        else if($groupplayer->GetLP() < $groupplayer->GetMaxLP())
                        {
                            $message = 'Alle Mitglieder müssen volle LP haben.';
                        }
                        else if($groupplayer->GetKP() < $groupplayer->GetMaxKP())
                        {
                            $message = 'Alle Mitglieder müssen volle AD haben.';
                        }
                        else if(!$groupplayer->IsVerified() || $groupplayer->IsBanned())
                        {
                            $message = 'Alle Gruppenmitglieder müssen verifiziert und dürfen nicht gebannt sein.';
                        }
                        else if($groupplayer->GetActiveUserID() != $groupplayer->GetUserID())
                        {
                            $message = 'Es dürfen keine Sitter mit zu sittenden Charaktere an Bandenkämpfe teilnehmen.';
                        }
                    }
                }

                if($message == '')
                {
                    if($clan->GetRankPermission($player->GetClanRank(), "challenge") == 0)
                    {
                        $message = 'Du bist nicht dazu berechtigt ein Gebiet anzugreifen.';
                    }
                    else {
                        $type = 11;
                        $name = 'Bandenkampf um ' . $place->GetName();
                        $mode = Fight::ValidateMode('3vs3');
                        $createdFight = Fight::CreateFight($player, $database, $type, $name, $mode, 0, $actionManager, 0, 0, 0, 0, 0, 0, $place->GetTerritorium());
                        if (!$createdFight) {
                            $message = 'Es gab Probleme bei der Erstellung des Kampfes.';
                        } else {
                            foreach ($group as $groupmember) {
                                $groupplayer = new Player($database, $groupmember);
                                $createdFight->Join($groupplayer, 0, false);
                            }
                            $gegnerBande->SetChallengeFight($createdFight->GetID());
                            $timestamp = date('Y-m-d H:i:s', time() + 30 * 60);
                            $database->Update('challengefight=' . $createdFight->GetID() . ', challengedtime="' . $timestamp . '"', 'clans', 'id=' . $gegnerBande->GetID());
                            $database->Update('clanwarpopup=1, challengedpopup=0', 'accounts', 'clan=' . $gegnerBande->GetID());
                            $message = '<a href="?p=clan&id=' . $gegnerBande->GetID() . '">' . $gegnerBande->GetName() . '</a> wurde herausgefordert.';

                            $text = '/system Die Bande '.$clan->GetName().' greift die Bande '.$gegnerBande->GetName().' an.';
                            $text = str_replace('{@BGMSG@}', '', $text);
                            $text = str_replace('{@BG@}', '', $text);
                            $chat->SendMessage($text, 1, 1);
                        }
                    }
                }
            }
        }
    }
	else if (isset($_GET['a']) && $_GET['a'] == 'endAction' && $player->GetArank() >= 2)
	{
		if ($player->GetFight() != 0 && !isset($_GET['type']))
		{
			$message = 'Du kannst in einem Kampf die Aktion nicht abbrechen.';
		}
		else if ($player->GetAction() != 0 && !isset($_GET['type']))
		{
			$player->EndAction();
		}
		else if ($player->GetTravelAction() != 0 && isset($_GET['type']))
		{
			$player->EndTravelAction();
		}
	}
	else if (isset($_POST['a']) && $_POST['a'] == 'cancelAction')
	{
		if ($player->GetFight() != 0)
		{
			$message = 'Du kannst in einem Kampf die Aktion nicht abbrechen.';
		}
		else if (isset($_POST['t']) && $_POST['t'] == 'travel')
		{
			$player->CancelTravelAction();
		}
		else if ($player->GetAction() != 0)
		{
            $player->CancelAction();
		}
	}
	else if (isset($_POST['a']) && $_POST['a'] == 'refreshAction')
	{
		if ($player->GetFight() != 0)
		{
			$message = 'Du kannst in einem Kampf die Aktion nicht aktualisieren.';
		}
        if ($player->GetTournament() != 0)
        {
            $message = 'Du kannst in einem Turnier die Aktion nicht aktualisieren.';
        }
		else if ($player->GetAction() != 1 && $player->GetAction() != 9)
		{
            $message = 'Diese Aktion kann nicht aktualisiert werden.';
		}
        else
        {
            $player->RefreshAction();
        }
	}

    else if(isset($_GET['a']) && $_GET['a'] == 'updatemail')
    {
        if(!$account->IsValid())
        {
            $message = 'Du bist nicht eingeloggt.';
        }
        else if((!isset($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) && !isset($_GET['code']))
        {
            $message = 'Die eingegebene Email Adresse ist nicht gültig.';
        }
        else
        {
            if(!isset($_GET['code'])) {
                $code = $account->GetCode($account->Get('id'), $_POST['email']);
                $topic = 'OPWX - Bestätigung Email Adresse';
                $serverUrl = 'https://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
                $content = '
              Du hast die Email-Adresse bei dem <a href="' . $serverUrl . '">One Piece Browsergame</a> aktualisiert.<br/>
              Um die aktualisierung abzuschließen, musst du den folgenden Link klicken.<br/>
              Wenn du den Link nicht bis zum nächsten Tag öffnest, wird der Account in den nächsten Tagen inklusive Charaktere gelöscht.<br/>
              <br/>
              <a href="' . $serverUrl . '?p=charalogin&a=updatemail&code=' . $code . '&id=' . $account->Get("id") . '">Email Adresse bestätigen.</a>
              <br/>
              <br/>';

                $resultID = $account->UpdateEmail($_POST['email'], $code, $account->Get('id'), 0);
                if ($resultID == 0)
                {
                    $message = 'Es wurde eine E-Mail an deine E-Mail Adresse gesendet. Bitte schau auch in den Spam-Ordner nach.';
                    SendMail($_POST['email'], $topic, $content);
                }
                else if($resultID == 1)
                {
                    $message = 'Die eingetragene Email-Adresse wird bereits verwendet.';
                }
            }
            else
            {
                $result = $account->UpdateEmail('', $_GET['code'], $_GET['id'], 1);
                if($result == 0)
                {
                    $message = "Du hast die Email-Adresse erfolgreich bestätigt.";
                }
                else if($result == 1)
                {
                    $message = "Der Aktivierungslink ist nicht gültig, bitte wende dich an ein Teammitglied.";
                }
            }
        }
    }
}
?>