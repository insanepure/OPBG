<?php

    include_once 'classes/places/place.php';
    include_once 'classes/items/itemmanager.php';
    include_once 'classes/market/market.php';
    include_once 'classes/bbcode/bbcode.php';
    $title = 'Marktplatz';
    $market = new Market($database);
    $itemManager = new ItemManager($database);
    $PMManager = new PMManager($database, $player->GetID());
    $inventory = $player->GetInventory();

    if (!isset($player) || !$player->IsValid())
    {
        header('Location: ?p=news');
        exit();
    }

    if (isset($_GET['a']) && $_GET['a'] == 'sell') {
        if (!$player->IsVerified()) {
            $message = 'Dein Charakter wurde noch von keinem Admin verifiziert.';
        } else if ($player->GetFight() != 0) {
            $message = 'Dies kannst du im Kampf nicht tun.';
        } else if ($player->GetLevel() < 5) {
            $message = 'Du musst mindestens Level 5 sein, um etwas auf dem Markt verkaufen zu können.';
        } else if (!isset($_POST['item']) || !is_numeric($_POST['item'])) {
            $message = 'Das Item ist ungültig.';
        } else if (!isset($_POST['amount']) || !is_numeric($_POST['amount']) || floor($_POST['amount']) <= 0) {
            $message = 'Die Anzahl ist ungültig.';
        } else if (!isset($_POST['price']) || !is_numeric($_POST['price']) || floor($_POST['price']) <= 0) {
            $message = 'Der Preis ist ungültig.';
        } else if (!is_numeric($_POST['offer']) && $_POST['offer'] != '') {
            $message = 'Der Gebotspreis ist ungültig.';
        } else {
            $itemID = $_POST['item'];
            $amount = floor($_POST['amount']);
            $price = floor($_POST['price']);
            $gebot = floor($_POST['offer']);
            $item = $inventory->GetItem($itemID);
            if ($item == null) {
                $message = 'Das Item gibt es nicht.';
            } else if ($item->IsProtected()) {
                $message = 'Das Item ist geschützt.';
            } else {
                $itemPrice = $item->GetPrice();

                $buyerName = htmlentities($database->EscapeString($_POST['kaeufername']), ENT_QUOTES | ENT_XML1);
                $buyerPlayer = null;
                $buyerID = 0;
                if ($buyerName != null) {
                    $result = $database->Select('*', 'accounts', 'name="' . $buyerName . '"', 1);
                    if ($result) {
                        $row = $result->fetch_assoc();
                        $buyerID = $row['id'];
                        $result->close();
                    }
                    $buyerPlayer = new Player($database, $buyerID);
                }

                if ($buyerPlayer != null && !$buyerPlayer->IsValid()) {
                    $message = 'Diesen Spieler gibt es nicht.';
                } else if ($buyerPlayer != null && $buyerPlayer->IsBanned()) {
                    $message = 'Dieser Spieler ist gebannt!';
                } else if ($buyerPlayer != null && $buyerPlayer->GetID() == $player->GetID()) {
                    $message = 'Du kannst das Item nicht an dich selbst verkaufen!';
                } else {
                    $auras = array(125, 127, 128, 129, 130, 294, 295, 296, 297, 298, 299, 300);
                    $pfaditems = array(52, 53, 54, 55, 56, 57);
                    $chestItems = array(319, 320, 102, 318);
                    if ($item->IsMarktplatz() == 0) {
                        $message = 'Das Item kann nicht verkauft werden.';
                    } else if ($item->GetWear() > 0 && $itemManager->GetItem($item->GetVisualID())->GetItemUses() != 0) {
                        $message = "Abgenutzte Items kannst du nicht auf dem Marktplatz verkaufen!";
                    } else if ($item->IsEquipped()) {
                        $message = 'Ein ausgerüstet Item kannst du nicht verkaufen.';
                    } else if ($item->GetType() != 3 && $gebot > 0) {
                        $message = 'Dieses Item kann kein Gebot annehmen.';
                    } else if ($gebot > $_POST['price']) {
                        $message = 'Das Gebot kann nicht höher sein als der Preis.';
                    } else if ($item->GetAmount() < $amount) {
                        $message = 'Du besitzt nicht genügend davon.';
                    } else if ($item->GetType() != 3 && $market->HasItemInside($item->GetStatsID(), $item->GetVisualID(), $item->GetStatsType(), $item->GetUpgrade(), $player->GetID())) {
                        $message = 'Du hast so ein Item schon im Markt.';
                    } else if (in_array($item->GetVisualID(), $auras)) {
                        $message = 'Dieses Item kann so nicht verkauft werden.';
                    } else if (in_array($item->GetStatsID(), $pfaditems)) {
                        $message = "Pfaditems können nicht gehandelt werden.";
                    } else if (in_array($item->GetStatsID(), $chestItems) && $price < 5000) {
                        $message = "Diese Truhen können nicht unter 5000 Berry verkauft werden.";
                    } else {
                        if ($buyerPlayer != null) {
                            $buyerID = $buyerPlayer->GetID();
                        }
                        $owners = $item->GetFormerOwners();
                        $player->RemoveItems($item, $amount);
                        $market->AddItem($item->GetStatsID(), $item->GetVisualID(), $item->GetStatsType(), $item->GetUpgrade(), $amount, $price, $player, $owners, $buyerID, $gebot);
                        $waehrung = '<img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 5px; height: 20px; width: 13px;"/>';
                        if ($item->IsPremium())
                            $waehrung = '<img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 5px; height: 20px; width: 20px;"/>';

                        if ($buyerPlayer == null) {
                            $message = 'Du hast ' . number_format($amount, '0', '', '.') . 'x ' . $item->GetName() . ' für ' . number_format($price, '0', '', '.') . ' ' . $waehrung . ' in den Marktplatz gestellt.';
                        } else {
                            $message = 'Du hast ' . number_format($amount, '0', '', '.') . 'x ' . $item->GetName() . ' für ' . number_format($price, '0', '', '.') . ' ' . $waehrung . ' an <a href="?p=profil&id=' . $buyerPlayer->GetID() . '">' . $buyerPlayer->GetName() . '</a> in den Marktplatz gestellt.';
                        }
                    }
                }
            }
        }
    }
    else if (isset($_GET['a']) && $_GET['a'] == 'bid')
    {
        if (!$player->IsVerified())
        {
            $message = 'Dein Charakter wurde noch von keinem Admin verifiziert.';
        }
        else if ($player->GetLevel() < 5)
        {
            $message = 'Du musst mindestens Level 5 sein, um ein Gebot auf dem Marktplatz platzieren zu können.';
        }
        else if (!isset($_POST['id']) || !is_numeric($_POST['id']))
        {
            $message = 'Das Item ist ungültig.';
        }
        else if ($player->GetFight() != 0)
        {
            $message = 'Du bist in einem Kampf und kannst das Item nicht kaufen.';
        }
        else if(!isset($_POST['gebot']) || !is_numeric($_POST['gebot']))
        {
            $message = 'Dein Gebot ist ungültig.';
        }
        else {
            $gebot = $_POST['gebot'];
            $amount = floor($_POST['amount']);
            $item = $market->GetItemByID($_POST['id']);
            if ($item == null)
            {
                $message = 'Das Item gibt es nicht auf dem Markt.';
            }
            else if (!$item->IsPremium() && ($player->GetBerry() < ($item->GetPrice() * $amount)))
            {
                $message = 'Du hast nicht genügend Berry.';
            }
            else if ($item->IsPremium() && ($player->GetGold() < ($item->GetPrice() * $amount)))
            {
                $message = 'Du hast nicht genügend Gold.';
            }
            else if ($amount > $item->GetAmount())
            {
                $message = 'So viele Items werden im Markt nicht angeboten.';
            }
            else if ($item->GetSellerID() == $player->GetID())
            {
                $message = 'Du kannst nicht auf deine eigenen Items bieten.';
            }
            else if ($item->GetKaeufer() != 0 && $item->GetKaeufer() != $player->GetID())
            {
                $message = 'Du kannst nicht auf deine eigenen Items bieten.';
            }
            else if($item->GetBieter() == $player->GetID())
            {
                $message = 'Dein Gebot ist bereits das Höchste.';
            }
            else if($gebot < $item->GetGebot())
            {
                $message = 'Dein Gebot ist kleiner als das aktuelle Gebot.';
            }
            else
            {
                $seller = new Player($database, $item->GetSellerID());
                $price = $item->GetPrice() * $amount;
                $priceminus = round(10 * $price / 100);
                $priceseller = $price - $priceminus;
                $statstype = $item->GetStatsType();
                $upgrade = $item->GetUpgrade();
                $owners = array();
                if ($seller->IsBanned())
                {
                    $message = 'Der Verkäufer wurde gebannt, du kannst nicht von ihm Kaufen!';
                }
                else
                {
                    if ($item->GetFormerOwners() != '')
                        $owners = explode(";", $item->GetFormerOwners());
                    $buyerAccount = $player->GetUserID();
                    $darf = true;
                    foreach ($owners as $owner) {
                        $ow = new Player($database, $owner);
                        if ($ow->GetUserID() == $player->GetUserID()) {
                            if ((sizeof($owners) - (array_search($player->GetUserID(), $owners) + 1)) <= 5 && $ow->GetID() != $player->GetID() && $item->GetCategory() != 1) {
                                $message = "Du hast dieses Item erst vor kurzem über einen anderen Charakter verkauft!";
                                $darf = false;
                                break;
                            }
                        }
                    }
                    if ($darf == true) {
                        $owners[] = $item->GetSellerID();
                        $owners = implode(";", $owners);

                        $waehrung = "<img src='img/offtopic/BerrySymbol.png' alt='Berry' title='Berry' style='position: relative; top: 5px; height: 20px; width: 13px;'/>";
                        if($item->IsPremium())
                            $waehrung = "<img src='img/offtopic/GoldSymbol.png' alt='Gold' title='Gold' style='position: relative; top: 5px; height: 20px; width: 20px;'/>";

                        if ($item->GetBieter() != 0 && $item->GetGebot() > 0)
                        {
                            if($item->GetBieter() != $player->GetID())
                                $offerPlayer = new Player($database, $item->GetBieter());
                            else
                                $offerPlayer = $player;
                            $offerPlayer->ReturnOffer($item->GetGebot(), $item->IsPremium());
                            $database->Update('zeni='.$offerPlayer->GetBerry().', gold='.$offerPlayer->GetGold(),'accounts', 'id='.$offerPlayer->GetID());
                            $text = "Du wurdest von <a href='?p=profil&id=" . $player->GetID()."'>" . $player->GetName() . "</a> bei " . $item->GetName() . " überboten.<br/> Das neue Gebot liegt bei: " . number_format($gebot, 0, '', '.') . " " . $waehrung . ". Du hast " . number_format($item->GetGebot(), 0, '', '.') . " " . $waehrung . " zurückerhalten.";
                            $text = "<center>".$text."</center>";
                            $PMManager->SendPM(0, 'img/money.png', 'SYSTEM', 'Du wurdest überboten bei ' . $item->GetName() . '.', $text, $offerPlayer->GetName(), 1);
                        }

                        $market->SetGebot($item->GetID(), $player->GetID(), $gebot);
                        if($item->IsPremium())
                            $player->SetGold($player->GetGold() - $gebot);
                        else
                            $player->SetBerry($player->GetBerry() - $gebot);
                        $database->Update('zeni='.$player->GetBerry().', gold='.$player->GetGold(), 'accounts', 'id='.$player->GetID());

                        $text = "<img src='img/items/" . $item->GetImage() . ".png' width='80px' height='80px'>Du hast ein Gebot in Höhe von " . number_format($gebot, '0', '', '.') . " " . $waehrung . " für " . $item->GetName() . " von <a href='?p=profil&id=" . $player->GetID()."'>" . $player->GetName() . "</a> bekommen.";
                        $text = "<center>".$text."</center>";
                        $PMManager->SendPM(0, 'img/money.png', 'SYSTEM', 'Gebot für ' . $item->GetName() . ' erhalten.', $text, $item->GetSeller(), 1);

                        $message = 'Du hast ' . number_format($gebot, '0', '', '.') . ' ' . $waehrung . ' auf ' . $item->GetName() . ' von <a href="?p=profil&id=' . $item->GetSellerID().'">' . $item->GetSeller() . '</a> geboten.';
                    }
                }
            }
        }
    }
    else if (isset($_GET['a']) && $_GET['a'] == 'buy')
    {
        if (!$player->IsVerified())
        {
            $message = 'Dein Charakter wurde noch von keinem Admin verifiziert.';
        }
        else if ($player->GetLevel() < 5)
        {
            $message = 'Du musst mindestens Level 5 sein, um etwas auf dem Markt kaufen zu können.';
        }
        else if (!isset($_POST['id']) || !is_numeric($_POST['id']))
        {
            $message = 'Das Item ist ungültig.';
        }
        else if (!isset($_POST['amount']) || !is_numeric($_POST['amount']) || floor($_POST['amount']) <= 0)
        {
            $message = 'Die Anzahl ist ungültig.';
        }
        else if ($player->GetFight() != 0)
        {
            $message = 'Du bist in einem Kampf und kannst das Item nicht kaufen.';
        }
        else
        {
            $amount = floor($_POST['amount']);
            $item = $market->GetItemByID($_POST['id']);
            if ($item == null)
            {
                $message = 'Das Item gibt es nicht auf dem Markt.';
            }
            else if (!$item->IsPremium() && ($player->GetBerry() < ($item->GetPrice() * $amount)))
            {
                $message = 'Du hast nicht genügend Berry.';
            }
            else if ($item->IsPremium() && ($player->GetGold() < ($item->GetPrice() * $amount)))
            {
                $message = 'Du hast nicht genügend Gold.';
            }
            else if ($amount > $item->GetAmount())
            {
                $message = 'So viele Items werden im Markt nicht angeboten.';
            }
            else if ($item->GetSellerID() == $player->GetID())
            {
                $message = 'Du kannst dir nichts selber verkaufen.';
            }
            else if ($item->GetKaeufer() != 0 && $item->GetKaeufer() != $player->GetID())
            {
                $message = 'Du darfst das Item nicht kaufen.';
            }
            else
            {
                $seller = new Player($database, $item->GetSellerID());
                $price = $item->GetPrice() * $amount;
                if($player->GetLevel() <= 20)
                {
                    if($item->GetStatsID() == 52 || $item->GetStatsID() == 53 || $item->GetStatsID() == 54 || $item->GetStatsID() == 55 || $item->GetStatsID() == 56 || $item->GetStatsID() == 57)
                    {
                        $price = round($price / 2);
                    }
                }
                $priceminus = round(10 * $price / 100);
                $priceseller = $price - $priceminus;
                $statstype = $item->GetStatsType();
                $upgrade = $item->GetUpgrade();
                $owners = array();
                if ($seller->IsBanned())
                {
                    $message = 'Der Verkäufer wurde gebannt, du kannst nicht von ihm Kaufen!';
                }
                else if ($seller->GetUserID() == $player->GetUserID())
                {
                    $message = 'Du darfst mit keinem deiner Charaktere interagieren.';
                }
                else
                {
                    if ($item->GetFormerOwners() != '')
                        $owners = explode(";", $item->GetFormerOwners());
                    $buyerAccount = $player->GetUserID();
                    $darf = true;
                    foreach ($owners as $owner) {
                        $ow = new Player($database, $owner);
                        if ($ow->GetUserID() == $player->GetUserID()) {
                            if ((sizeof($owners) - (array_search($player->GetUserID(), $owners) + 1)) <= 5 && $ow->GetID() != $player->GetID() && $item->GetCategory() != 1) {
                                $message = "Du hast dieses Item erst vor kurzem über einen anderen Charakter verkauft!";
                                $darf = false;
                                break;
                            }
                        }
                    }
                    if ($darf == true) {
                        $owners[] = $item->GetSellerID();
                        $owners = implode(";", $owners);

                        $waehrung = "<img src='img/offtopic/BerrySymbol.png' alt='Berry' title='Berry' style='position: relative; top: 5px; height: 20px; width: 13px;'/>";
                        if ($item->IsPremium())
                            $waehrung = "<img src='img/offtopic/GoldSymbol.png' alt='Gold' title='Gold' style='position: relative; top: 5px; height: 20px; width: 20px;'/>";

                        if ($item->GetBieter() != 0 && $item->GetGebot() > 0) {
                            if ($item->GetBieter() != $player->GetID())
                                $offerPlayer = new Player($database, $item->GetBieter());
                            else
                                $offerPlayer = $player;
                            $offerPlayer->ReturnOffer($item->GetGebot(), $item->IsPremium());
                            $database->Update('zeni=' . $offerPlayer->GetBerry() . ', gold=' . $offerPlayer->GetGold(), 'accounts', 'id=' . $offerPlayer->GetID());

                            $text = $item->GetName() . " wurde von <a href='?p=profil&id=" . $player->GetID() . "'>" . $player->GetName() . "</a> gekauft. <br/>Du hast " . number_format($item->GetGebot(), 0, '', '.') . " " . $waehrung . " zurückerhalten.";
                            $text = "<center>" . $text . "</center>";
                            $PMManager->SendPM(0, 'img/money.png', 'SYSTEM', $item->GetName() . ' wurde leider verkauft.', $text, $offerPlayer->GetName(), 1);

                        }
                        $player->BuyItemFrom($itemManager->GetItem($item->GetStatsID()), $itemManager->GetItem($item->GetVisualID()), $statstype, $upgrade, $amount, $price, $item->GetSellerID(), $owners);
                        $market->TakeItem($_POST['id'], $amount);

                        $text = "<img src='img/items/" . $item->GetImage() . ".png' width='80px' height='80px'>Du hast " . number_format($amount, '0', '', '.') . "x " . $item->GetName() . " für " . number_format($price, '0', '', '.') . " " . $waehrung . " an <a href='?p=profil&id=" . $player->GetID() . "'>" . $player->GetName() . "</a> verkauft.";
                        $text = "<center>" . $text . "</center>";
                        $PMManager->SendPM(0, 'img/money.png', 'SYSTEM', $item->GetName() . ' wurde verkauft.', $text, $seller->GetName(), 1);

                        $charaids = array();
                        $charaids[] = $player->GetID();
                        $charaids[] = $item->GetSellerID();
                        $waehrung = "Berry";
                        if ($item->IsPremium())
                            $waehrung = "Gold";
                        $marktkauf = 'Marktkauf ' . number_format($amount, '0', '', '.') . 'x ' . $item->GetName() . ' für ' . number_format($price, '0', '', '.') . ' ' . $waehrung . ' gekauft';
                        LoginTracker::AddInteraction($accountDB, $charaids, $marktkauf, 'opbg');
                        $player->AddDebugLog(date('H:i:s', time()) . " - Marktkauf: " . $item->GetName() . " - Anzahl: " . $amount . " - Einzelpreis: " . $item->GetPrice() . " - Gesamtpreis: " . $price);

                        $message = 'Du hast ' . number_format($amount, '0', '', '.') . 'x ' . $item->GetName() . ' gekauft.';
                    }
                }
            }
        }
    }
    else if (isset($_GET['a']) && $_GET['a'] == 'remove')
    {
        if (!isset($_POST['id']) || !is_numeric($_POST['id']))
        {
            $message = 'Das Item ist ungültig.';
        }
        else
        {
            $item = $market->GetItemByID($_POST['id']);
            if ($item == null)
            {
                $message = 'Das Item gibt es nicht auf dem Markt.';
            }
            else if($player->GetArank() < 3)
            {
                $message = 'Du bist dazu nicht berechtigt.';
            }
            else
            {
                $statsItem = $itemManager->GetItem($item->GetStatsID());
                $visualItem = $itemManager->GetItem($item->GetVisualID());
                $seller = new Player($database, $item->GetSellerID());
                $seller->AddItems($statsItem, $visualItem, $item->GetAmount(), $item->GetStatsType(), $item->GetUpgrade());
                $text = "Der Gegenstand ".$item->GetName()." wurde von ".$player->GetName()." vom Marktplatz entfernt.<br/>Der Gegenstand wurde deinem Inventar hinzugefügt.";
                $PMManager->SendPM(0, 'img/system.png', 'System', 'Marktplatz: Gegenstand entfernt', $text, $seller->GetName(), 1);
                $market->RemoveItem($_POST['id']);

                $message = 'Du hast ' . number_format($item->GetAmount(),'0', '', '.') . 'x ' . $item->GetName() . ' vom Marktplatz entfernt.';
            }
        }
    }
    else if (isset($_GET['a']) && $_GET['a'] == 'retake')
    {
        if (!isset($_POST['id']) || !is_numeric($_POST['id']))
        {
            $message = 'Das Item ist ungültig.';
        }
        else if (!isset($_POST['amount']) || !is_numeric($_POST['amount']) || floor($_POST['amount']) <= 0)
        {
            $message = 'Die Anzahl ist ungültig.';
        }
        else
        {
            $item = $market->GetItemByID($_POST['id']);
            $amount = floor($_POST['amount']);
            if ($item == null)
            {
                $message = 'Das Item gibt es nicht auf dem Markt.';
            }
            else if ($item->GetSellerID() != $player->GetID())
            {
                $message = 'Das item gehört dir nicht.';
            }
            else if ($amount > $item->GetAmount())
            {
                $message = 'So viele Items werden im Markt nicht angeboten.';
            }
            else if($item->GetBieter() != 0)
            {
                $message = 'Es gibt bereits ein Gebot auf dieses Item.';
            }
            else
            {
                $statsItem = $itemManager->GetItem($item->GetStatsID());
                $visualItem = $itemManager->GetItem($item->GetVisualID());
                $player->AddItems($statsItem, $visualItem, $amount, $item->GetStatsType(), $item->GetUpgrade());
                $market->TakeItem($_POST['id'], $amount);

                $message = 'Du hast ' . number_format($amount,'0', '', '.') . 'x ' . $item->GetName() . ' zurückgenommen.';
            }
        }
    }
