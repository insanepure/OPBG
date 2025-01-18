<?php

if ($database == NULL)
{
    print 'This File (' . __FILE__ . ') should be after Database!';
}
if ($actionManager == NULL)
{
    print 'This File (' . __FILE__ . ') should be after ActionManager!';
}
if ($account == NULL)
{
    print 'This File (' . __FILE__ . ') should be after Account!';
}

include_once 'playerinventory.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/items/itemmanager.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/fight/attackmanager.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/titel/titelmanager.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pms/pmmanager.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';

class Player
{

    private $database;
    private $data;
    private $valid;
    private $needinventory;
    private $inventory;
    private $actionManager;
    private $isChat;
    private $localplayer;
    private $backupTime;
    private $resetTime;

    private $startLog = '';

    function __construct($db, $id = 0, $actionManager = null, $isChat = false, $userid = 0, $needinventory = true)
    {
        $id = $db->EscapeString($id);
        $this->actionManager = $actionManager;
        $this->valid = false;
        $this->needinventory = $needinventory;
        $this->localplayer = false;
        $this->database = $db;
        $this->isChat = $isChat;
        $this->data = array();
        $this->data['id'] = $id;
        $this->data['debuglog'] = '';
        $this->backupTime = strtotime('2022-07-05 03:00:00');
        $this->resetTime = strtotime('2022-07-06 18:00:00');
        $key = 'id';
        if ($id == 0 && $userid != 0)
        {
            $key = 'session';
            $this->data[$key] = session_id();
            $id = session_id();
            $this->localplayer = true;
        }
        $this->LoadPlayer($key, '*', $id);

        if (!$this->valid && isset($_POST['GoogleCrawler']))
        {
            $googleChara = 595;
            $this->data['id'] = $googleChara;
            $this->Login(true);
            $key = 'id';
            $this->LoadPlayer($key, '*', $googleChara);
        }

        if (!$this->valid && isset($_COOKIE['ocharaid']) && is_numeric($_COOKIE['ocharaid']))
        {
            $id = $_COOKIE['ocharaid'];
            $result = $this->database->Select('userid, arank, id, adminlogged, session', 'accounts', 'id = ' . $id . 'AND arank >= 2 AND userid=' . $userid . ' AND session="'. session_id() .'" AND adminlogged=0', 1);
            if ($result && $result->num_rows > 0)
            {
                $row = $result->fetch_assoc();
                $key = 'id';
                $this->data['id'] = $id;
                $this->LoadPlayer($key, '*', $id);
            }
            else
            {
                $date_of_expiry = time() - 10;
                setcookie("ocharaid", "", $date_of_expiry);
                $this->Logout();
            }
        }

        $this->CheckMulti();

        if ($this->valid && $this->IsBanned() && !$this->IsAdminLogged())
        {
            $this->Logout();
            //$this->valid = false;
            //$this->data['id'] = 0;
        }

        $this->startLog = $this->GetDebugLog();
    }

    function __destruct()
    {
      /*
        $debuglog = $this->GetDebugLog();
        if ($debuglog == $this->startLog)
            return;

        $this->database->Update('debuglog="' . $debuglog . '"', 'accounts', 'id = ' . $this->GetID() . '', 1);
        */
    }

    public function DebugSend($withText = false, $title = '')
    {
        if ($withText)
        {
            echo 'In Euren Kampf kam es zu einen Fehler. Der Fehler muss manuell behoben werden, die Administration wurde informiert.<br/>';
            echo 'Falls du diese Nachricht siehst, melde dich bei Shirobaka im Discord.<br/>';
        }

        $timestamp = date('Y-m-d H:i:s');
        if ($title == '')
            $title = 'Error In Player: ' . $this->GetName() . '(' . number_format($this->GetID(), '0', '', '.') . ')';
        $isHTML = 1;
        $this->database->Insert(
            '`sendername`, `senderimage`, `senderid`, `receiverid`, `receivername`, `text`, `time`, `topic`, `read`, `ishtml`',
            '"System","img/battel2.png","0","1","AzaX","' . $this->GetDebugLog() . '","' . $timestamp . '","' . $title . '", "0","' . $isHTML . '"',
            'pms'
        );
        $this->database->Insert(
            '`sendername`, `senderimage`, `senderid`, `receiverid`, `receivername`, `text`, `time`, `topic`, `read`, `ishtml`',
            '"System","img/battel2.png","0","506","ShirobakaX","' . $this->GetDebugLog() . '","' . $timestamp . '","' . $title . '", "0","' . $isHTML . '"',
            'pms'
        );
    }

    public function AddDebugLog($text)
    {
        $text = $this->database->EscapeString($text);
        $debugLog = $this->GetDebugLog();
        if ($debugLog == '')
            $debugLog = $text;
        else
            $debugLog = $debugLog . '<br/>' . $text;

        $this->SetDebugLog($debugLog);
    }

    public function IsBlocked($id)
    {
        $blocked = explode(';', $this->data['blocked']);
        return in_array($id, $blocked);
    }

    public function Block($id)
    {
        $blocked = explode(';', $this->data['blocked']);
        if (!in_array($id, $blocked))
        {
            $blocked[] = $id;
            $blocked = implode(';', $blocked);
            $update = 'blocked="' . $blocked . '"';

            $friends = explode(';', $this->GetFriends());
            if(in_array($id, $friends))
            {
                $this->UnFriend($id);
                $otherPlayer = new Player($this->database, $id);
                $otherPlayer->UnFriend($this->GetID());
            }

            $this->database->Update($update, 'accounts', 'id = ' . $this->data['id'] . '', 1);
        }
    }

    public function UnBlock($id)
    {
        $blocked = explode(';', $this->data['blocked']);
        if (in_array($id, $blocked))
        {
            $key = array_search($id, $blocked);
            array_splice($blocked, $key, 1);
            $blocked = implode(';', $blocked);
            $update = 'blocked="' . $blocked . '"';
            $this->database->Update($update, 'accounts', 'id = ' . $this->data['id'] . '', 1);
        }
    }

    public function SetNewName($value)
    {
        $result = $this->database->Update('name="'.$value.'"', 'accounts', 'id="'.$this->GetID().'"');
    }

    public function Friend($id)
    {
        if ($this->data['friends'] != '') $friends = explode(";", $this->data['friends']);
        else $friends = array();

        if (!in_array($id, $friends))
        {
            $friends[] = $id;
            $friends = implode(';', $friends);
            $update = 'friends="' . $friends . '"';
            $this->database->Update($update, 'accounts', 'id = ' . $this->data['id'] . '', 1);
        }
    }

    public function UnFriend($id)
    {
        $friends = explode(';', $this->data['friends']);
        if (in_array($id, $friends))
        {
            $key = array_search($id, $friends);
            array_splice($friends, $key, 1);
            $friends = implode(';', $friends);
            $update = 'friends="' . $friends . '"';
            $this->database->Update($update, 'accounts', 'id = ' . $this->data['id'] . '', 1);
        }
    }

    public function IsFriend($id): bool
    {
        $friends = explode(';', $this->data['friends']);
        return in_array($id, $friends);
    }

    public function IsDonator(): bool
    {
        if($this->HasTitel(76) || $this->GetArank() >= 2) return true;
        return false;
    }

    public function SetDonator($value): void
    {
        $titels = $this->GetTitels();
        if($value == 1)
        {
            if($titels != '')
            {
                $titels[] = 76;
                $titels = implode(";", $titels);
                $this->SetTitels($titels);
                $this->database->Update('titels="'.$titels.'"', 'accounts', 'id='.$this->GetID());
            }
        }
        else
        {
            if($this->HasTitel(76)) {
                if (($key = array_search(76, $titels)) !== false) {
                    unset($titels[$key]);
                }
                $titels = implode(";", $titels);
                $this->database->Update('titels="' . $titels . '"', 'accounts', 'id=' . $this->GetID());
            }
        }
    }

    public function IsMulti($otherPlayer)
    {
        return $otherPlayer->GetUserID() == $this->GetUserID();
    }

    public function IsMultiChar()
    {
        return $this->data['ismulti'];
    }

    public function IsMulticharBande()
    {
        $bande = "";
        if($this->IsMultiChar())
        {
            $MultiCheck = $this->database->Select('*', 'accounts', 'userid="'.$this->GetUserID().'" AND ismulti=0 AND arank < 2');
            $multis = $MultiCheck->fetch_assoc();
            $bande = $multis['clan'];
        }
        return $bande;
    }

    public function GetLastNPCID()
    {
        return $this->data['lastnpcid'];
    }

    public function SetLastNPCID($id)
    {
        $this->data['lastnpcid'] = $id;
        $result = $this->database->Update('lastnpcid="'.$id.'"', 'accounts', 'id="'.$this->GetID().'"');
    }

    public function RecycleEquip($id)
    {
        $result = $this->database->Delete('inventory', 'id="'.$id.'" AND ownerid="'.$this->GetID().'"', 1);
    }

    public function GetUserID(): int
    {
        return $this->data['userid'];
    }

    public function GetBlackBoard()
    {
        return $this->data['blackboard'];
    }

    public function DeleteAdminBlackboard($id)
    {
        $this->database->Update('blackboard="nichts"', 'accounts', 'id='.$id);
    }

    public function SetBlackBoardText($value)
    {
        $this->data['blackboard'] = $value;
        $result = $this->database->Update('blackboard="'.$value.'"', 'accounts', 'id="'.$this->GetID().'"');
    }

    public function DeleteBlackBoard()
    {
        $result = $this->database->Update('blackboard="nichts"', 'accounts', 'id="'.$this->GetID().'"');
    }

    public function GetDailyEloFights()
    {
        return $this->data['dailyelofights'];
    }

    public function SetDailyEloFights($value)
    {
        $this->data['dailyelofights'] = $value;
        $this->database->Update('dailyelofights="'.$value.'"', 'accounts', 'id="'.$this->GetID().'"');
        if($value >= 6)
        {
            $this->AddMeldung("Fightid: ".$this->GetFight()." - dailyelofights >= 6 - bei Player-ID: <a href='?p=profil&id=" . $this->GetID() . "'>" . $this->GetID() . "</a>", $this->GetID(), "System", 2);
        }
    }

    public function GetDailyMaxElofights()
    {
        return $this->data['dailymaxelofights'];
    }

    public function SetDailyMaxEloFights($value)
    {
        $this->data['dailymaxelofights'] = $value;
        $this->database->Update('dailymaxelofights="'.$value.'"', 'accounts', 'id="'.$this->GetID().'"');
    }

    public function GetBookingDays()
    {
        return $this->data['bookingdays'];
    }

    public function SetBookingDays($value)
    {
        $this->data['bookingdays'] = $value;
        $this->database->Update('bookingdays="'.$value.'"', 'accounts', 'id="'.$this->GetID().'"');
    }

    public function GetDebugLog()
    {
        return $this->data['debuglog'];
    }

    public function TicketAnswer()
    {
        $id = $this->data['id'];
        $check = $this->database->Select('*', 'ticket', 'ersteller="'.$id.'" AND active=0 AND gelesen=0');
        return $check->num_rows;
    }

    public function GetRabatt()
    {
        return $this->data['casino_rabatt'];
    }

    public function GetReadRules()
    {
        return $this->data['read_rules'];
    }

    public function SetReadRules($value)
    {
        $this->data['read_rules'] = $value;
    }

    public function SetRabatt($value)
    {
        $this->data['casino_rabatt'] = $value;
        $this->database->Update('casino_rabatt="'.$value.'"', 'accounts', 'id="'.$this->data['id'].'"');
    }

    public function GetRabattLoseCount()
    {
        return $this->data['casino_lose_count'];
    }

    public function SetRabattLoseCount($value)
    {
        $this->data['casino_lose_count'] = $value;
        $this->database->Update('casino_lose_count="'.$value.'"', 'accounts', 'id="'.$this->data['id'].'"');
    }

    public function GetCasinoOutOfMoney()
    {
        return $this->data['casino_out_of_money'];
    }

    public function SetCasinoOutOfMoney($value)
    {
        $this->data['casino_out_of_money'] = $value;
        $this->database->Update('casino_out_of_money="'.$value.'"', 'accounts', 'id="'.$this->data['id'].'"');
    }

    public function SetDebugLog($value)
    {
        $this->data['debuglog'] = $value;
    }

    public function IsLocalPlayer(): bool
    {
        return $this->localplayer;
    }

    public function Login($stayLogged, $adminLogin = 0)
    {
        if ($stayLogged)
        {
            $date_of_expiry = time() + 60 * 60 * 24 * 30;
            setcookie("ocharaid", $this->data['id'], $date_of_expiry);
        }
        else
        {
            $date_of_expiry = time() - 10;
            setcookie("ocharaid", "", $date_of_expiry);
        }

        if ($adminLogin != 0)
        {
            $this->data['adminlogged'] = $adminLogin;
        }
        $update = 'session="' . session_id() . '",adminlogged="' . $adminLogin . '"';
        $this->database->Update($update, 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function Logout()
    {
        $this->database->Update('session="",adminlogged="0"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function SetMultiText($value)
    {
        $this->data['multitext'] = $value;
        $this->database->Update('multitext="'.$value.'"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function GetMultiText()
    {
        return $this->data['multitext'];
    }

    public function GetSitter()
    {
        return $this->data['sitter'];
    }

    public function HasTeleschnecke(): bool
    {
        $items = $this->data['useritems'];
        $items = explode(";", $items);
        return (in_array(10, $items));
    }

    public function HasEastBlueMap(): bool
    {
        $items = $this->data['useritems'];
        $items = explode(";", $items);
        return (in_array(36, $items));
    }

    public function HasVivreCard(): bool
    {
        $items = $this->data['useritems'];
        $items = explode(";", $items);
        return (in_array(510, $items));
    }

    public function HasSouthBlueMap(): bool
    {
        $items = $this->data['useritems'];
        $items = explode(";", $items);
        return (in_array(38, $items));
    }

    public function HasWestBlueMap(): bool
    {
        $items = $this->data['useritems'];
        $items = explode(";", $items);
        return (in_array(39, $items));
    }

    public function HasNorthBlueMap(): bool
    {
        $items = $this->data['useritems'];
        $items = explode(";", $items);
        return (in_array(37, $items));
    }

    public function HasImpeldownMap(): bool
    {
        $items = $this->data['useritems'];
        $items = explode(";", $items);
        return (in_array(72, $items));
    }

    public function HasLogPort(): bool
    {
        $items = $this->data['useritems'];
        $items = explode(";", $items);
        return (in_array(85, $items));
    }

    public function GetTravelTicket()
    {
        return $this->data['usetravelticket'];
    }

    public function UseTravelTicket($value)
    {
        $this->data['usetravelticket'] = $value;
        $this->database->Update('usetravelticket="'.$value.'"', 'accounts', 'id="'.$this->data['id'].'"');
    }

    public function CheckMulti()
    {
        if (!$this->IsLocalPlayer())
        {
            return;
        }

        $multiWhere = '';
        if (isset($this->data['multiaccounts']))
        {
            $multiAccs = explode(';', $this->data['multiaccounts']);
            $i = 0;
            while (isset($multiAccs[$i]))
            {
                $multiString = 'chara != "' . $multiAccs[$i] . '"';
                if ($multiWhere == '')
                {
                    $multiWhere = ' AND ' . $multiString;
                }
                else
                {
                    $multiWhere = $multiWhere . ' AND ' . $multiString;
                }
                ++$i;
            }
        }
    }

    public function Ban($reason): void
    {
        $banned = 1;
        $this->SetBanned($banned);
        $this->SetBanReason($reason);
        $this->database->Update('banned="' . $banned . '",banreason="' . $reason . '"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
        $this->database->Update('igentry=1', 'statslist', 'acc='.$this->GetID());
    }

    public function HasItem($id)
    {
        return $this->inventory->HasItem($id);
    }

    public function HasItemWithID($statsid, $visualid)
    {
        return $this->inventory->HasItemWithID($statsid, $visualid);
    }

    public function GetItemByIDOnly($statsid, $visualid) : ?InventoryItem
    {
        return $this->GetInventory()->GetItemByIDOnly($statsid, $visualid);
    }

    public function GetItemByStatsIDOnly($statsid) : ?InventoryItem
    {
        return $this->GetInventory()->GetItemByStatsIDOnly($statsid);
    }

    public function GetItemByVisualIDOnly($visualid) : ?InventoryItem
    {
        return $this->GetInventory()->GetItemByVisualIDOnly($visualid);
    }

    public function SpeedUpAction($minutes, $berry, $travel = 0)
    {
        $this->AddDebugLog('SpeedUpAction');
        if ($travel == 0)
        {
            $actionTime = $this->GetActionTime() - $minutes;
            $berry = $this->GetBerry() - $berry;
            if ($actionTime < 0) $actionTime = 0;

            $this->AddDebugLog(' - Berry From ' . number_format($this->GetBerry(), '0', '', '.') . ' to ' . number_format($berry, '0', '', '.'));
            $this->SetBerry($berry);
            $this->AddDebugLog(' - ActionTime From ' . $this->GetActionTime() . ' to ' . $actionTime);
            $this->SetActionTime($actionTime);

            $this->AddDebugLog(' ');
            $update = 'zeni="' . $berry . '", actiontime="' . $actionTime . '"';
            $this->database->Update($update, 'accounts', 'id = ' . $this->data['id'] . '', 1);
        }
        else
        {
            $actionTime = $this->GetTravelActionTime() - $minutes;
            $berry = $this->GetBerry() - $berry;
            if ($actionTime < 0) $actionTime = 0;

            $this->AddDebugLog(' - Berry From ' . number_format($this->GetBerry(), '0', '', '.') . ' to ' . number_format($berry, '0', '', '.'));
            $this->SetBerry($berry);
            $this->AddDebugLog(' - ActionTime From ' . $this->GetTravelActionTime() . ' to ' . $actionTime);
            $this->SetTravelActionTime($actionTime);

            $this->AddDebugLog(' ');
            $this->SetTravelSpeededUp(1);
            $update = 'zeni="' . $berry . '", travelactiontime="' . $actionTime . '", speededup="1"';
            $this->database->Update($update, 'accounts', 'id = ' . $this->data['id'] . '', 1);
        }
    }

    public function SetTravelSpeededUp($value)
    {
        $this->data['speededup'] = $value;
    }

    public function GiveItem($id, $amount)
    {
        $result = $this->database->Select('*', 'inventory', 'ownerid="' . $this->GetID() . '" AND statsid="' . $id . '" AND visualid="' . $id . '"', 1);
        $inventar = $result->fetch_assoc();
        $amount = $inventar['amount'] - $amount;
        if ($amount > 0) {
            $this->database->Update('amount="' . $amount . '"', 'inventory', 'ownerid="' . $this->GetID() . '" AND statsid="' . $id . '" AND visualid="' . $id . '"');
        } else {
            $this->database->Delete('inventory', 'ownerid="' . $this->GetID() . '" AND statsid="' . $id . '" AND visualid="' . $id . '"');
        }
    }

    public function IsTeam($value)
    {
        if($this->GetArank($value) > 0)
        {
            return TRUE;
        }
    }

    public function TravelSpeededUp()
    {
        return $this->data['speededup'];
    }

    public function HasStatsResetted()
    {
        return $this->data['statsresetted'];
    }

    public function SetStatsResetted($value)
    {
        $this->data['statsresetted'] = $value;
    }

    public function GetResettedStatsAmount()
    {
        return $this->data['resetamount'];
    }

    public function SetResettedStatsAmount($amount)
    {
        $this->data['resetamount'] = $amount;
    }

    public function GetEloTournament($value)
    {
        $all = explode(";", $this->data['elotournament']);
        $answer =  $all[$value];
        return $answer;
    }

    public function SetEloTournament($pos, $value)
    {
      $CheckArray = explode(";", $this->data['elotournament']);
      $CheckArray[$pos] = $value;
      $SaveArray = implode(";", $CheckArray);
      $this->database->Update('elotournament="'.$SaveArray.'"', 'accounts', 'id="'.$this->GetID().'"');
    }

    public function SetAllEloTournamenBonus()
    {
        if($this->GetArank() >= 0) {
            if ($this->GetEloPoints() > 500 && $this->GetEloTournament(0) == 0)
            {
                $this->AddItems(60, 60, 1);
                $this->SetEloTournament(0, 1);
            }
            else if ($this->GetEloPoints() >= 1250 && $this->GetEloTournament(1) == 0) {
                $this->AddItems(60, 60, 1);
                $this->SetEloTournament(1, 1);
            }
            else if($this->GetEloPoints() > 2250 && $this->GetEloTournament(2) == 0)
            {
                $this->AddItems(315, 315, 1);
                $this->SetEloTournament(2, 1);
            }
            else if($this->GetEloPoints() > 3500 && $this->GetEloTournament(3) == 0)
            {
                $this->AddItems(316, 316, 1);
                $this->SetEloTournament(3, 1);
            }
            else if($this->GetEloPoints() > 4750 && $this->GetEloTournament(4) == 0)
            {
                $this->AddItems(317, 317, 1);
                $this->SetEloTournament(4, 1);
            }
            else if($this->GetEloPoints() > 5500 && $this->GetEloTournament(5) == 0)
            {
                $this->AddItems(102, 102, 1);
                $this->SetEloTournament(5, 1);
            }
            else if($this->GetEloPoints() > 5500 && $this->GetEloTournament(6) == 0)
            {
                $this->AddItems(102, 102, 1);
                $this->SetEloTournament(6, 1);
            }
            else if($this->GetEloPoints() > 6500 && $this->GetEloTournament(7) == 0)
            {
                $this->AddItems(302, 302, 1);
                $this->SetEloTournament(7, 1);
            }
            else if($this->GetEloPoints() > 7500 && $this->GetEloTournament(8) == 0)
            {
                $this->AddItems(82, 82, 1);
                $this->SetEloTournament(8, 1);
            }
            else if($this->GetEloPoints() > 8500 && $this->GetEloTournament(9) == 0)
            {
                $this->AddItems(81, 81, 1);
                $this->SetEloTournament(9, 1);
            }
            else if($this->GetEloPoints() > 9750 && $this->GetEloTournament(10) == 0)
            {
                $this->AddItems(86, 86, 1);
                $this->SetEloTournament(10, 1);
            }
            else if($this->GetEloPoints() > 11000 && $this->GetEloTournament(11) == 0)
            {
                $this->AddItems(87, 87, 1);
                $this->SetEloTournament(11, 1);
            }
            else if($this->GetEloPoints() > 12250 && $this->GetEloTournament(12) == 0)
            {
                $this->AddItems(388, 388, 1);
                $this->AddItems(389, 389, 1);
                $this->AddItems(390, 390, 1);
                $this->SetEloTournament(12, 1);
            }
            else if($this->GetEloPoints() > 13750 && $this->GetEloTournament(13) == 0)
            {
                $this->AddItems(409, 409, 1);
                $this->SetEloTournament(13, 1);
            }
            else if($this->GetEloPoints() > 14200 && $this->GetEloTournament(14) == 0)
            {
                $this->AddItems(223, 223, 1);
                $this->SetEloTournament(14, 1);
            }
            else if($this->GetEloPoints() > 14200 && $this->GetEloTournament(15) == 0)
            {
                $this->AddItems(119, 119, 1);
                $this->SetEloTournament(15, 1);
            }
            else if($this->GetEloPoints() >= 15000 && $this->GetEloTournament(16) == 0)
            {
                $this->AddItems(174, 174, 1);
                $this->SetEloTournament(16, 1);
            }
        }
    }

    public function ResetStats()
    {
        $this->AddDebugLog('');
        $this->AddDebugLog('Reset Stats: ' . date('d.m.Y H:i:s',time()));

        $stats = 10;
        $mStats = $stats * 10;
        $aStats = $stats * 2;

        $statspointsAdd = ($this->GetMaxLP() / 10) - $stats;
        $statspointsAdd += ($this->GetMaxKP() / 10) - $stats;
        //$statspointsAdd += $this->GetAttack() - $stats;TODO: Reupload
        $statspointsAdd += ($this->GetAttack() / 2) - $stats;
        $statspointsAdd += $this->GetDefense() - $stats;

        $statspoints = $this->GetStats() + $statspointsAdd;

        $this->SetStats($statspoints);
        $this->AddDebugLog('LP: ' . number_format($this->GetMaxLP() / 10, 0, '', '.'));
        $this->AddDebugLog('AD: ' . number_format($this->GetMaxKP() / 10, 0, '', '.'));
        $this->AddDebugLog('ATK: ' . number_format($this->GetAttack() / 2, 0, '', '.'));
        $this->AddDebugLog('DEF: ' . number_format($this->GetDefense(), 0, '', '.'));
        $this->AddDebugLog('Stats: ' . number_format($statspoints, 0, '', '.'));
        $this->SetLP($mStats);
        $this->SetMaxLP($mStats);
        $this->SetKP($mStats);
        $this->SetMaxKP($mStats);
        //$this->SetAttack($stats); TODO: Reupload
        $this->SetAttack($aStats);
        $this->SetDefense($stats);
        // aStats zu Stats TODO: Reupload
        $update = 'stats="' . $statspoints . '"
		, lp="' . $mStats . '"
		, mlp="' . $mStats . '"
		, kp="' . $mStats . '"
		, mkp="' . $mStats . '"
		, attack="' . $aStats . '"
		, defense="' . $stats . '"
		, statsresetted="1"
		, resetamount="' . $statspointsAdd . '"
		, debuglog="'.$this->GetDebugLog().'"
		';
        $this->database->Update($update, 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function ResetSkills($pfad)
    {
        $allAttacks = explode(';', $this->GetAttacks());
        $fightattacks = explode(';', $this->GetFightAttacks());

        $skillPoints = $this->GetSkillPoints();
        $where = '';
        $resetp1 = 0;
        $resetp2 = 0;
        if ($pfad == 'Zoan')
        {
            $where = ' AND type = "1"';
            $resetp1 = 1;
        }
        else if ($pfad == 'Paramecia')
        {
            $where = ' AND type = "2"';
            $resetp1 = 1;
        }
        else if ($pfad == 'Logia')
        {
            $where = ' AND type = "3"';
            $resetp1 = 1;
        }
        else if ($pfad == 'Schwertkaempfer')
        {
            $where = ' AND type = "4"';
            $resetp2 = 1;
        }
        else if ($pfad == 'Schwarzfuss')
        {
            $where = ' AND type = "5"';
            $resetp2 = 1;
        }
        else if ($pfad == 'Karatekämpfer')
        {
            $where = ' AND type = "6"';
            $resetp2 = 1;
        }
        else if ($pfad == "Alle")
        {
            $resetp1 = 1;
            $resetp2 = 1;
        }

        $result = $this->database->Select('*', 'skilltree', 'attack != 0' . $where, 99999);
        if ($result)
        {
            if ($result->num_rows > 0)
            {
                while ($row = $result->fetch_assoc())
                {
                    $foundID = array_search($row['attack'], $allAttacks);
                    if ($foundID != false)
                    {
                        array_splice($allAttacks, $foundID, 1);
                        $skillPoints += $row['neededpoints'];
                    }
                    $foundID = array_search($row['attack'], $fightattacks);
                    if ($foundID != false)
                    {
                        array_splice($fightattacks, $foundID, 1);
                    }
                }
            }
            $result->close();
        }

        if(array_search(52, $fightattacks)) // Zoan->Fisch-Frucht
            array_splice($fightattacks, array_search(52, $fightattacks) ,1);
        if(array_search(762, $fightattacks)) // Zoan->Vogel-Frucht
            array_splice($fightattacks, array_search(762, $fightattacks) ,1);
        if(array_search(763, $fightattacks)) // Zoan->Mensch-Mensch-Frucht
            array_splice($fightattacks, array_search(763, $fightattacks) ,1);
        if(array_search(50, $fightattacks)) // Paramecia->Operations-Frucht
            array_splice($fightattacks, array_search(50, $fightattacks) ,1);
        if(array_search(760, $fightattacks)) // Paramecia->Faden-Frucht
            array_splice($fightattacks, array_search(760, $fightattacks) ,1);
        if(array_search(761, $fightattacks)) // Paramecia->Mochi-Frucht
            array_splice($fightattacks, array_search(761, $fightattacks) ,1);
        if(array_search(51, $fightattacks)) // Logia->Donner-Frucht
            array_splice($fightattacks, array_search(51, $fightattacks) ,1);
        if(array_search(764, $fightattacks)) // Logia->Feuer-Frucht
            array_splice($fightattacks, array_search(764, $fightattacks) ,1);
        if(array_search(765, $fightattacks)) // Logia->Gefrier-Frucht
            array_splice($fightattacks, array_search(765, $fightattacks) ,1);
        if(array_search(53, $fightattacks)) // Schwertkämpfer
            array_splice($fightattacks, array_search(53, $fightattacks) ,1);
        if(array_search(54, $fightattacks)) // Schwarzfuß
            array_splice($fightattacks, array_search(54, $fightattacks) ,1);
        if(array_search(55, $fightattacks)) // Karatekämpfer
            array_splice($fightattacks, array_search(55, $fightattacks) ,1);

        $fightattacks = implode(';', $fightattacks);

        $attacks = implode(';', $allAttacks);
        $attacks = str_replace(";;", "", $attacks);

        $this->SetFightAttacks($fightattacks);
        $this->SetAttacks($attacks);
        $this->SetSkillPoints($skillPoints);
        $preset = '';
        if ($resetp1 == 1)
            $preset = ', pfad="None"';
        if ($resetp2 == 1)
            $preset .= ', pfad2="None"';
        $update = 'fightattacks="' . $fightattacks . '", attacks="' . $attacks . '", skillpoints="' . $skillPoints . '"' . $preset;
        $this->database->Update($update, 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function UpdateStartingPowerup($attack)
    {
        $this->SetStartingPowerup($attack);
        $this->database->Update('powerupstart="' . $attack . '"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function UpdateFightAttacks($attacks)
    {
        $i = 0;
        $allAttacks = explode(';', $this->GetAttacks());
        $powerup = $this->GetStartingPowerup();
        $hasPowerup = false;
        while (isset($attacks[$i]))
        {
            if (!in_array($attacks[$i], $allAttacks))
            {
                return;
            }
            if ($attacks[$i] == $powerup)
            {
                $hasPowerup = true;
            }
            ++$i;
        }

        $attacks = implode(';', $attacks);
        $attacks = str_replace(";;", "", $attacks);
        $this->SetFightAttacks($attacks);
        $update = 'fightattacks="' . $attacks . '"';
        if (!$hasPowerup)
        {
            $update = $update . ',powerupstart=0';
            $this->SetStartingPowerup(0);
        }
        $this->database->Update($update, 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function ChestOpen($chest, $useamount = 1, $item = null)
    {
        $itemManager = new itemManager($this->database);
        $chestitem = $itemManager->GetItem($chest);
        $possibleItems = array();
        $canBeEmpty = false;
        $canBePicked = false;
        $cashstring = '';
        for($i = 4; $i < count($chestitem->GetSchatzitems()); ++$i)
        {
            array_push($possibleItems, $itemManager->GetItem($chestitem->GetSchatzitems()[$i]));
        }
        $canBeEmpty = $chestitem->GetSchatzitems()[0];
        $canBePicked = $chestitem->GetSchatzitems()[1];
        $berrymin = explode('@', $chestitem->GetSchatzitems()[2])[0];
        $berrymax = explode('@', $chestitem->GetSchatzitems()[2])[1];
        $goldmin = explode('@', $chestitem->GetSchatzitems()[3])[0];
        $goldmax = explode('@', $chestitem->GetSchatzitems()[3])[1];
        $gitem = null;
        $gotArray = array();
        if ($chest == 60)
        {
            $i = 0;
            while ($i < $useamount)
            {
                ++$i;
                $chestitem = rand(1, 100);
                $amount = 0;
                if ($chestitem <= 27)
                {
                    $this->inventory->RemoveItem($this->inventory->GetItemByStatsIDOnly(60), 1);
                    if (array_key_exists("Nichts", $gotArray))
                        $gotArray["Nichts"] += 1;
                    else
                        $gotArray["Nichts"] = 1;
                }
                else if ($chestitem > 27 && $chestitem <= 37)
                {
                    $amount = 1;
                    $gitem = $this->inventory->AddItem($itemManager->GetItem(71), $itemManager->GetItem(71), $amount);
                    $this->inventory->RemoveItem($this->inventory->GetItemByStatsIDOnly(60), 1);
                    if (array_key_exists($gitem->GetName(), $gotArray))
                        $gotArray[$gitem->GetName()] += $amount;
                    else
                        $gotArray[$gitem->GetName()] = $amount;
                }
                else if ($chestitem > 37 && $chestitem <= 41)
                {
                    $amount = 1;
                    $gitem = $this->inventory->AddItem($itemManager->GetItem(81), $itemManager->GetItem(81), $amount);
                    $this->inventory->RemoveItem($this->inventory->GetItemByStatsIDOnly(60), 1);
                    if (array_key_exists($gitem->GetName(), $gotArray))
                        $gotArray[$gitem->GetName()] += $amount;
                    else
                        $gotArray[$gitem->GetName()] = $amount;
                }
                else if ($chestitem > 41 && $chestitem <= 45)
                {
                    $amount = 1;
                    $gitem = $this->inventory->AddItem($itemManager->GetItem(82), $itemManager->GetItem(82), $amount);
                    $this->inventory->RemoveItem($this->inventory->GetItemByStatsIDOnly(60), 1);
                    if (array_key_exists($gitem->GetName(), $gotArray))
                        $gotArray[$gitem->GetName()] += $amount;
                    else
                        $gotArray[$gitem->GetName()] = $amount;
                }
                else if ($chestitem > 45 && $chestitem <= 49)
                {
                    $amount = 1;
                    $gitem = $this->inventory->AddItem($itemManager->GetItem(33), $itemManager->GetItem(33), $amount);
                    $this->inventory->RemoveItem($this->inventory->GetItemByStatsIDOnly(60), 1);
                    if (array_key_exists($gitem->GetName(), $gotArray))
                        $gotArray[$gitem->GetName()] += $amount;
                    else
                        $gotArray[$gitem->GetName()] = $amount;
                }
                else if ($chestitem > 49 && $chestitem <= 59)
                {
                    $amount = 1;
                    $gitem = $this->inventory->AddItem($itemManager->GetItem(31), $itemManager->GetItem(31), $amount);
                    $this->inventory->RemoveItem($this->inventory->GetItemByStatsIDOnly(60), 1);
                    if (array_key_exists($gitem->GetName(), $gotArray))
                        $gotArray[$gitem->GetName()] += $amount;
                    else
                        $gotArray[$gitem->GetName()] = $amount;
                }
                else if ($chestitem > 59 && $chestitem <= 64)
                {
                    $amount = 1;
                    $gitem = $this->inventory->AddItem($itemManager->GetItem(32), $itemManager->GetItem(32), $amount);
                    $this->inventory->RemoveItem($this->inventory->GetItemByStatsIDOnly(60), 1);
                    if (array_key_exists($gitem->GetName(), $gotArray))
                        $gotArray[$gitem->GetName()] += $amount;
                    else
                        $gotArray[$gitem->GetName()] = $amount;
                }
                else if ($chestitem > 64 && $chestitem <= 67)
                {
                    $amount = 1;
                    $gitem = $this->inventory->AddItem($itemManager->GetItem(302), $itemManager->GetItem(302), $amount);
                    $this->inventory->RemoveItem($this->inventory->GetItemByStatsIDOnly(60), 1);
                    if (array_key_exists($gitem->GetName(), $gotArray))
                        $gotArray[$gitem->GetName()] += $amount;
                    else
                        $gotArray[$gitem->GetName()] = $amount;
                }
                else if ($chestitem > 67 && $chestitem <= 68)
                {
                    $amount = 1;
                    $gitem = $this->inventory->AddItem($itemManager->GetItem(317), $itemManager->GetItem(317), $amount);
                    $this->inventory->RemoveItem($this->inventory->GetItemByStatsIDOnly(60), 1);
                    if (array_key_exists($gitem->GetName(), $gotArray))
                        $gotArray[$gitem->GetName()] += $amount;
                    else
                        $gotArray[$gitem->GetName()] = $amount;
                }
                else if ($chestitem > 68 && $chestitem <= 70)
                {
                    $amount = 1;
                    $gitem = $this->inventory->AddItem($itemManager->GetItem(316), $itemManager->GetItem(316), $amount);
                    $this->inventory->RemoveItem($this->inventory->GetItemByStatsIDOnly(60), 1);
                    if (array_key_exists($gitem->GetName(), $gotArray))
                        $gotArray[$gitem->GetName()] += $amount;
                    else
                        $gotArray[$gitem->GetName()] = $amount;
                }
                else if ($chestitem > 70 && $chestitem <= 73)
                {
                    $amount = 1;
                    $gitem = $this->inventory->AddItem($itemManager->GetItem(315), $itemManager->GetItem(315), $amount);
                    $this->inventory->RemoveItem($this->inventory->GetItemByStatsIDOnly(60), 1);
                    if (array_key_exists($gitem->GetName(), $gotArray))
                        $gotArray[$gitem->GetName()] += $amount;
                    else
                        $gotArray[$gitem->GetName()] = $amount;
                }
                else if ($chestitem > 73 && $chestitem <= 82)
                {
                    $amount = 1;
                    $gitem = $this->inventory->AddItem($itemManager->GetItem(49), $itemManager->GetItem(49), $amount);
                    $this->inventory->RemoveItem($this->inventory->GetItemByStatsIDOnly(60), 1);
                    if (array_key_exists($gitem->GetName(), $gotArray))
                        $gotArray[$gitem->GetName()] += $amount;
                    else
                        $gotArray[$gitem->GetName()] = $amount;
                }
                else if ($chestitem > 82 && $chestitem <= 91)
                {
                    $amount = 1;
                    $gitem = $this->inventory->AddItem($itemManager->GetItem(50), $itemManager->GetItem(50), $amount);
                    $this->inventory->RemoveItem($this->inventory->GetItemByStatsIDOnly(60), 1);
                    if (array_key_exists($gitem->GetName(), $gotArray))
                        $gotArray[$gitem->GetName()] += $amount;
                    else
                        $gotArray[$gitem->GetName()] = $amount;
                }
                else if ($chestitem > 91 && $chestitem <= 100)
                {
                    $amount = 1;
                    $gitem = $this->inventory->AddItem($itemManager->GetItem(51), $itemManager->GetItem(51), $amount);
                    $this->inventory->RemoveItem($this->inventory->GetItemByStatsIDOnly(60), 1);
                    if (array_key_exists($gitem->GetName(), $gotArray))
                        $gotArray[$gitem->GetName()] += $amount;
                    else
                        $gotArray[$gitem->GetName()] = $amount;
                }
            }
        } // Schatztruhe
        else if ($chest == 320)
        {
            $i = 0;
            while ($i < $useamount)
            {
                ++$i;
                $chestitem = rand(1, 100);
                $amount = 0;
                if ($chestitem <= 15)
                {
                    $this->inventory->RemoveItem($this->inventory->GetItemByStatsIDOnly(320), 1);
                    if (array_key_exists("Nichts", $gotArray))
                        $gotArray["Nichts"] += 1;
                    else
                        $gotArray["Nichts"] = 1;
                }
                else if ($chestitem > 15 && $chestitem <= 29)
                {
                    $amount = 1;
                    $gitem = $this->inventory->AddItem($itemManager->GetItem(71), $itemManager->GetItem(71), $amount);
                    $this->inventory->RemoveItem($this->inventory->GetItemByStatsIDOnly(320), 1);
                    if (array_key_exists($gitem->GetName(), $gotArray))
                        $gotArray[$gitem->GetName()] += $amount;
                    else
                        $gotArray[$gitem->GetName()] = $amount;
                }
                else if ($chestitem > 29 && $chestitem <= 37)
                {
                    $amount = 1;
                    $gitem = $this->inventory->AddItem($itemManager->GetItem(81), $itemManager->GetItem(81), $amount);
                    $this->inventory->RemoveItem($this->inventory->GetItemByStatsIDOnly(320), 1);
                    if (array_key_exists($gitem->GetName(), $gotArray))
                        $gotArray[$gitem->GetName()] += $amount;
                    else
                        $gotArray[$gitem->GetName()] = $amount;
                }
                else if ($chestitem > 37 && $chestitem <= 45)
                {
                    $amount = 1;
                    $gitem = $this->inventory->AddItem($itemManager->GetItem(82), $itemManager->GetItem(82), $amount);
                    $this->inventory->RemoveItem($this->inventory->GetItemByStatsIDOnly(320), 1);
                    if (array_key_exists($gitem->GetName(), $gotArray))
                        $gotArray[$gitem->GetName()] += $amount;
                    else
                        $gotArray[$gitem->GetName()] = $amount;
                }
                else if ($chestitem > 45 && $chestitem <= 49)
                {
                    $amount = 1;
                    $gitem = $this->inventory->AddItem($itemManager->GetItem(31), $itemManager->GetItem(31), $amount);
                    $this->inventory->RemoveItem($this->inventory->GetItemByStatsIDOnly(320), 1);
                    if (array_key_exists($gitem->GetName(), $gotArray))
                        $gotArray[$gitem->GetName()] += $amount;
                    else
                        $gotArray[$gitem->GetName()] = $amount;
                }
                else if ($chestitem > 49 && $chestitem <= 64)
                {
                    $amount = 1;
                    $gitem = $this->inventory->AddItem($itemManager->GetItem(31), $itemManager->GetItem(31), $amount);
                    $this->inventory->RemoveItem($this->inventory->GetItemByStatsIDOnly(320), 1);
                    if (array_key_exists($gitem->GetName(), $gotArray))
                        $gotArray[$gitem->GetName()] += $amount;
                    else
                        $gotArray[$gitem->GetName()] = $amount;
                }
                else if ($chestitem > 64 && $chestitem <= 67)
                {
                    $amount = 1;
                    $gitem = $this->inventory->AddItem($itemManager->GetItem(8), $itemManager->GetItem(8), $amount);
                    $this->inventory->RemoveItem($this->inventory->GetItemByStatsIDOnly(320), 1);
                    if (array_key_exists($gitem->GetName(), $gotArray))
                        $gotArray[$gitem->GetName()] += $amount;
                    else
                        $gotArray[$gitem->GetName()] = $amount;
                }
                else if ($chestitem > 67 && $chestitem <= 75)
                {
                    $amount = 1;
                    $gitem = $this->inventory->AddItem($itemManager->GetItem(103), $itemManager->GetItem(103), $amount);
                    $this->inventory->RemoveItem($this->inventory->GetItemByStatsIDOnly(320), 1);
                    if (array_key_exists($gitem->GetName(), $gotArray))
                        $gotArray[$gitem->GetName()] += $amount;
                    else
                        $gotArray[$gitem->GetName()] = $amount;
                }
                else if ($chestitem > 75 && $chestitem <= 85)
                {
                    $amount = 1;
                    $gitem = $this->inventory->AddItem($itemManager->GetItem(32), $itemManager->GetItem(32), $amount);
                    $this->inventory->RemoveItem($this->inventory->GetItemByStatsIDOnly(320), 1);
                    if (array_key_exists($gitem->GetName(), $gotArray))
                        $gotArray[$gitem->GetName()] += $amount;
                    else
                        $gotArray[$gitem->GetName()] = $amount;
                }
                else if ($chestitem > 85 && $chestitem <= 95)
                {
                    $amount = 1;
                    $gitem = $this->inventory->AddItem($itemManager->GetItem(315), $itemManager->GetItem(315), $amount);
                    $this->inventory->RemoveItem($this->inventory->GetItemByStatsIDOnly(320), 1);
                    if (array_key_exists($gitem->GetName(), $gotArray))
                        $gotArray[$gitem->GetName()] += $amount;
                    else
                        $gotArray[$gitem->GetName()] = $amount;
                }
                else if ($chestitem > 95 && $chestitem <= 98)
                {
                    $amount = 1;
                    $gitem = $this->inventory->AddItem($itemManager->GetItem(316), $itemManager->GetItem(316), $amount);
                    $this->inventory->RemoveItem($this->inventory->GetItemByStatsIDOnly(320), 1);
                    if (array_key_exists($gitem->GetName(), $gotArray))
                        $gotArray[$gitem->GetName()] += $amount;
                    else
                        $gotArray[$gitem->GetName()] = $amount;
                }
                else if ($chestitem > 98 && $chestitem <= 100)
                {
                    $amount = 1;
                    $gitem = $this->inventory->AddItem($itemManager->GetItem(317), $itemManager->GetItem(317), $amount);
                    $this->inventory->RemoveItem($this->inventory->GetItemByStatsIDOnly(320), 1);
                    if (array_key_exists($gitem->GetName(), $gotArray))
                        $gotArray[$gitem->GetName()] += $amount;
                    else
                        $gotArray[$gitem->GetName()] = $amount;
                }
            }
        } // Kolosseumtruhe
        else //if ($chest == 102 || $chest == 318 || $chest == 319 || $chest == 103 || $chest == 356 || $chest == 357 || $chest == 355 || $chest == 358 || $chest == 359 || $chest == 360)
        {
            if($canBeEmpty)
                array_push($possibleItems, 'Nichts');
            if($berrymin > 0 && $berrymax > 0)
                array_push($possibleItems, 'Berry');
            if($goldmin > 0 && $goldmax > 0)
                array_push($possibleItems, 'Gold');
            $i = 0;
            while ($i < $useamount)
            {
                ++$i;
                $amount = 1;
                $max = count($possibleItems) - 1;
                if($canBePicked)
                    $gotItem = array_search($itemManager->GetItem($item), $possibleItems);
                else
                    $gotItem = rand(0, $max);
                if($canBeEmpty && $possibleItems[$gotItem] == 'Nichts')
                {
                    if (array_key_exists("Nichts", $gotArray))
                        $gotArray["Nichts"] += 1;
                    else
                        $gotArray["Nichts"] = 1;
                }
                else if($berrymin > 0 && $berrymax > 0 && $possibleItems[$gotItem] == 'Berry')
                {
                    $berry = rand($berrymin, $berrymax);
                    if (array_key_exists("Berry", $gotArray))
                        $gotArray["Berry"] += $berry;
                    else
                        $gotArray["Berry"] = $berry;
                }
                else if($goldmin > 0 && $goldmax > 0 && $possibleItems[$gotItem] == 'Gold')
                {
                    $gold = rand($goldmin, $goldmax);
                    if (array_key_exists("Gold", $gotArray))
                        $gotArray["Gold"] += $gold;
                    else
                        $gotArray["Gold"] = $gold;
                }
                else
                {
                    $gitem = $this->inventory->AddItem($possibleItems[$gotItem], $possibleItems[$gotItem], $amount);
                    if (array_key_exists($gitem->GetName(), $gotArray))
                        $gotArray[$gitem->GetName()] += $amount;
                    else
                        $gotArray[$gitem->GetName()] = $amount;
                }
                $this->RemoveItems($this->inventory->GetItemByStatsIDOnly($chestitem->GetID()), 1);
            }
            $nArray = $gotArray;
            while ($citem = current($nArray))
            {
                if (key($nArray) == "Gold")
                {
                    $this->SetGold($this->GetGold() + $citem);
                    $this->database->Update('gold=' . $this->GetGold(), 'accounts', 'id = ' . $this->GetID() . '', 1);
                    $cashstring .= 'Gold erhalten: ' . number_format($citem, 0, '', '.');

                }
                if (key($nArray) == "Berry")
                {
                    $this->SetBerry($this->GetBerry() + $citem);
                    $this->database->Update('zeni=' . $this->GetBerry(), 'accounts', 'id = ' . $this->GetID() . '', 1);
                    $cashstring .= 'Berry erhalten: ' . number_format($citem, 0, '', '.');
                }
                next($nArray);
            }
        }
        $this->AddDebugLog(date("H:i:s", time())." - Kiste geöffnet: Enthaltener Gegenstand: ".key($gotArray)." - Anzahl: ".$gotArray[key($gotArray)] . $cashstring);
        return $gotArray;
    }

    public function MultiAccounts()
    {
        $userid = $this->data['userid'];
        $id = $this->data['id'];
        $multi = $this->database->Select('*', 'accounts', 'id <> '.$id.' AND userid='.$userid);
        if($multi)
        {
            if($multi->num_rows != 0)
            {
                echo "Multis:";
                while($multis = $multi->fetch_assoc())
                {
                    echo " <a href='?p=profil&id=".$multis['id']."'>".$multis['name']."</a><br />";
                }
            }
        }
    }

    public function DiscordIDVerified()
    {
        return $this->data['discordverified'];
    }

    public function GetDiscordID()
    {
        return $this->data['discordid'];
    }

    public function GetNewDiscordID()
    {
        return $this->data['newdiscordid'];
    }

    public function GetDiscordCode()
    {
        return $this->data['discordcode'];
    }

    public function SetDiscordCode($value = '')
    {
        $this->data['discordcode'] = $value;
        if($value == '')
            $this->data['discordcode'] = '';
    }

    public function SetNewDiscordID($value)
    {
        $this->data['newdiscordid'] = $value;
    }

    public function SetDiscordID($value)
    {
        $this->data['discordid'] = $value;
    }

    public function IsDiscordReassignable(): int
    {
        $lastReassign = strtotime($this->data['discordreassign']);
        $time = time() - $lastReassign;
        $time = ($time < 1) ? 1 : $time;
        return $time > 600 ? 1 : 0;
    }

    public function SetDiscordIDVerified($value)
    {
        $this->data['discordverified'] = $value;
    }

    public function LeaveGroup()
    {
        $group = $this->GetGroup();
        $where = '';
        $i = 0;

        while (isset($group[$i]))
        {
            if ($group[$i] == $this->GetID())
            {
                ++$i;
                continue;
            }

            if ($where == '')
            {
                $where = 'id = ' . $group[$i] . '';
            }
            else
            {
                $where = $where . ' OR id = ' . $group[$i] . '';
            }
            ++$i;
        }

        $key = array_search($this->GetID(), $group);
        array_splice($group, $key, 1);

        $groupSQL = '';
        $limit = 1;

        $leaderID = 0;
        if (count($group) != 1)
        {
            $groupSQL = implode(';', $group);
            $limit = count($group);
            $leaderID = $group[0];
        }
        $this->database->Update('`group`="' . $groupSQL . '"', 'accounts', $where, $limit);
        $this->database->Update('`group`="", `groupleader`="0"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
        if ($this->IsGroupLeader() && $leaderID != 0)
        {
            $this->database->Update('`groupleader`=1', 'accounts', 'id = ' . $leaderID . '', 1);
        }
        $this->SetGroup('');
        $this->SetGroupLeader(false);
        return $groupSQL;
    }

    public function MakeGroupLeader()
    {
        $this->SetGroupLeader(true);
        $this->database->Update('`groupleader`="1"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }
    public function GiveupGroupLeader()
    {
        $this->SetGroupLeader(false);
        $this->database->Update('`groupleader`="0"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function AddToGroup($playerID)
    {
        $group = $this->GetGroup();
        if ($group == null)
        {
            $group = array();
            $group[0] = $this->GetID();
            $this->SetGroupLeader(true);
            $this->database->Update('`groupleader`="1"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
        }
        $group[] = $playerID;
        $groupSQL = implode(';', $group);
        $where = '';

        $i = 0;
        while (isset($group[$i]))
        {
            if ($where == '')
            {
                $where = 'id = ' . $group[$i] . '';
            }
            else
            {
                $where = $where . ' OR id = ' . $group[$i] . '';
            }
            ++$i;
        }
        $limit = count($group);

        $this->SetGroup($groupSQL);
        $this->database->Update('`group`="' . $groupSQL . '"', 'accounts', $where, $limit);
    }

    public function InviteToGroup($inviter)
    {
        $this->SetGroupInvite($inviter);
        $this->database->Update('groupinvite="' . $inviter . '"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function DeclineGroupInvite()
    {
        $this->SetGroupInvite(0);
        $this->database->Update('groupinvite="0"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function GetClanEloFights()
    {
        return $this->data['clanelofights'];
    }

    public function SetClanEloFights($value)
    {
        $this->data['clanelofights'] = $value;
        $this->database->Update('clanelofights='.$value, 'accounts', 'id='.$this->GetID());
    }

    public function AddDailyEloEnemys($target)
    {
        if($target == $this->GetID())
            return false;

        $enemys = explode(";", $this->data['dailyeloenemys']);
        $found = false;
        foreach($enemys as $key => $enemy)
        {
            $en = explode("@", $enemy);
            if($en[0] == $target)
            {
                $enm = explode("@", $enemy);
                $enm[1]++;
                $enemy = implode("@", $enm);
                $enemys[$key] = $enemy;
                $enemys = implode(";", $enemys);
                $this->database->Update("dailyeloenemys='".$enemys."'", "accounts", "id=".$this->GetID());
                $found = true;
                break;
            }
        }
        if(!$found)
        {
            $enemys[] = $target . '@1';
            $enemys = implode(";", $enemys);
            $this->database->Update("dailyeloenemys='".$enemys."'", "accounts", "id=".$this->GetID());
        }
        return true;
    }

    public function GetDailyEloEnemyCount($target)
    {
        $enemys = explode(";", $this->data['dailyeloenemys']);
        foreach($enemys as $enemy)
        {
            $en = explode("@", $enemy);
            if($en[0] == $target)
            {
                return $en[1];
            }
        }
        return 0;
    }

    public function AddArenaPoints($amount)
    {
        $this->AddDebugLog('Add Arenapoints');
        $points = $this->GetArenaPoints() + $amount;
        $this->SetArenaPoints($points);
        $this->AddDebugLog(' - Add Arenapoints: ' . number_format($amount, '0', '', '.'));
        $this->AddDebugLog(' - New Arenapoints: ' . number_format($points, '0', '', '.'));
        $this->AddDebugLog(' ');
        $this->database->Update('arenapoints="' . $points . '"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function RemoveArenaPoints($amount)
    {
        $this->AddDebugLog('Remove Arenapoints');
        $points = $this->GetArenaPoints() - $amount;
        $this->SetArenaPoints($points);
        $this->AddDebugLog(' - Remove Arenapoints: ' . number_format($amount, '0', '', '.'));
        $this->AddDebugLog(' - New Arenapoints: ' . number_format($points, '0', '', '.'));
        $this->AddDebugLog(' ');
        $this->database->Update('arenapoints="' . $points . '"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function AddBerry($amount)
    {
        $this->AddDebugLog('Add Berry');
        $berry = $this->GetBerry() + $amount;
        $this->SetBerry($berry);
        $this->AddDebugLog(' - Add Berry: ' . number_format($amount, '0', '', '.'));
        $this->AddDebugLog(' - New Berry: ' . number_format($berry, '0', '', '.'));
        $this->AddDebugLog(' ');
        $this->database->Update('zeni="' . $berry . '"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function RemoveBerry($amount)
    {
        $this->AddDebugLog('Remove Berry');
        $berry = $this->GetBerry() - $amount;
        $this->SetBerry($berry);
        $this->AddDebugLog(' - Remove Berry: ' . number_format($amount, '0', '', '.'));
        $this->AddDebugLog(' - New Berry: ' . number_format($berry, '0', '', '.'));
        $this->AddDebugLog(' ');
        $this->database->Update('zeni="' . $berry . '"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function RemoveGold($amount)
    {
        $this->AddDebugLog('Remove Gold');
        $gold = $this->GetGold() - $amount;
        $this->SetGold($gold);
        $this->AddDebugLog(' - Remove Gold: ' . number_format($amount, '0', '', '.'));
        $this->AddDebugLog(' - New Gold: ' . number_format($gold, '0', '', '.'));
        $this->AddDebugLog(' ');
        $this->database->Update('gold="' . $gold . '"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function AddGold($amount)
    {
        $this->AddDebugLog('Add Gold');
        $gold = $this->GetGold() + $amount;
        $this->SetGold($gold);
        $this->AddDebugLog(' - Add Gold: ' . number_format($amount, '0', '', '.'));
        $this->AddDebugLog(' - New Gold: ' . number_format($gold, '0', '', '.'));
        $this->AddDebugLog(' ');
        $this->database->Update('gold="' . $gold . '"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function CombineItems($statsitem, $visualitem)
    {
        $this->AddDebugLog('CombineItems');
        $this->AddDebugLog(' - Stats: ' . $statsitem->GetName() . ' (' . $statsitem->GetID() . ')');
        $this->AddDebugLog(' - Visual: ' . $visualitem->GetName() . ' (' . $visualitem->GetID() . ')');
        $this->AddDebugLog(' ');

        $this->inventory->CombineItems($statsitem, $visualitem);
    }

    public function RevertCombineItems($visualitem, $statsitem)
    {
        $this->AddDebugLog('RevertCombineItems');
        $this->AddDebugLog(' - Visual: ' . $visualitem->GetName() . ' (' . $visualitem->GetID() . ')');
        $this->AddDebugLog(' - Stats: ' . $statsitem->GetName() . ' (' . $statsitem->GetID() . ')');
        $this->AddDebugLog(' ');
        $itemManager = new itemManager($this->database);
        $vitem = $itemManager->GetItem($visualitem->GetVisualID());
        $this->AddItems($vitem, $vitem, 1);
        $this->inventory->RevertCombineItems($visualitem, $statsitem);
    }

    public function AddItems($statsitem, $visualitem, $amount, $statstype = 0, $upgrade = 0)
    {
        $itemManager = new ItemManager($this->database);
        if(is_numeric($statsitem))
            $statsitem = $itemManager->GetItem($statsitem);
        if(is_numeric($visualitem))
            $visualitem = $itemManager->GetItem($visualitem);

        $this->AddDebugLog('AddItems');
        $this->AddDebugLog(' - Stats: ' . $statsitem->GetName() . ' (' . $statsitem->GetID() . ')');
        $this->AddDebugLog(' - Visual: ' . $visualitem->GetName() . ' (' . $visualitem->GetID() . ')');
        $this->AddDebugLog(' - Amount: ' . number_format($amount, 0, '', '.'));
        $this->AddDebugLog(' - Statstype: ' . $statstype);
        $this->AddDebugLog(' - Upgrade: ' . $upgrade);
        $this->AddDebugLog(' ');

        $inventoryItem = $this->GetInventory()->AddItem($statsitem, $visualitem, $amount, $statstype, $upgrade);
        return $inventoryItem;
    }

    public function RemoveItemsByID($statsid, $itemsid, $statstype, $upgrade, $amount)
    {
        $item = $this->inventory->GetItemByStatsIDOnly($statsid, $itemsid, $statstype, $upgrade);
        $this->inventory->RemoveItem($item, $amount);
    }

    public function RemoveItems($item, $amount)
    {
        $this->inventory->RemoveItem($item, $amount);
    }

    public function SetDailyArenaPoints($value)
    {
        $this->database->Update('dailyarenapoints="'.$value.'"', 'accounts', 'id="'.$this->GetID().'"');
    }

    public function GetDailyCollectedKoloPoints()
    {
        return $this->data['collectedkolopoints'];
    }

    public function SetDailyCollectedKoloPoints($value)
    {
        $this->data['collectedkolopoints'] = $value;
    }

    public function BuyItemFrom($statsitem, $visualitem, $statstype, $upgrade, $amount, $price, $sellerid, $owners = '')
    {
        $this->BuyItem($statsitem, $visualitem, $statstype, $upgrade, $amount, $price, 0, $owners);
        $seller = new Player($this->database, $sellerid);
        if($seller->IsValid()) {
            if ($statsitem->IsPremium() == 0) {
                $calcprice = $seller->GetBerry() + ($price - floor((($price * 10) / 100)));
                $seller->SetBerry($calcprice);
                $this->database->Update('zeni='.$calcprice, 'accounts', 'id = ' . $sellerid, 1);
            }
            else {
                $calcprice = $seller->GetGold() + ($price - floor((($price * 10) / 100)));
                $seller->SetGold($calcprice);
                $this->database->Update('gold='.$calcprice, 'accounts', 'id = ' . $sellerid, 1);
            }
            $seller->AddDebugLog(date("H:i:s", time()) . " - Marktverkauf - Gegenstand: ".$statsitem->GetName()." - Anzahl: ".$amount." - Einzelpreis: ".($price/$amount)." - Gesamtpreis: ".$price." - Steuern: ".floor((($price * 10) / 100))." - Käufer: ".$this->GetName()." - KäuferID: ".$this->GetID());
        }
    }

    public function BuyItem($statsitem, $visualitem, $statstype, $upgrade, $amount, $price, $arenaprice = 0, $owners = '')
    {
        $this->inventory->AddItem($statsitem, $visualitem, $amount, $statstype, $upgrade, $owners);

        $berry = 0;
        $gold = 0;
        if ($statsitem->IsPremium())
        {
            $gold = $this->GetGold() - $price;
            $this->SetGold($gold);
            $berry = $this->GetBerry();
        }
        else
        {
            $berry = $this->GetBerry() - $price;
            $this->SetBerry($berry);
            $gold = $this->GetGold();
        }
        $arenapoints = $this->GetArenaPoints() - $arenaprice;
        $this->SetArenaPoints($arenapoints);

        $this->AddDebugLog('Buy Item');
        $this->AddDebugLog(' - Stats: ' . $statsitem->GetName() . ' (' . $statsitem->GetID() . ')');
        $this->AddDebugLog(' - Visual: ' . $visualitem->GetName() . ' (' . $visualitem->GetID() . ')');
        $this->AddDebugLog(' - Amount: ' . number_format($amount, '0', '', '.'));
        $this->AddDebugLog(' - Statstype: ' . $statstype);
        $this->AddDebugLog(' - Upgrade: ' . $upgrade);
        $this->AddDebugLog(' - Is Premium: ' . $statsitem->IsPremium());
        $this->AddDebugLog(' - Price Berry/Gold: ' . number_format($price, '0', '', '.'));
        $this->AddDebugLog(' - Price Arenapoints: ' . number_format($arenaprice, '0', '', '.'));
        $this->AddDebugLog(' - New Arenapoints: ' . number_format($arenaprice, '0', '', '.'));
        $this->AddDebugLog(' - New Berry: ' . number_format($berry, '0', '', '.'));
        $this->AddDebugLog(' - New Gold: ' . number_format($gold, '0', '', '.'));
        $this->AddDebugLog(' ');
        $this->database->Update('zeni="' . $berry . '", gold="' . $gold . '", arenapoints="' . $arenapoints . '"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function BuyEventItem($statsitem, $visualitem, $statstype, $upgrade, $amount, $price, $arenaprice = 0, $owners = '')
    {
        $this->inventory->AddItem($statsitem, $visualitem, $amount, $statstype, $upgrade, $owners);

        $berry = 0;
        $gold = 0;
        if ($statsitem->IsPremium())
        {
            $gold = $this->GetGold() - $price;
            $this->SetGold($gold);
            $berry = $this->GetBerry();
        }
        else
        {
            $muenzen = $this->GetMünzen() - $price;
            $this->SetMünzen($muenzen);
        }
        $arenapoints = $this->GetArenaPoints() - $arenaprice;
        $this->SetArenaPoints($arenapoints);

        $this->AddDebugLog('Buy Item');
        $this->AddDebugLog(' - Stats: ' . $statsitem->GetName() . ' (' . $statsitem->GetID() . ')');
        $this->AddDebugLog(' - Visual: ' . $visualitem->GetName() . ' (' . $visualitem->GetID() . ')');
        $this->AddDebugLog(' - Amount: ' . number_format($amount, '0', '', '.'));
        $this->AddDebugLog(' - Statstype: ' . $statstype);
        $this->AddDebugLog(' - Upgrade: ' . $upgrade);
        $this->AddDebugLog(' - Is Premium: ' . $statsitem->IsPremium());
        $this->AddDebugLog(' - Price Berry/Gold: ' . number_format($price, '0', '', '.'));
        $this->AddDebugLog(' - Price Arenapoints: ' . number_format($arenaprice, '0', '', '.'));
        $this->AddDebugLog(' - New Arenapoints: ' . number_format($arenaprice, '0', '', '.'));
        $this->AddDebugLog(' - New Berry: ' . number_format($berry, '0', '', '.'));
        $this->AddDebugLog(' - New Gold: ' . number_format($gold, '0', '', '.'));
        $this->AddDebugLog(' ');
        $this->database->Update('münzen="' . $muenzen . '"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function SellItem($item, $amount = 1)
    {
        $gold = 0;
        $berry = 0;
        if ($item->IsPremium())
        {
            $gold = (floor($item->GetPrice() / 2) * $amount);
            $this->SetGold(($gold + $this->GetGold()));
            $this->database->Update('gold=' . $this->GetGold(), 'accounts', 'id = ' . $this->data['id'], 1);
        }
        else
        {
            $berry = (round($item->GetPrice() * 0.5) * $amount);
            $this->SetBerry(($berry + $this->GetBerry()));
            $this->database->Update('zeni=' . intval($this->GetBerry()), 'accounts', 'id = ' . $this->data['id'], 1);
        }
        $this->AddDebugLog(date("H:i:s")." - Itemverkauf: ".$item->GetName()." - Anzahl: ".$amount." - Preis: " . ($berry + $gold) . " - Premium: " . $item->IsPremium());
        $this->inventory->RemoveItem($item, $amount);
    }

    public function GetIP()
    {
        $client = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote = $_SERVER['REMOTE_ADDR'];

        if (filter_var($client, FILTER_VALIDATE_IP))
        {
            $ip = $client;
        }
        elseif (filter_var($forward, FILTER_VALIDATE_IP))
        {
            $ip = $forward;
        }
        else
        {
            $ip = $remote;
        }
        $ip = hash('sha256', 'itsa' . $ip . 'ip');

        return $ip;
    }

    public function SendClanApplication($clan, $text)
    {
        $text = $this->database->EscapeString($text);
        $this->SetClanApplication($clan->GetID());
        $this->SetClanApplicationText($text);
        $this->database->Update('clanapplication="' . $clan->GetID() . '", clanapplicationtext="' . $text . '"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function JoinClan($clan)
    {
        $image = 'img/system.png';
        if($clan->GetImage() != '')
            $image = $clan->GetImage();
        $PMManager = new PMManager($this->database, $this->GetID());
        $list = new Generallist($this->database, 'accounts', 'name, clan', 'clan="' . $clan->GetID() . '"', '', 999999, 'ASC');
        $i = 0;
        $entry = $list->GetEntry($i);
        while ($entry != null)
        {
            $PMManager->SendPM(0, $image, $clan->GetName(), 'Ein neues Mitglied ist der Bande beigetreten', '<div style="text-align:center;">Der Spieler <a href="?p=profil&id='.$this->GetID().'">'.$this->GetName().'</a> ist der Bande beigetreten.</div>', $entry['name'], 1);
            ++$i;
            $entry = $list->GetEntry($i);
        }
        $this->SetClanApplication(0);
        $this->SetClanApplicationText('');
        $this->SetClan($clan->GetID());
        $this->SetClanName($clan->GetName());
        $this->database->Update('clanrank=2, clanapplication="0", clanapplicationtext="", clan="' . $clan->GetID() . '", clanname="' . $clan->GetName() . '", clansince=NOW()', 'accounts', 'id = ' . $this->data['id'], 1);
        $PMManager->SendPM(0, $image, $clan->GetName(), 'Clanaufnahme', '<div style="text-align:center;">Deine Beitrittsanfrage an die Bande <a href="?p=clan&id='.$clan->GetID().'">'.$clan->GetName().'</a> wurde angenommen.</div>', $this->GetName(), 1);
    }

    public function GetLeaveClan()
    {
        return $this->data['leaveclan'];
    }

    public function LeaveClan()
    {
        if($this->GetLeaveClan() == 0) $this->data['leaveclan'] = 1;
        else $this->data['leaveclan'] = 0;
        $this->database->Update('leaveclan="' . $this->data['leaveclan'] . '"', 'accounts', 'id = "' . $this->data['id'] . '"', 1);
    }

    public function DeleteClanApplication()
    {
        $this->SetClanApplication(0);
        $this->SetClanApplicationText('');
        $this->database->Update('clanapplication="0",clanapplicationtext=""', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function CheckSkill($skill)
    {
        $type = $skill->GetType();
        $path = $this->GetPfad(1);
        $path2 = $this->GetPfad(2);
        $attacks = explode(";", $this->GetAttacks());
        // Others
        //if($path != "Zoan" && $path != "Paramecia" && $path != "Logia")
        //	return true;
        // Zoan
        if ($type == 1 && ($path == "Zoan" || $path == "None"))
            return true;
        // Paramecia
        else if ($type == 2 && ($path == "Paramecia" || $path == "None"))
            return true;
        // Logia
        else if ($type == 3 && ($path == "Logia" || $path == "None"))
            return true;
        // Schwertkämpfer
        else if ($type == 4 && ($path2 == "Schwertkaempfer" || $path2 == "None"))
            return true;
        // Schwarzfuß
        else if ($type == 5 && ($path2 == "Schwarzfuss" || $path2 == "None"))
            return true;
        // Schütze
        else if ($type == 6 && ($path2 == "Karatekämpfer" || $path2 == "None"))
            return true;
        else
            return false;
    }

    public function GetPfad($path)
    {
        switch ($path)
        {
            case 1:
                return $this->data['pfad'];
            case 2:
                return $this->data['pfad2'];
        }
    }

    public function CountName($name)
    {
        $result = $this->database->Select('name', 'accounts', 'name="'.$name.'"');
        $num = $result->num_rows;
        return $num;
    }

    public function AcceptNPCWonItems()
    {
        $this->SetNPCWonItems('');
        $this->SetNPCWonItemsType(-1);
        $this->SetNPCWonItemsDungeon(0);
        $this->database->Update('npcwonitems="",npcwonitemtype="-1",npcwonitemdungeon="0"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function UnequipItem($item)
    {
        $equippedStats = $this->GetEquippedStats();
        $travelBonus = $this->GetTravelBonusInternal();
        $trainBonus = $this->GetTrainBonus();

        if ($item->GetType() == 3)
        {
            $equippedStats = $this->RemoveEquippedStats($item);
        }
        /*else if($item->GetType() == 4)
		{
			$travelBonus = $travelBonus - $item->GetTravelBonus();
			$this->SetTravelBonus($travelBonus);
		}*/

        $trainBonus = $trainBonus - $item->GetTrainBonus();
        $this->SetTrainBonus($trainBonus);

        $this->inventory->UnequipItem($item);
        $this->database->Update('equippedstats="' . $equippedStats . '", travelbonus="' . $travelBonus . '", trainingstats="' . $trainBonus . '"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function AddEquippedStats($item)
    {
        $equippedStats = explode(';', $this->GetEquippedStats());
        if ($item->GetLP() != 0)
        {
            $equippedStats[0] += $item->GetLP();
        }
        if ($item->GetKP() != 0)
        {
            $equippedStats[1] += $item->GetKP();
        }
        if ($item->GetAttack() != 0)
        {
            $equippedStats[2] += $item->GetAttack();
        }
        if ($item->GetDefense() != 0)
        {
            $equippedStats[3] += $item->GetDefense();
        }

        $newEquippedStats = implode(';', $equippedStats);
        $this->SetEquippedStats($newEquippedStats);
        return $newEquippedStats;
    }

    public function RemoveEquippedStats($item)
    {
        $equippedStats = explode(';', $this->GetEquippedStats());
        if ($item->GetLP() != 0)
        {
            $equippedStats[0] -= $item->GetLP();
        }
        if ($item->GetKP() != 0)
        {
            $equippedStats[1] -= $item->GetKP();
        }
        if ($item->GetAttack() != 0)
        {
            $equippedStats[2] -= $item->GetAttack();
        }
        if ($item->GetDefense() != 0)
        {
            $equippedStats[3] -= $item->GetDefense();
        }

        $newEquippedStats = implode(';', $equippedStats);
        $this->SetEquippedStats($newEquippedStats);
        return $newEquippedStats;
    }

    public function EquipItem($item)
    {
        if ($item->GetType() == 3 || $item->GetSlot() == 4) {
            $slotItem = $this->inventory->GetItemAtSlot($item->GetSlot());

            $equippedStats = $this->GetEquippedStats();
            $travelBonus = $this->GetTravelBonusInternal();
            $trainBonus = $this->GetTrainBonus();

            if ($slotItem != null) {
                $this->inventory->UnequipItem($slotItem);
                if ($slotItem->GetType() == 3) {
                    $equippedStats = $this->RemoveEquippedStats($slotItem);
                }
                /*else if($slotItem->GetType() == 4)
                {
                    $travelBonus = $travelBonus - $slotItem->GetTravelBonus();
                    $this->SetTravelBonus($travelBonus);
                }*/
                $trainBonus = $trainBonus - $slotItem->GetTrainBonus();
                $this->SetTravelBonus($trainBonus);
            }

            $this->inventory->EquipItem($item);
            if ($item->GetType() == 3) {
                $equippedStats = $this->AddEquippedStats($item);
            }
            /*else if($item->GetType() == 4)
            {
                    $travelBonus = $travelBonus + $item->GetTravelBonus();
                    $this->SetTravelBonus($travelBonus);
            }*/

            $trainBonus = $trainBonus + $item->GetTrainBonus();
            $this->SetTrainBonus($trainBonus);

            $this->database->Update('equippedstats="' . $equippedStats . '", travelbonus="' . $travelBonus . '", trainingstats="' . $trainBonus . '"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
        }
    }

    public function DeleteAccount()
    {
        $result = $this->database->Select('id,friends,friendrequests', 'accounts', '');
        if ($result)
        {
            while ($row = $result->fetch_assoc())
            {
                $fl = explode(";", $row['friends']);
                if (in_array($this->GetID(), $fl))
                {
                    if (($key = array_search($this->GetID(), $fl)) !== false)
                    {
                        unset($fl[$key]);
                        $fl = implode(";", $fl);
                        $this->database->Update('friends="' . $fl . '"', 'accounts', 'id=' . $row['id']);
                    }
                }
                $fr = explode(";", $row['friendrequests']);
                if (in_array($this->GetID(), $fr))
                {
                    if (($key = array_search($this->GetID(), $fr)) !== false)
                    {
                        unset($fr[$key]);
                        $fr = implode(";", $fr);
                        $this->database->Update('friendrequests="' . $fr . '"', 'accounts', 'id=' . $row['id']);
                    }
                }
            }
        }
        $otherChars = $this->database->Select('*', 'accounts', 'userid='.$this->GetUserID().' AND id != '.$this->GetID().' AND ismulti = 1');
        if($otherChars && $otherChars->num_rows > 0)
        {
            $userIDs = array();
            $finalID = 0;
            while($row = $otherChars->fetch_assoc())
            {
                $userIDs[] = $row['id'];
            }
            foreach($userIDs as $userID)
            {
                if($finalID > $userID || $finalID == 0)
                    $finalID = $userID;
            }
            if($finalID != 0)
            {
                $this->database->Update('ismulti=0', 'accounts', 'id='.$finalID);
            }
        }
        $text = 'Charakterlöschung - Name: <a href="?p=profil&id='.$this->data["id"].'">'.$this->data["name"].'</a> ID: '.$this->data["id"].' AccountID: '.$this->data["userid"].' Grund: ' .$this->data['grund'];
        $text = $this->database->EscapeString($text);
        $receiver = $this->data['id'];
        $sender = "System";
        $type = 2;
        $this->database->Insert('text, receiver, sender, status, type', '"' . $text . '", "' . $receiver . '", "' . $sender . '", 0, ' . $type, 'meldungen');
        for($i = 0; $i <= 10; ++$i)
        {
            $name = $this->GetName();
            if($name == $name)
            {
                $name = $this->GetName()."".$i;
                if($this->CountName($name) == 1)
                {
                    $b = $i + 1;
                    $name = $this->GetName()."".$b;
                    $this->SetNewName($name);
                }
                else
                {
                    $name = $this->GetName()."0";
                    $this->SetNewName($name);
                }
            }
        }
        $titels = $this->GetTitels();
        array_splice($titels, array_search(76 , $titels), 1);
        $this->database->Update('old_userid=userid, deleted=1, userid=2964, session="", friends="", friendrequests="", titels="'.implode(";", $titels).'", sitter=0', 'accounts', 'id='.$this->GetID());
        $this->database->Delete('market', 'sellerid = ' . $this->GetID(), 999999999);
        $this->database->Delete('arenafighter', 'fighter = ' . $this->GetID(), 1);
        $this->database->Update('igentry=1', 'statslist', 'acc='.$this->GetID());
    }

    public function AddMeldung($text, $receiver, $sender, $type)
    {
        $text = $this->database->EscapeString($text);
        $receiver = $this->database->EscapeString($receiver);
        $sender = $this->database->EscapeString($sender);
        $type = $this->database->EscapeString($type);
        $this->database->Insert('text, receiver, sender, status, type', '"' . $text . '", "' . $receiver . '", "' . $sender . '", 0, ' . $type, 'meldungen');
    }

    public function UpgradeRüstungshaki($price)
    {
        $rhakiID = 11;
        $itemManager = new itemManager($this->database);
        $item = $this->GetItemByIDOnly($rhakiID, $rhakiID);
        if ($item == null)
        {
            $rhakiItem = $itemManager->GetItem($rhakiID);
            $this->AddItems($rhakiItem, $rhakiItem, 1, $rhakiItem->GetDefaultStatsType(), 0);
            $this->UpgradeRüstungshaki($price);
            return;
        }

        $rhakiLevel = $item->GetCalculateUpgrade();
        if ($rhakiLevel >= $this->GetLevel())
            return;

        $isEquipped = $item->isEquipped();
        if ($isEquipped)
            $this->UnequipItem($item);

        $newLevel = $item->GetUpgrade() + 1;
        $item->SetUpgrade($newLevel);
        $this->SetBerry($this->GetBerry() - $price);
        $this->database->Update('upgrade="' . $newLevel . '"', 'inventory', 'id = ' . $item->GetID() . '', 1);
        $this->database->Update('zeni="' . $this->GetBerry() . '"', 'accounts', 'id = ' . $this->GetID() . '', 1);

        if ($isEquipped)
            $this->EquipItem($item);
    }

    public function GetCapchaCount()
    {
        return $this->data['capchacount'];
    }

    public function SetCaptchaCount($value)
    {
        $this->data['capchacount'] = $value;
    }

    public function GetKoloCode()
    {
        return $this->data['kolocode'];
    }

    public function SetKoloCode($value)
    {
        $this->data['kolocode'] = $value;
    }

    public function IsKoloPlayer()
    {
        $kolocheck = $this->database->Select('*', 'arenafighter', 'fighter="'.$this->data['id'].'"');
        $koloplayer = $kolocheck->num_rows;
        if($koloplayer == 1)
        {
            return true;
        }
    }

    public function UpgradeAura($aura, $price)
    {
        $itemManager = new itemManager($this->database);
        $item = $this->GetItemByStatsIDOnly($aura);
        if ($item == null)
        {
            $auraItem = $itemManager->GetItem($aura);
            $this->AddItems($auraItem, $auraItem, 1, $auraItem->GetDefaultStatsType(), 0);
            $this->UpgradeAura($aura, $price);
            return;
        }

        $auraLevel = $item->GetCalculateUpgrade();
        if ($auraLevel == $this->GetLevel())
            return;

        $isEquipped = $item->isEquipped();
        if ($isEquipped)
            $this->UnequipItem($item);




        $newLevel = $item->GetUpgrade() + 1;
        $item->SetUpgrade($newLevel);
        $this->SetBerry($this->GetBerry() - $price);
        $this->database->Update('upgrade="' . $newLevel . '"', 'inventory', 'id = ' . $item->GetID() . '', 1);
        $this->database->Update('zeni="' . $this->GetBerry() . '"', 'accounts', 'id = ' . $this->GetID() . '', 1);

        if ($isEquipped)
            $this->EquipItem($item);
    }

    public function UseItem($id, $item, $amount = 1)
    {
        $inventoryItem = $this->inventory->GetItem($id);
        $newAmount = $inventoryItem->GetAmount();
        $newAmount = $newAmount - $amount;
        if ($newAmount == 0)
        {
            $this->inventory->RemoveItem($inventoryItem);
        }
        else
        {
            $inventoryItem->SetAmount($newAmount);
        }

        $type = $item->GetType();
        $lpHeal = 0;
        $kpHeal = 0;

        $healPercentage = 1;
        if ($type == 1)
        {
            $lpHeal = $item->GetLP() * $amount * $healPercentage;
            $kpHeal = $item->GetKP() * $amount * $healPercentage;
        }
        else if ($type == 2)
        {
            $lpHeal = (($item->GetLP() * $amount * $healPercentage) / 100) * $this->GetMaxLP();
            $kpHeal = (($item->GetKP() * $amount * $healPercentage) / 100) * $this->GetMaxKP();
        }

        $newLP = $this->GetLP() + floor($lpHeal);
        $newKP = $this->GetKP() + floor($kpHeal);

        if ($newLP > $this->GetMaxLP())
        {
            $newLP = $this->GetMaxLP();
        }

        if ($newKP > $this->GetMaxKP())
        {
            $newKP = $this->GetMaxKP();
        }

        $this->SetLP($newLP);
        $this->SetKP($newKP);

        $items = $this->inventory->Encode();
        $this->database->Update('inventory="' . $items . '",lp="' . $newLP . '",kp="' . $newKP . '"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function FactionChange($faction)
    {
        $attacks = explode(";", $this->GetAttacks());
        $fightattacks = explode(";", $this->GetFightAttacks());
        if($faction == 'Pirat')
        {
            // Regular Attacks
            if(in_array(229, $attacks))
            {
                $attacks[array_search(229,$attacks)] = 227;
            }
            if(in_array(230, $attacks))
            {
                $attacks[array_search(230,$attacks)] = 228;
            }
            if(in_array(98, $attacks))
            {
                $attacks[array_search(98,$attacks)] = 99;
            }

            //Fight Attacks
            if(in_array(229, $fightattacks))
            {
                $fightattacks[array_search(229,$fightattacks)] = 227;
            }
            if(in_array(230, $fightattacks))
            {
                $fightattacks[array_search(230,$fightattacks)] = 228;
            }
            if(in_array(98, $fightattacks))
            {
                $fightattacks[array_search(98,$fightattacks)] = 99;
            }
        }
        else if($faction == 'Marine')
        {
            // Regular Attacks
            if(in_array(227, $attacks))
            {
                $attacks[array_search(227,$attacks)] = 229;
            }
            if(in_array(228, $attacks))
            {
                $attacks[array_search(228,$attacks)] = 230;
            }
            if(in_array(99, $attacks))
            {
                $attacks[array_search(99,$attacks)] = 98;
            }

            // Fight Attacks
            if(in_array(227, $fightattacks))
            {
                $fightattacks[array_search(227,$fightattacks)] = 229;
            }
            if(in_array(228, $fightattacks))
            {
                $fightattacks[array_search(228,$fightattacks)] = 230;
            }
            if(in_array(99, $fightattacks))
            {
                $fightattacks[array_search(99,$fightattacks)] = 98;
            }
        }
        $attacks = implode(";", $attacks);
        $fightattacks = implode(";", $fightattacks);
        $this->SetAttacks($attacks);
        $this->SetFightAttacks($fightattacks);
        $this->database->Update('race="'.$faction.'", attacks="'.$attacks.'", fightattacks="'.$fightattacks.'"', 'accounts', 'id='.$this->GetID());
    }

    public function DoSkillReset($id, $pfad)
    {
        $item = $this->inventory->GetItem($id);
        if ($item->GetStatsID() == 30)
        {
            $this->ResetSkills($pfad);
            $this->inventory->RemoveItem($item, 1);
        }
    }

    public function Track($text, $receiver, $sender, $type)
    {
        $text = $this->database->EscapeString($text);
        $receiver = $this->database->EscapeString($receiver);
        $sender = $this->database->EscapeString($sender);
        $this->database->Insert('text, receiver, sender, status, type', '"' . $text . '", "' . $receiver . '", "' . $sender . '", 0, ' . $type, 'meldungen');
    }

    public function UseItem2($id, $amount = 1, $onItem = null)
    {
        $item = $this->inventory->GetItem($id);

        $type = $item->GetType();
        $lpHeal = 0;
        $kpHeal = 0;

        $healPercentage = 1;
        if ($type == 1)
        {
            $lpHeal = $item->GetLP() * $amount * $healPercentage;
            $kpHeal = $item->GetKP() * $amount * $healPercentage;
        }
        else if ($type == 2)
        {
            $lpHeal = (($item->GetLP() * $amount * $healPercentage) / 100) * $this->GetMaxLP();
            $kpHeal = (($item->GetKP() * $amount * $healPercentage) / 100) * $this->GetMaxKP();
        }
        else if($type == 5)
        {
            if($item->GetStatsID() == 302)
            {
                if($this->GetTravelTicket() == 0)
                {
                    $this->UseTravelTicket(1);
                }
            }
            if($item->GetStatsID() == 350)
            {
                $rabatt = $this->GetRabatt() + (25 * $amount);
                if($rabatt <= 100) {
                    $this->SetRabatt($rabatt);
                }
            }
            if($item->GetStatsID() == 351)
            {
                $rabatt = $this->GetRabatt() + (50 * $amount);
                if($rabatt <= 100) {
                    $this->SetRabatt($rabatt);
                }
            }
            if($item->GetStatsID() == 352)
            {
                $rabatt = $this->GetRabatt() + (75 * $amount);
                if($rabatt <= 100) {
                    $this->SetRabatt($rabatt);
                }
            }
            if($item->GetStatsID() == 353)
            {
                $rabatt = $this->GetRabatt() + (100 * $amount);
                if($rabatt <= 100) {
                    $this->SetRabatt($rabatt);
                }
            }
        }
        else if ($type == 6)
        {
            if ($item->GetStatsID() == 9) //StatsReset
            {
                $this->ResetStats();
            }
            if ($item->GetStatsID() == 405) //Halloween StatsReset
            {
                $this->ResetStats();
            }
            if($item->GetStatsID() == 10)
            {
                if(!$this->HasTeleschnecke())
                {
                    $items = $this->data['useritems'];
                    $items = explode(";", $items);
                    $items[] = $item->GetStatsID();
                    $items = implode(";", $items);
                    $this->data['useritems'] = $items;
                    $this->database->Update('useritems="'.$items.'"', 'accounts', 'id='.$this->GetID());
                }
            }
            if($item->GetStatsID() == 36)
            {
                if(!$this->HasEastBlueMap())
                {
                    $items = $this->data['useritems'];
                    $items = explode(";", $items);
                    $items[] = $item->GetStatsID();
                    $items = implode(";", $items);
                    $this->data['useritems'] = $items;
                    $this->database->Update('useritems="'.$items.'"', 'accounts', 'id='.$this->GetID());
                }
            }

            if($item->GetStatsID() == 37)
            {
                if(!$this->HasNorthBlueMap())
                {
                    $items = $this->data['useritems'];
                    $items = explode(";", $items);
                    $items[] = $item->GetStatsID();
                    $items = implode(";", $items);
                    $this->data['useritems'] = $items;
                    $this->database->Update('useritems="'.$items.'"', 'accounts', 'id='.$this->GetID());
                }
            }
            if($item->GetStatsID() == 38)
            {
                if(!$this->HasSouthBlueMap())
                {
                    $items = $this->data['useritems'];
                    $items = explode(";", $items);
                    $items[] = $item->GetStatsID();
                    $items = implode(";", $items);
                    $this->data['useritems'] = $items;
                    $this->database->Update('useritems="'.$items.'"', 'accounts', 'id='.$this->GetID());
                }
            }
            if($item->GetStatsID() == 39)
            {
                if(!$this->HasWestBlueMap())
                {
                    $items = $this->data['useritems'];
                    $items = explode(";", $items);
                    $items[] = $item->GetStatsID();
                    $items = implode(";", $items);
                    $this->data['useritems'] = $items;
                    $this->database->Update('useritems="'.$items.'"', 'accounts', 'id='.$this->GetID());
                }
            }
            if($item->GetStatsID() == 72)
            {
                if(!$this->HasImpeldownMap())
                {
                    $items = $this->data['useritems'];
                    $items = explode(";", $items);
                    $items[] = $item->GetStatsID();
                    $items = implode(";", $items);
                    $this->data['useritems'] = $items;
                    $this->database->Update('useritems="'.$items.'"', 'accounts', 'id='.$this->GetID());
                }
            }
            if($item->GetStatsID() == 85)
            {
                if(!$this->HasLogPort())
                {
                    $items = $this->data['useritems'];
                    $items = explode(";", $items);
                    $items[] = $item->GetStatsID();
                    $items = implode(";", $items);
                    $this->data['useritems'] = $items;
                    $this->database->Update('useritems="'.$items.'"', 'accounts', 'id='.$this->GetID());
                }
            }
            else if ($item->GetStatsID() == 71) // Schmiede W.
            {
                $onItem = $this->inventory->GetItemByDatabaseID($onItem);
                if ($onItem != null && $onItem->CanUpgrade())
                {
                    $upgrade = $onItem->GetUpgrade() + $amount;
                    if ($upgrade > $onItem->GetMaxUpgrade())
                    {
                        return "Dieses Item kann nicht weiter verbessert werden.";
                    }
                    $this->database->Update('upgrade="' . $upgrade . '"', 'inventory', 'id = ' . $onItem->GetID() . '', 1);
                }
            }
			else if ($item->GetStatsID() == 181) // Haloween Schmiede W.
			{
				$onItem = $this->inventory->GetItemByDatabaseID($onItem);
				if ($onItem != null && $onItem->CanUpgrade())
				{
					$upgrade = $onItem->GetUpgrade() + $amount;
					if ($upgrade > $onItem->GetMaxUpgrade())
					{
						return "Dieses Item kann nicht weiter verbessert werden.";
					}
					$result = $this->database->Update('upgrade="' . $upgrade . '"', 'inventory', 'id = ' . $onItem->GetID() . '', 1);
				}
			}
            else if ($item->GetStatsID() == 174) // Rüstungskristall
            {
                $onItem = $this->inventory->GetItemByDatabaseID($onItem);
                if ($onItem != null && $onItem->CanUpgrade())
                {
                    $canUpgrade = true;
                    $rand = rand(1,100);
                    if($rand < 25 || $rand > 75) {
                        $returnmessage = 'Das Upgrade ist schiefgegangen, der Rüstungskristall ist zerbrochen.';
                        $canUpgrade = false;
                    }
                    if($canUpgrade) {
                        $upgrade = $onItem->GetUpgrade() + $amount;
                        if ($upgrade < $onItem->GetMaxUpgrade()) {
                            return "Das Item muss mindestens auf Level 5 sein um dieses Item darauf anzuwenden.";
                        } else if ($upgrade > $onItem->GetMaxUpgrade() + 1) {
                            return "Höher als Level 6 kannst du dieses Item nicht upgraden!";
                        }
                        $this->database->Update('upgrade="' . $upgrade . '"', 'inventory', 'id = ' . $onItem->GetID() . '', 1);
                    }
                }
            }
            else if ($item->GetStatsID() == 180) // Halloween Rüstungskristall
			{
				$onItem = $this->inventory->GetItemByDatabaseID($onItem);
				if ($onItem != null && $onItem->CanUpgrade())
				{

					$upgrade = $onItem->GetUpgrade() + $amount;
					$upgradecheck = $upgrade + $onItem->GetUpgrade();
					if ($upgrade < $onItem->GetMaxUpgrade())
					{
						return "Das Item muss mindestens auf Level 5 sein um dieses Item darauf anzuwenden.";
					}
					else if ($upgrade > $onItem->GetMaxUpgrade() + 1)
					{
						return "Höher als Level 6 kannst du dieses Item nicht Upgraden!";
					}
					$result = $this->database->Update('upgrade="' . $upgrade . '"', 'inventory', 'id = ' . $onItem->GetID() . '', 1);
				}
			}
            else if ($item->GetStatsID() == 81) // Testo Booster
            {
                $fightID = $this->GetFight();
                $playerAD = 0;
                $playerMAD = 0;
                $fighterID = 0;
                $result = $this->database->Select('id,kp,ikp', 'fighters', 'acc=' . $this->data['id'] . ' AND fight=' . $fightID, 1);
                if ($result)
                {
                    $row = $result->fetch_assoc();
                    $playerAD = $row['kp'];
                    $playerMAD = $row['ikp'];
                    $fighterID = $row['id'];
                }
                $AD = $playerAD + (ceil(($playerMAD * 0.10)) * $amount);
                if ($AD > $playerMAD)
                {
                    $AD = $playerMAD;
                }
                if ($this->GetFight() > 0)
                {
                    $fight = new Fight($this->database, $this->GetFight(), $this, $this->actionManager);
                    $fighter = $fight->GetFighter($fighterID);
                    $fighter->SetKP($AD);
                    $this->database->Update('kp="' . $AD . '"', 'fighters', 'acc=' . $this->data['id'] . ' AND fight=' . $fightID, 1);
                    $result = $this->database->Select('id, text', 'fights', 'id="' . $fightID . '"', 1);
                    $fightText = '';
                    if ($result)
                    {
                        $rowtext = $result->fetch_assoc();
                        $fightText = $rowtext['text'];
                    }
                    $textamount = "einen";
                    if ($amount > 1) $textamount = number_format($amount, '0', '', '.');

                    $itemtext = '<tr><td align=center colspan=4><h2>Item Einsatz</h2></td></tr>'; //Das fügt "Item Einsatz" hinzu
                    $itemtext = $itemtext . '<tr><td align=center colspan=3>' . $this->data['name'] . ' trinkt ' . $textamount . ' Testo Booster, ' . $this->data['name'] . ' scheint sich ein wenig zu erholen.</td></tr>' . $fightText; //Das ist der normale Text


                    $this->database->Update('text="' . $this->database->EscapeString($itemtext) . '"', 'fights', 'id=' . $fightID, 1);
                }
            }
            else if ($item->GetStatsID() == 82) // Vitamine
            {
                $fightID = $this->GetFight();
                $playerEnergy = 0;
                $fighterID = 0;
                $result = $this->database->Select('id,energy', 'fighters', 'acc=' . $this->data['id'] . ' AND fight=' . $fightID, 1);
                if ($result)
                {
                    $row = $result->fetch_assoc();
                    $playerEnergy = $row['energy'];
                    $fighterID = $row['id'];
                }
                $EP = $playerEnergy - 25 * $amount;
                if($EP < 0)
                    $EP = 0;
                if ($this->GetFight() > 0)
                {
                    $fight = new Fight($this->database, $this->GetFight(), $this, $this->actionManager);
                    $fighter = $fight->GetFighter($fighterID);
                    $fighter->SetEnergy($EP);
                    $this->database->Update('energy="' . $EP . '"', 'fighters', 'acc=' . $this->data['id'] . ' AND fight=' . $fightID, 1);
                    $result = $this->database->Select('id, text', 'fights', 'id="' . $fightID . '"', 1);
                    $fightText = '';
                    if ($result)
                    {
                        $rowtext = $result->fetch_assoc();
                        $fightText = $rowtext['text'];
                    }
                    $textamount = "einmal";
                    if ($amount > 1) $textamount = number_format($amount, '0', '', '.');

                    $itemtext = '<tr><td align=center colspan=4><h2>Item Einsatz</h2></td></tr>'; //Das fügt "Item Einsatz" hinzu
                    $itemtext = $itemtext . '<tr><td align=center colspan=3>' . $this->data['name'] . ' nimmt ' . $textamount . ' Vitamine zu sich, ' . $this->data['name'] . ' scheint neue Energie getankt zu haben.</td></tr>' . $fightText; //Das ist der normale Text

                    $this->database->Update('text="' . $this->database->EscapeString($itemtext) . '"', 'fights', 'id=' . $fightID, 1);
                }
            }
            else if ($item->GetStatsID() == 86) // Seltene Rote Frucht
            {
                $fightID = $this->GetFight();
                $playerAttack = 0;
                $fighterID = 0;
                $result = $this->database->Select('id,attack', 'fighters', 'acc=' . $this->data['id'] . ' AND fight=' . $fightID, 1);
                if ($result)
                {
                    $row = $result->fetch_assoc();
                    $playerAttack = $row['attack'];
                    $fighterID = $row['id'];
                }
                $attackrechnung = round(10 * $playerAttack / 100  * $amount + $playerAttack);
                $Attack = $attackrechnung;
                if ($this->GetFight() > 0)
                {
                    $fight = new Fight($this->database, $this->GetFight(), $this, $this->actionManager);
                    $fighter = $fight->GetFighter($fighterID);
                    $fighter->SetAttack($Attack);
                    $this->database->Update('attack="' . $Attack . '"', 'fighters', 'acc=' . $this->data['id'] . ' AND fight=' . $fightID, 1);
                    $result = $this->database->Select('id, text', 'fights', 'id="' . $fightID . '"', 1);
                    $fightText = '';
                    if ($result)
                    {
                        $rowtext = $result->fetch_assoc();
                        $fightText = $rowtext['text'];
                    }
                    $textamount = "eine seltene rote Frucht";
                    if ($amount > 1) $textamount = number_format($amount, '0', '', '.') . " seltene rote Früchte";
                    $itemtext = '<tr><td align=center colspan=4><h2>Item Einsatz</h2></td></tr>'; //Das fügt "Item Einsatz" hinzu
                    $itemtext = $itemtext . '<tr><td align=center colspan=3>' . $this->data['name'] . ' isst ' . $textamount . ', die Muskeln von ' . $this->data['name'] . ' steigen stark an.</td></tr>' . $fightText; //Das ist der normale Text

                    $this->database->Update('text="' . $this->database->EscapeString($itemtext) . '"', 'fights', 'id=' . $fightID, 1);
                }
            }
            else if ($item->GetStatsID() == 87) // Seltene Orangene Frucht
            {
                $fightID = $this->GetFight();
                $playerDefense = 0;
                $fighterID = 0;
                $result = $this->database->Select('id,defense', 'fighters', 'acc=' . $this->data['id'] . ' AND fight=' . $fightID, 1);
                if ($result)
                {
                    $row = $result->fetch_assoc();
                    $playerDefense = $row['defense'];
                    $fighterID = $row['id'];
                }
                $Defenserechnung = round(10 * $playerDefense / 100 * $amount + $playerDefense);
                $Defense = $Defenserechnung;
                if ($this->GetFight() > 0)
                {
                    $fight = new Fight($this->database, $this->GetFight(), $this, $this->actionManager);
                    $fighter = $fight->GetFighter($fighterID);
                    $fighter->SetDefense($Defense);
                    $this->database->Update('defense="' . $Defense . '"', 'fighters', 'acc=' . $this->data['id'] . ' AND fight=' . $fightID, 1);
                    $result = $this->database->Select('id, text', 'fights', 'id="' . $fightID . '"', 1);
                    $fightText = '';
                    if ($result)
                    {
                        $rowtext = $result->fetch_assoc();
                        $fightText = $rowtext['text'];
                    }
                    $textamount = "eine seltene orangene Frucht";
                    if ($amount > 1) $textamount = number_format($amount, '0', '', '.') . " seltene orangene Früchte";
                    $itemtext = '<tr><td align=center colspan=4><h2>Item Einsatz</h2></td></tr>'; //Das fügt "Item Einsatz" hinzu
                    $itemtext = $itemtext . '<tr><td align=center colspan=3>' . $this->data['name'] . ' isst ' . $textamount . ', der Körper von ' . $this->data['name'] . ' scheint sich zu verhärten.</td></tr>' . $fightText; //Das ist der normale Text

                    $this->database->Update('text="' . $this->database->EscapeString($itemtext) . '"', 'fights', 'id=' . $fightID, 1);
                }
            }
            else if ($item->GetStatsID() == 88) // Seltene Gelbe Frucht
            {
                $fightID = $this->GetFight();
                $playerReflex = 0;
                $fighterID = 0;
                $result = $this->database->Select('id,reflex', 'fighters', 'acc=' . $this->data['id'] . ' AND fight=' . $fightID, 1);
                if ($result)
                {
                    $row = $result->fetch_assoc();
                    $playerReflex = $row['reflex'];
                    $fighterID = $row['id'];
                }
                $Reflex = $playerReflex + (50 * $amount);
                if ($this->GetFight() > 0)
                {
                    $fight = new Fight($this->database, $this->GetFight(), $this, $this->actionManager);
                    $fighter = $fight->GetFighter($fighterID);
                    $fighter->SetReflex($Reflex);
                    $this->database->Update('reflex="' . $Reflex . '"', 'fighters', 'acc=' . $this->data['id'] . ' AND fight=' . $fightID, 1);
                    $result = $this->database->Select('id, text', 'fights', 'id="' . $fightID . '"', 1);
                    $fightText = '';
                    if ($result)
                    {
                        $rowtext = $result->fetch_assoc();
                        $fightText = $rowtext['text'];
                    }
                    $textamount = "eine seltene gelbe Frucht";
                    if ($amount > 1) $textamount = number_format($amount, '0', '', '.') . " seltene gelbe Früchte";
                    $itemtext = '<tr><td align=center colspan=4><h2>Item Einsatz</h2></td></tr>'; //Das fügt "Item Einsatz" hinzu
                    $itemtext = $itemtext . '<tr><td align=center colspan=3>' . $this->data['name'] . ' isst ' . $textamount . ', der Körper von ' . $this->data['name'] . ' wird leichter und schneller.</td></tr>' . $fightText; //Das ist der normale Text

                    $this->database->Update('text="' . $this->database->EscapeString($itemtext) . '"', 'fights', 'id=' . $fightID, 1);
                    $this->AddDebugLog(' - - add Reflex to: ' . number_format($Reflex, '0', '', '.'));
                }
            }
            else if ($item->GetStatsID() == 104) // Stat Geist
            {
                if ($this->GetAssignedStats() >= 2000)
                {
                    $this->database->Update('assignedstats=0', 'accounts', 'id=' . $this->GetID(), 1);
                }
            }
            else if ($item->GetStatsID() == 119) // Rumble Ball
            {
                $statfights = intval($this->GetTotalStatsfights());
                $winnable = $this->GetLevel() * 10;
                if ($statfights < $winnable)
                {
                    $rechnung = $this->GetStats() + 10;
                    $statfightsnew = intval(10 * ceil($statfights / 10));
                    $stats = StatsList::GetEntryOrEmpty($this->database, $this->GetID(), 1);
                    if ($statfights == $statfightsnew)
                    {
                        $statfightsnew = $statfights + 10;
                    }
                    $statswin = intval($stats["win"]) + ($statfightsnew - $statfights);
                    $statstotal = intval($stats["total"]) + ($statfightsnew - $statfights);
                    $this->database->Update('totalstatsfights="' . $statfightsnew . '"', 'accounts', 'id=' . $this->GetID(), 1);
                    $this->database->Update('win=' . $statswin . ', total=' . $statstotal, 'statslist', 'acc=' . $this->GetID() . ' AND type=1');
                    $this->database->Update('stats="' . $rechnung . '"', 'accounts', 'id=' . $this->GetID(), 1);
                }
                else
                {
                    return 'Du hast schon das Maximum der Statkämpfe erreicht, du musst dein Level steigern um dieses Item zu nutzen.';
                }
            }
            else if($item->GetStatsID() == 223) // Statboost
            {
                // Variable declarations
                $actionStats = 0;
                $eventStats = 0;
                $levelStats = ($this->GetLevel() - 1) * 25;
                $statFightStats = (floor($this->GetTotalStatsFights() / 10) * 10);
                // --------------------

                // Calculation of finished actions and dungeons
                $getSpecialActionStats = $this->database->Select('*', 'actions', 'type=15 AND level <= "' . $this->GetLevel() . '"');
                if ($getSpecialActionStats) {
                    while ($specialActionStats = $getSpecialActionStats->fetch_assoc()) {
                        if ($this->HasSpecialTrainingUsed($specialActionStats['id'])) {
                            $actionStats += $specialActionStats['stats'];
                        }
                    }
                }
                if (!empty($this->GetExtraDungeons())) {
                    $dungeons = $this->GetExtraDungeons();
                    foreach ($dungeons as &$dungeon) {
                        $event = new Event($this->database, $dungeon[0]);
                        if ($event->GetStats() > 0) {
                            $eventStats += $event->GetStats() * $dungeon[1];
                        }
                    }
                }
                // -------------------------------------------

                // Get Statpoints since game-start
                $statsSinceStart = 0;
				$year = 2025;
				$month = 1;
				$day = 19;
				$hour = 12;
				$minute = 0;
				$second = 0;
				$time = mktime($hour, $minute, $second, $month, $day, $year);
                $now = new DateTime("now");
                $hoursSinceOpening = floor(($now->getTimestamp() - $time) / 3600);
                if ($hoursSinceOpening >= 1)
                    $statsSinceStart = $hoursSinceOpening * 6;

                $statsSinceStart = $statsSinceStart + 0;
                // -------------------------------

                // Current Statpoints by assigned Stats
                $rlp = $this->GetMaxLP() / 10;
                $rkp = $this->GetMaxKP() / 10;
                $ratk = $this->GetAttack() / 2;
                $rdef = $this->GetDefense();
                $rechnung = $rlp + $rkp + $ratk + $rdef;
                // ----------------------------------

                // Alle Stats vom Player
                $newplayer = $statsSinceStart + $eventStats + $levelStats + $actionStats + $statFightStats;
                $oldplayer = $rechnung;
                $different = $newplayer - ($oldplayer + $this->GetStats());
                if($different < 0) $different = 0;
                $different = abs($different);
                if($different >= 0)
                {
                    $this->AddDebugLog('star stats eingesetzt');
                    $this->AddDebugLog("Du besitzt ".number_format($oldplayer, 0, '', '.')." Stats");
                    $this->AddDebugLog("Ein neuer Spieler bekommt ".number_format($newplayer, 0, '', '.')." Stats");
                    $this->AddDebugLog("Zum start: ".number_format($statsSinceStart, 0, '', '.'));
                    $this->AddDebugLog("Stats durch Spezialtraining ".number_format($actionStats, 0, '', '.'));
                    $this->AddDebugLog("Stats durch Events ".number_format($eventStats, 0, '', '.'));
                    $this->AddDebugLog("Stats durch Story ".number_format($levelStats, 0, '', '.'));
                    $this->AddDebugLog("Stats durch Statsfight ".number_format($statFightStats, 0, '', '.'));
                    $this->AddDebugLog('Verfügbare Punkte: '.number_format($different,0,',','.'));
                    $this->database->Update('debuglog="'.$this->GetDebugLog().'", stats="'.$different.'", statsresetted=1, resetamount='.$different, 'accounts', 'id="'.$this->GetID().'"');
                }
//
            }
            else if($item->GetStatsID() == 388 && $this->GetPlace() == 10 ||
                $item->GetStatsID() == 389 && $this->GetPlace() == 11 ||
                $item->GetStatsID() == 390 && $this->GetPlace() == 12 ||
                $item->GetStatsID() == 391 && $this->GetPlace() == 13 ||
                $item->GetStatsID() == 392 && $this->GetPlace() == 14 ||
                $item->GetStatsID() == 393 && $this->GetPlace() == 15)
            {
                $this->Revive();
            }
            /*else if ($item->GetStatsID() == 137)
            {
                $this->Revive();
            }*/
            else if ($item->GetStatsID() == 406) // Seltene grüne Wolke
            {
                $fightID = $this->GetFight();
                $result = $this->database->Select('*', 'fighters', 'acc="'.$this->GetID().'" AND fight="'.$fightID.'"', 1);
                if($result)
                {
                    $user = $result->fetch_assoc();
                    $newDefs = floor(10 * ($user['attack'] / 100));
                    $newDef = floor($user['defense'] + $newDefs);
                    $newAtk = floor($user['attack'] - $newDefs);
                    $update = $this->database->Update('defense="'.$newDef.'", attack="'.$newAtk.'"', 'fighters', 'acc="'.$this->GetID().'" AND fight="'.$user['fight'].'"');

                $resultz = $this->database->Select('id, text', 'fights', 'id="' . $fightID . '"', 1);
                $fightText = '';
                if ($resultz)
                {
                    $rowtextz = $resultz->fetch_assoc();
                    $fightText = $rowtextz['text'];
                    $textamount = "einmal";

                    $itemtext = '<tr><td align=center colspan=4><h2>Item Einsatz</h2></td></tr>'; //Das fügt "Item Einsatz" hinzu
                    $itemtext = $itemtext . '<tr><td align=center colspan=3>' . $this->data['name'] . ' aktiviert ' . $textamount . ' seltene grüne Wolke, es erscheint eine grüne Wolke über ' . $this->data['name'] . ' welche die Werte von '.$this->GetName().' tauscht.</td></tr>'.$fightText; //Das ist der normale Text

                    $this->database->Update('text="' . $this->database->EscapeString($itemtext) . '"', 'fights', 'id=' . $fightID, 1);
                }

                }
            }
            else if ($item->GetStatsID() == 407) // Seltene Rote Wolke
            {
                $fightID = $this->GetFight();
                $result = $this->database->Select('*', 'fighters', 'acc="'.$this->GetID().'" AND fight="'.$fightID.'"', 1);
                if($result)
                {
                    $user = $result->fetch_assoc();
                    $newDefs = floor(10 * ($user['defense'] / 100));
                    $newDef = floor($user['attack'] + $newDefs);
                    $newAtk = floor($user['defense'] - $newDefs);
                    $update = $this->database->Update('attack="'.$newDef.'", defense="'.$newAtk.'"', 'fighters', 'acc="'.$this->GetID().'" AND fight="'.$user['fight'].'"');

                    $resultz = $this->database->Select('id, text', 'fights', 'id="' . $fightID . '"', 1);
                    $fightText = '';
                    if ($resultz)
                    {
                        $rowtextz = $resultz->fetch_assoc();
                        $fightText = $rowtextz['text'];
                        $textamount = "einmal";

                        $itemtext = '<tr><td align=center colspan=4><h2>Item Einsatz</h2></td></tr>'; //Das fügt "Item Einsatz" hinzu
                        $itemtext = $itemtext . '<tr><td align=center colspan=3>' . $this->data['name'] . ' aktiviert ' . $textamount . ' Seltene rote Wolke, es erscheint eine rote Wolke über ' . $this->data['name'] . ' welche die Werte von '.$this->GetName().' tauscht.</td></tr>'.$fightText; //Das ist der normale Text

                        $this->database->Update('text="' . $this->database->EscapeString($itemtext) . '"', 'fights', 'id=' . $fightID, 1);
                    }

                }
            }
            }

        $newLP = $this->GetLP() + $lpHeal;
        $newKP = $this->GetKP() + $kpHeal;

        if ($newLP > $this->GetMaxLP())
        {
            $newLP = $this->GetMaxLP();
        }
        else if ($newLP < 0)
        {
            $newLP = 0;
        }

        if ($newKP > $this->GetMaxKP())
        {
            $newKP = $this->GetMaxKP();
        }
        else if ($newKP < 0)
        {
            $newKP = 0;
        }

        $this->SetLP($newLP);
        $this->SetKP($newKP);


        $this->AddDebugLog(' - - used item: ' .$item->GetStatsID() . ' - amount: ' . $amount);
        $this->inventory->RemoveItem($item, $amount);
        $this->database->Update('lp="' . $newLP . '",kp="' . $newKP . '"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
        if($returnmessage)
            return $returnmessage;
    }

    public function GetChallengeTimeSinceNow()
    {
        return time() - strtotime($this->GetChallengeTime());
    }

    public function IsAdminLogged()
    {
        return $this->data['adminlogged'] != 0;
    }

    public function GetAdminLogged()
    {
        return $this->data['adminlogged'];
    }

    public function SendGift($amount, $item)
    {
        if ($item == 0)
        {
            $this->data['itemgift'] += $amount;
        }
        else if ($item == 1)
        {
            $this->data['berrygift'] += $amount;
        }
        else if ($item == 2)
        {
            $this->data['goldgift'] += $amount;
        }
        $this->database->Update('itemgift=' . $this->GetItemGifted() . ', berrygift=' . $this->GetBerryGifted() . ', goldgift=' . $this->GetGoldGifted(), 'accounts', 'id=' . $this->GetID());
    }

    public function GetItemGifted()
    {
        return $this->data['itemgift'];
    }

    public function GetBerryGifted()
    {
        return $this->data['berrygift'];
    }

    public function GetGoldGifted()
    {
        return $this->data['goldgift'];
    }

    public function GetGiftTime()
    {
        return $this->data['gifttime'];
    }

    private function UpdateLastAction()
    {
        $lastClickTime = $this->GetLastClickTime();

        $difference = microtime(true) - $lastClickTime;

        $timestamp = date('Y-m-d H:i:s');
        $newClickTime = microtime(true);

        $update = 'lastaction="' . $timestamp . '"';
        $this->SetLastAction($timestamp);
        $update = $update.',lastclicktime="' . $newClickTime . '"';
        $this->SetLastClickTime($newClickTime);

        if($_GET['p'] != 'infight')
        {

            if ($difference <= 0.5 && $difference > 0)
            {
                $clickCount = $this->GetClickCount() + 1;
                $update = $update . ', clickcount="' . $clickCount . '"';
                $this->SetClickCount($clickCount);
            }
            if($difference <= 5.0 && $difference > 0)
            {
                $update = $update . ', totalclickcount=totalclickcount+1';

                $clickSpeed = $this->GetClickSpeed();
                if($clickSpeed < 0)
                    $clickSpeed = 0;
                $clickSpeed = ($clickSpeed+$difference);
                $update = $update . ', clickspeed="'.$clickSpeed.'"';
            }

        }

        if (!$this->IsAdminLogged())
            $this->database->Update($update, 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function UpdateLastEventAction()
    {
        $timestamp = date('Y-m-d H:i:s');
        $update = 'lasteventaction="' . $timestamp . '"';
        $this->SetLastEventAction($timestamp);

        $this->database->Update($update, 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function GetPvPImage()
    {
        return $this->data['kopfgeldimage'];
    }

    public function SetPvPImage($value)
    {
        $this->data['kopfgeldimage'] = $value;
    }

    public function GetStartingPowerup()
    {
        return $this->data['powerupstart'];
    }

    public function GetBday()
    {
        return $this->data['bday'];
    }

    public function SetBday($value)
    {
        $this->data['bday'] = $value;
        if($this->GetBday() != '0000-00-00')
            $this->database->Update('bday="'.$value.'"', 'accounts', 'id="'.$this->GetID().'"');
        else
            return;
    }

    public function GetBdaySuprise()
    {
        $susi = explode(";", $this->data['bdaysuprise']);
        $tada = $susi[0];
        return $tada;
    }

    public function SetBdaySuprise($value)
    {
        $this->data['bdaysuprise'] = $value;
        $suprise = explode(';', $this->GetBdaySuprise());
        $suprise[0] = $value;
        $check = $suprise[0].";".$suprise[1];
        $this->database->Update('bdaysuprise="'.$check.'"', 'accounts', 'id="'.$this->GetID().'"');
    }

    public function GetShadowAge()
    {
        $susi = explode(';', $this->data['bdaysuprise']);
        return $susi[1];
    }

    public function SetShadowBday($value)
    {
        $shadow = explode(";", $this->data['bdaysuprise']);
        $shadow[1] = $value;
        $check = $shadow[0].";".$shadow[1];
        $this->database->Update('bdaysuprise="'.$check.'"', 'accounts', 'id="'.$this->GetID().'"');
    }

    public function GetAge()
    {
        $exp = explode("-", $this->GetBday());
        $year = $exp[0] != "0000" ? $exp[0] : "1900";
        $month = $exp[1] != "00" ? $exp[1] : "01";
        $day = $exp[2] != "00" ? $exp[2] : "01";
        $now = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $bday = mktime(0, 0, 0, $month, $day, $year);
        $age   = intval(($now - $bday) / (3600 * 24 * 365));
        if($year != "1900")
            return $age;
        return false;
    }

    public function GetWarns()
    {
        $result = $this->database->Select('*', 'warnings', 'userid='.$this->data['userid']);
        if($result && $result->num_rows > 0)
        {
            $warnings = array();
            while($row = $result->fetch_assoc()) {
                $user = $this->database->Select('name', 'accounts', 'id='.$row['charid']);
                $row["name"] = "Unbekannt";
                if($user && $user->num_rows > 0)
                {
                    $username = $user->fetch_assoc()['name'];
                    $row["name"] = $username;
                }
                $warnings[] = $row;
            }
            return $warnings;
        }
        return false;
    }

    public function GetAllWarnings()
    {
        $result = $this->database->Select('*', 'warnings', '', 99999, 'userid');
        $warnings = array();
        if($result && $result->num_rows > 0)
        {
            while($row = $result->fetch_assoc())
            {
                $warnings[] = $row;
            }
        }
        return $warnings;
    }

    public function GetWarnsCount($expired = 1)
    {
        $result = $this->database->Select('*', 'warnings', 'userid='.$this->data['userid'].' AND (active=1 OR active='.$expired.')');
        if($result && $result->num_rows > 0)
            return $result->num_rows;
        return 0;
    }

    public function SetWarnActive($id, $active): void
    {
        $this->database->Update('active='.$active, 'warnings', 'id='.$id);
    }

    public function AddWarning($reason, $expire = 1): void
    {
        $reason = $this->database->EscapeString($reason);
        $datestamp = time()+(24*60*30*60);
        $date = date("d.m.Y", $datestamp);
        $today = date("d.m.Y", time());
        if($this->GetWarnsCount() > 0)
        {
            $this->database->Update('expires="'.$date.'"', 'warnings', 'userid='.$this->data['userid'].' AND active=1');
        }
        $this->database->Insert('userid, charid, reason, received, expires, expire', $this->data['userid'].', '.$this->data['id'].', "'.$reason.'", "'.$today.'", "'.$date.'", '.$expire, 'warnings');

        if($this->GetWarnsCount() > 3)
        {
            $this->SetBanned(1);
            $this->SetBanReason("4 Verwarnungen");
            $this->database->Update('banned=1, banreason="4 Verwarnungen"', 'accounts', 'userid='.$this->data['userid']);
            $this->database->Update('expire=0', 'warnings', 'userid='.$this->data['userid'].' AND active=1');
        }
    }

    public function DeleteWarning($id): void
    {
        $this->database->Delete('warnings', 'id='.$id);
        if($this->GetWarnsCount() > 0)
        {
            $last_warning = $this->GetWarns()[$this->GetWarnsCount()-1];
            $last_warn_date = $last_warning['received'];
            $last_warn_date = date("d.m.Y", strtotime($last_warn_date)+(24*60*30*60));
            $this->database->Update('expires="'.$last_warn_date.'"', 'warnings', 'userid='.$this->data['userid'].' AND active=1');
        }
    }

    public function SetStartingPowerup($value)
    {
        $this->data['powerupstart'] = $value;
    }

    public function GetClickCount()
    {
        return $this->data['clickcount'];
    }

    public function SetClickCount($value)
    {
        $this->data['clickcount'] = $value;
    }

    public function GetTotalClickCount()
    {
        return $this->data['totalclickcount'];
    }

    public function SetTotalClickCount($value)
    {
        $this->data['totalclickcount'] = $value;
    }

    public function GetLastAction()
    {
        return $this->data['lastaction'];
    }

    public function GetLastFight()
    {
        $select = "id, name, type, mode, fighters, state";
        $pID = '[' . $this->GetID() . ']';
        $where = 'fights.fighters LIKE "%' . $pID . '%" AND state = 2';
        $where .= ' AND testfight=0';
        $order = 'id';
        $from = 'fights';
        $result = $this->database->Select($select, $from, $where, 1, $order, 'DESC');
        $fight = $result->fetch_assoc();
        return $fight['id'];
    }

    public function SetLastAction($value)
    {
        $this->data['lastaction'] = $value;
    }

    public function GetLastClickTime()
    {
        return $this->data['lastclicktime'];
    }

    public function SetLastClickTime($value)
    {
        $this->data['lastclicktime'] = $value;
    }

    public function GetClickSpeed()
    {
        return $this->data['clickspeed'];
    }

    public function SetClickSpeed($value)
    {
        $this->data['clickspeed'] = $value;
    }

    public function GetLastEventAction()
    {
        return $this->data['lasteventaction'];
    }

    public function SetLastEventAction($value)
    {
        $this->data['lasteventaction'] = $value;
    }

    public function IsStillActive()
    {
        return $this->data['stillActive'];
    }

    public function GetActiveSince()
    {
        return $this->data['activeSince'];
    }

    public function GetClan()
    {
        return $this->data['clan'];
    }

    public function SetClan($value)
    {
        $this->data['clan'] = $value;
    }

    public function GetClanName()
    {
        return $this->data['clanname'];
    }

    public function GetChatban()
    {
        return $this->data['chatban'];
    }

    public function SetClanName($value)
    {
        $this->data['clanname'] = $value;
    }

    public function GetClanApplication()
    {
        return $this->data['clanapplication'];
    }

    public function SetClanApplication($value)
    {
        $this->data['clanapplication'] = $value;
    }

    public function GetClanApplicationText()
    {
        return $this->data['clanapplicationtext'];
    }

    public function SetClanApplicationText($value)
    {
        $this->data['clanapplicationtext'] = $value;
    }

    public function IsVerified()
    {
        // return $this->data['verified'];
      return true;
    }

    public function SetVerified($value)
    {
        $this->data['verified'] = $value;
        $this->database->Update('verified=' . $value, 'accounts', 'id=' . $this->GetID());
    }

    public function GetTeamUser()
    {
        return $this->data['team'];
    }

    private function LoadPlayer($key, $select = '*', $id = -1)
    {
        if ($key == '' || $this->data[$key] == '')
        {
            return;
        }

        if ($key == 'id')
        {
            $result = $this->database->Select($select, 'accounts', 'id = ' . $id . '', 1);
        }
        else
        {
            $result = $this->database->Select($select, 'accounts', $key . ' = "' . $id . '"', 1);
        }

        if ($result)
        {
            if ($result->num_rows > 0)
            {
                $row = $result->fetch_assoc();
                $this->data = $row;
                $this->valid = true;
            }
            $result->close();
        }

        if ($this->valid && $this->needinventory)
        {
            $this->inventory = new Inventory($this->database, $this->GetID());
        }

        if ($this->IsLocalPlayer() && $this->valid && !$this->isChat)
        {
            $this->UpdateLastAction();
            $this->CalculateAction();
            $this->CalculateTravelAction();
        }
    }

    /*public function FreeFromImpelDown($id)
    {
        $pstart = $this->data['deathplanet'];
        $pcheck = $this->database->Select('*', 'planet', 'id="'.$pstart.'"');
        $ptrue = $pcheck->fetch_assoc();
        $deathPlace = $ptrue['startingplace'];

        if ($pstart == 2 || $pstart == 0 || $deathPlace == 0)
        {
            $deathPlace = 1; // Windmühlendorf
        }
        $this->database->Update('planet="' . $pstart . '", place="' . $deathPlace . '"', 'accounts', 'id=' . $this->data['id'], 1);
        $otherPlayer = new Player($this->database, $id, $this->actionManager);
        $clanpaid = false;
        if ($otherPlayer->GetClan() == $this->GetClan() && $this->GetClan() != 0)
        {
            $clan = new Clan($this->database, $this->GetClan());
            if ($clan->PaysBounty() && $clan->GetBerry() >= $this->GetPvP())
            {
                $clan->RemoveBerry($this->GetPvP(), $this->GetID(), $this->GetName(), 0, "Kopfgeld Impel Down");
                $clanpaid = true;
            }
        }
        if (!$clanpaid)
        {
            $berry = $otherPlayer->GetBerry() - $this->GetPvP();
            $otherPlayer->SetBerry($berry);
            $this->database->Update('zeni=' . $berry, 'accounts', 'id=' . $id, 1);
        }
    }*/

    public function FreeFromImpelDown($id, $with = null)
    {
        $keyid = 0;
        if($this->GetPlace() == 10)
            $keyid = 388;
        else if($this->GetPlace() == 11)
            $keyid = 389;
        else if($this->GetPlace() == 12)
            $keyid = 390;
        else if($this->GetPlace() == 13)
            $keyid = 391;
        else if($this->GetPlace() == 14)
            $keyid = 392;
        else if($this->GetPlace() == 15)
            $keyid = 393;
        $pstart = $this->data['deathplanet'];
        $pcheck = $this->database->Select('*', 'planet', 'id="'.$pstart.'"');
        $ptrue = $pcheck->fetch_assoc();
        $deathPlace = $ptrue['startingplace'];

        if ($pstart == 2 || $pstart == 0 || $deathPlace == 0)
        {
            $deathPlace = 1; // Windmühlendorf
        }
        $this->database->Update('planet="' . $pstart . '", place="' . $deathPlace . '", impeldownpopup=0', 'accounts', 'id=' . $this->data['id'], 1);
        $otherPlayer = new Player($this->database, $id, $this->actionManager);
        $clanpaid = false;
        if ($otherPlayer->GetClan() == $this->GetClan() && $this->GetClan() != 0 && $with == "berry")
        {
            $clan = new Clan($this->database, $this->GetClan());
            if ($clan->PaysBounty() && $clan->GetBerry() >= $this->GetPvP())
            {
                $clan->RemoveBerry($this->GetPvP(), $this->GetID(), $this->GetName(), 0, "Kopfgeld Impel Down");
                $clanpaid = true;
            }
        }
        if (!$clanpaid && $with == "berry")
        {
            $berry = $otherPlayer->GetBerry() - $this->GetPvP();
            $otherPlayer->SetBerry($berry);
            $this->database->Update('zeni=' . $berry, 'accounts', 'id=' . $id, 1);
        }
        else if($with == "key")
        {
            $item = $otherPlayer->GetItemByStatsIDOnly($keyid);
            $otherPlayer->RemoveItems($item, 1);
        }
    }

    public function GetTwoAccountBande()
    {
        $result = $this->database->Select('*', 'accounts', 'id != '.$this->GetID().' AND userid='.$this->GetUserID().' AND clan != '.$this->GetClan());
        if($result)
        {
            if($result->num_rows == 1)
            {
                $user = $result->fetch_assoc();
                return $user['clan'];
            }
        }
        return '';
    }

    public function FriendRequest($id)
    {
        $friendrequests = $this->GetFriendRequests();

        if ($friendrequests != '')
            $friendrequests = explode(";", $friendrequests);
        else
            $friendrequests = array();

        if (in_array($id, $friendrequests))
            return false;

        $friendrequests[] = $id;

        $friendrequests = implode(";", $friendrequests);
        return $this->database->Update('friendrequests="' . $friendrequests . '"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function GetFriendRequests()
    {
        return $this->data['friendrequests'];
    }

    public function FriendRequestRemove($id)
    {
        $friendrequests = $this->GetFriendRequests();
        $friendrequests = explode(";", $friendrequests);
        if (!in_array($id, $friendrequests)) return false;
        $key = array_search($id, $friendrequests);
        array_splice($friendrequests, $key, 1);
        $friendrequests = implode(';', $friendrequests);
        $update = 'friendrequests="' . $friendrequests . '"';
        return $this->database->Update($update, 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function Challenge($fightID)
    {
        $this->SetChallengeFight($fightID);

        $timestamp = $this->GetChallengeTime();
        if (strtotime($this->GetChallengeTime()) < time() - (60 * 10))
        {
            $timestamp = date('Y-m-d H:i:s');
            $this->SetChallengeTime($timestamp);
        }
        $this->database->Update('challengefight ="' . $fightID . '", challengedtime="' . $timestamp . '"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
        $this->SetClanWarPopup(1);
    }

    public function DeclineEvent()
    {
        $this->SetEventInvite(0);
        $this->database->Update('eventinvite="0"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function DeclineChallenge()
    {
        $this->SetChallengeFight(0);
        $this->database->Update('challengefight="0", challengedtime="0"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function LearnSkill($attackID, $skillPoints, $berry, $skilltype = 0)
    {
        $this->AddDebugLog('LearnSkill');
        $attacks = $this->GetAttacks();

        $this->AddDebugLog(' - add Attack ' . $attackID . ' to ' . $attacks);
        $attacks = $attacks . ';' . $attackID;
        $this->AddDebugLog(' - Attacks now: ' . $attacks);

        $this->AddDebugLog(' - set SkillPoints: ' . $this->GetSkillPoints() . ' - ' . $skillPoints);
        $points = $this->GetSkillPoints() - $skillPoints;
        $this->AddDebugLog(' - SkillPoints now: ' . $points);

        $attacks = str_replace(";;", "", $attacks);
        $update = 'attacks="' . $attacks . '",skillpoints="' . $points . '"';
        $this->SetAttacks($attacks);
        $this->SetSkillPoints($points);
        $this->SetBerry($this->GetBerry() - $berry);

        $this->AddDebugLog(' ');
        $debuglog = $this->GetDebugLog();
        $update = $update . ',debuglog="' . $debuglog . '"';
        if ($skilltype == 1 && $this->data['pfad'] != "Zoan")
            $update = $update . ',pfad="Zoan"';
        else if ($skilltype == 2 && $this->data['pfad'] != "Paramecia")
            $update = $update . ',pfad="Paramecia"';
        else if ($skilltype == 3 && $this->data['pfad'] != "Logia")
            $update = $update . ',pfad="Logia"';
        else if ($skilltype == 4 && $this->data['pfad2'] != "Schwertkaempfer")
            $update = $update . ',pfad2="Schwertkaempfer"';
        else if ($skilltype == 5 && $this->data['pfad2'] != "Schwarzfuss")
            $update = $update . ',pfad2="Schwarzfuss"';
        else if ($skilltype == 6 && $this->data['pfad2'] != "Karatekämpfer")
            $update = $update . ',pfad2="Karatekämpfer"';
        $this->database->Update($update, 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function GetBackupDifference()
    {
        $diff = $this->resetTime - $this->backupTime;
        return $diff;
    }

    public function HasActionBeforeReset($actionStart)
    {
        return $this->resetTime > $actionStart;
    }


    public function CalculateAction($cancel = false, $force = false)
    {
        if (!$cancel && ($this->GetAction() == 0 || $this->GetActionCountdown() > 0))
        {
            return;
        }

        $this->AddDebugLog('CalculateAction');

        $action = $this->actionManager->GetAction($this->GetAction());
        //$travelaction = $this->actionManager->GetAction($this->GetTravelAction());
        $this->AddDebugLog(' - Action: ' . $action->GetName());
        $this->AddDebugLog(' - ActionStart: ' . $this->GetActionStart() . ' = ' . strtotime($this->GetActionStart()));
        $this->AddDebugLog(' - Time Now: ' . date("Y-m-d H:i:s") . ' = ' . strtotime("now"));



        if ($cancel)
            $this->AddDebugLog(' - Canceled!');
        if ($force)
            $this->AddDebugLog(' - Forced!');

        $countdown = $this->GetActionCountdown();
        $actionTimes = 0;
        $elapsedMinutes = 0;
        if ($countdown <= 0)
        {
            $elapsedMinutes = $this->GetActionTime();
        }
        else
        {
            $actionTimes = $this->GetActionTime();
            $this->AddDebugLog(' - actionTimes: ' . $actionTimes);
            $leftMinutes = floor($countdown / 60);
            $elapsedMinutes = $actionTimes - $leftMinutes;
        }

        if ($elapsedMinutes < 0)
        {
            $elapsedMinutes = 0;
        }


        $actionMinutes = $action->GetMinutes();
        $times = 0;
        if ($actionMinutes != 0)
            $times = floor($elapsedMinutes / $actionMinutes);
        else
            $times = 1;
        $update = 'action="0",actiontime="0"';

        $this->AddDebugLog(' - countdown: ' . $countdown);
        $this->AddDebugLog(' - elapsedMinutes: ' . number_format($elapsedMinutes, '0', '', '.'));
        $this->AddDebugLog(' - actionMinutes: ' . number_format($actionMinutes, '0', '', '.'));
        $this->AddDebugLog(' - times: ' . number_format($times, '0', '', '.'));

        if ($times != 0 && $action->IsStory())
        {
            $newStory = $this->GetStory() + 1;
            $this->AddDebugLog(' - Update Story to: ' . number_format($newStory, '0', '', '.'));
            $update = $update . ',story="' . $newStory . '"';
            $this->SetStory($newStory);
        }

        if ($times != 0 && $action->IsSideStory())
        {
            $newSideStory = $this->GetSideStory() + 1;
            $this->AddDebugLog(' - Update SideStory to: ' . number_format($newSideStory, '0', '', '.'));
            $update = $update . ',sidestory="' . $newSideStory . '"';
            $this->SetSideStory($newSideStory);
        }

        if ($cancel && $this->GetSkillPointLearn() != 0)
        {
            $newPoints = $this->GetSkillPoints() + $this->GetSkillPointLearn();
            $this->AddDebugLog(' - Update Skillpoints to: ' . number_format($newPoints, '0', '', '.'));
            $update = $update . ',skillpoints="' . $newPoints . '"';
            $this->SetSkillPoints($newPoints);
        }
        $update = $update . ',skillpointlearn="0"';
        $this->SetSkillPointLearn(0);

        if ($times != 0 && $action->GetType() == 1)
        {
            if ($action->GetStats() != 0)
            {
                if ($this->GetStats() == 0)
                {
                    $update = $update . ',statspopup="1"';
                    $this->SetStatsPopup(true);
                }
                $this->AddDebugLog(' - Action Stats: ' . number_format($action->GetStats(), '0', '', '.'));
                $statsGain = $action->GetStats() * $times;
                $newPoints = $this->GetStats() + $statsGain;
                $this->AddDebugLog(' - Statsgain: ' . number_format($statsGain, '0', '', '.'));
                $this->AddDebugLog(' - Update Stats to: ' . number_format($newPoints, '0', '', '.'));
                $update = $update . ',stats="' . $newPoints . '"';
                $this->SetStats($newPoints);
            }
            if ($action->GetPlanet() != 0 && $action->GetPlace() != 0)
            {
                $place = $action->GetPlace();
                $planet = $action->GetPlanet();
                $this->AddDebugLog(' - Action Move Player to: ' . $planet . ' - ' . $place);
                $update = $update . ',planet="' . $planet . '",place="' . $place . '"';
                if ($planet == 2 && $this->GetPlanet() != 2)
                {
                    $update = $update . ',deathplanet="' . $this->GetPlanet() . '",deathplace="' . $this->GetPlace() . '"';
                    $this->SetDeathPlace($this->GetPlace());
                    $this->SetDeathPlanet($this->GetPlanet());
                }
                $this->SetPlace($place);
                $this->SetPlanet($planet);
            }
            $statsTraining = explode(';', $this->GetStatsTraining());
            $maxStats = $this->GetMaxStatsTrain();
            if ($action->GetLP() != 0)
            {
                $addValue = ($action->GetLP() + ($this->GetTrainBonus() * 10)) * $times;

                $count = 0;
                $statsTraining[$count] += $addValue;
                $max = $maxStats * 10;
                //if($statsTraining[$count] >= $max)
                //{
                //$addValue = $addValue - ($statsTraining[$count] - $max);
                //$statsTraining[$count] = $max;
                //}

                $newLP = $this->GetLP() + $addValue;
                $newMLP = $this->GetMaxLP() + $addValue;

                $this->AddDebugLog(' - LP from ' . number_format($this->GetLP(), '0', '', '.') . ' to: ' . number_format($newLP, '0', '', '.'));
                $this->AddDebugLog(' - MLP from ' . number_format($this->GetMaxLP(), '0', '', '.') . ' to: ' . number_format($newMLP, '0', '', '.'));
                $update = $update . ',lp="' . $newLP . '",mlp="' . $newMLP . '"';
                $this->SetLP($newLP);
                $this->SetMaxLP($newMLP);
            }
            if ($action->GetKP() != 0)
            {
                $addValue = ($action->GetKP() + ($this->GetTrainBonus() * 10)) * $times;

                $count = 1;
                $statsTraining[$count] += $addValue;
                $max = $maxStats * 10;
                //if($statsTraining[$count] >= $max)
                //{
                //$addValue = $addValue - ($statsTraining[$count] - $max);
                //$statsTraining[$count] = $max;
                //}

                $newKP = $this->GetKP() + $addValue;
                $newMKP = $this->GetMaxKP() + $addValue;

                $this->AddDebugLog(' - AD from ' . number_format($this->GetKP(), '0', '', '.') . ' to: ' . number_format($newKP, '0', '', '.'));
                $this->AddDebugLog(' - MAD from ' . number_format($this->GetMaxKP(), '0', '', '.') . ' to: ' . number_format($newMKP, '0', '', '.'));
                $update = $update . ',kp="' . $newKP . '",mkp="' . $newMKP . '"';
                $this->SetKP($newKP);
                $this->SetMaxKP($newMKP);
            }
            if ($action->GetAttack() != 0)
            {
                $addValue = ($action->GetAttack() + $this->GetTrainBonus()) * $times;

                $count = 2;
                $statsTraining[$count] += $addValue;
                //if($statsTraining[$count] >= $maxStats)
                //{
                //$addValue = $addValue - ($statsTraining[$count] - $maxStats);
                //$statsTraining[$count] = $maxStats;
                //}

                $newAttack = $this->GetAttack() + $addValue;
                $this->AddDebugLog(' - Attack from ' . number_format($this->GetAttack(), '0', '', '.') . ' to: ' . number_format($newAttack, '0', '', '.'));
                $update = $update . ',attack="' . $newAttack . '"';
                $this->SetAttack($newAttack);
            }
            if ($action->GetDefense() != 0)
            {
                $addValue = ($action->GetDefense() + $this->GetTrainBonus()) * $times;

                $count = 4;
                $statsTraining[$count] += $addValue;
                //if($statsTraining[$count] >= $maxStats)
                //{
                //$addValue = $addValue - ($statsTraining[$count] - $maxStats);
                //$statsTraining[$count] = $maxStats;
                //}

                $newDefense = $this->GetDefense() + $addValue;
                $this->AddDebugLog(' - Defense from ' . number_format($this->GetDefense(), '0', '', '.') . ' to: ' . number_format($newDefense, '0', '', '.'));
                $update = $update . ',defense="' . $newDefense . '"';
                $this->SetDefense($newDefense);
            }

            $newStatsTraining = implode(';', $statsTraining);
            $this->SetStatsTraining($newStatsTraining);
            $update = $update . ',statstraining="' . $newStatsTraining . '"';
        }
        else if ($times != 0 && $action->GetType() == 2 && !$force) //Not allow if cancel was forced, like when the player died
        {
            $place = 1; //"Windmühlendorf"
            $planet = 1; //"East Blue"
            $this->AddDebugLog(' - travel to place ' . $place . ' and planet ' . $planet);
            $update = $update . ',place="' . $place . '",planet="' . $planet . '"';
            $this->SetPlace($place);
            $this->SetPlanet($planet);
            $lp = $this->GetMaxLP();
            $kp = $this->GetMaxKP();
            $this->AddDebugLog(' - reset lp to ' . number_format($lp, '0', '', '.') . ' and ad to ' . number_format($kp, '0', '', '.'));
            $update = $update . ',lp="' . $lp . '",kp="' . $kp . '"';
            $this->SetLP($lp);
            $this->SetKP($kp);
        }
        else if ($times != 0 && $action->GetType() == 3)
        {
            $lp = $action->GetLP() * $times;
            if ($lp > 100)
            {
                $lp = 100;
            }
            $kp = $action->GetKP() * $times;
            if ($kp > 100)
            {
                $kp = 100;
            }
            $lp = $lp / 100;
            $kp = $kp / 100;
            $lp = $this->GetLP() + ($this->GetMaxLP() * $lp);
            $kp = $this->GetKP() + ($this->GetMaxKP() * $kp);
            if ($lp > $this->GetMaxLP())
            {
                $lp = $this->GetMaxLP();
            }
            if ($kp > $this->GetMaxKP())
            {
                $kp = $this->GetMaxKP();
            }
            if ($action->GetStats() != 0)
            {
                if ($this->GetStats() == 0)
                {
                    $update = $update . ',statspopup="1"';
                    $this->SetStatsPopup(true);
                }
                $this->AddDebugLog(' - Action Stats: ' . number_format($action->GetStats(), '0', '', '.'));
                $statsGain = $action->GetStats() * $times;
                $newPoints = $this->GetStats() + $statsGain;
                $this->AddDebugLog(' - Statsgain: ' . number_format($statsGain, '0', '', '.'));
                $this->AddDebugLog(' - Update Stats to: ' . number_format($newPoints, '0', '', '.'));
                $update = $update . ',stats="' . $newPoints . '"';
                $this->SetStats($newPoints);
            }
            $this->AddDebugLog(' - heal lp from ' . number_format($this->GetLP(), '0', '', '.') . ' to ' . number_format($lp, '0', '', '.'));
            $this->AddDebugLog(' - heal ad from ' . number_format($this->GetKP(), '0', '', '.') . ' to ' . number_format($kp, '0', '', '.'));
            $update = $update . ',lp="' . $lp . '",kp="' . $kp . '"';
        }
        else if ($countdown <= 0 && $action->GetType() == 5 && !$force)
        {
            $attacks = $this->GetAttacks();
            $attackID = $this->GetLearningAttack();

            $this->AddDebugLog(' - add Attack ' . $attackID . ' to ' . $attacks);
            $attacks = $attacks . ';' . $attackID;
            $this->AddDebugLog(' - Attacks now: ' . $attacks);
            $attacks = str_replace(";;", "", $attacks);
            $update = $update . ',learningattack="",attacks="' . $attacks . '"';
            $this->SetAttacks($attacks);
        }
        else if ($times != 0 && $action->GetType() == 6)
        {
            if ($action->GetID() == 4) //Upgrade
            {
                $this->AddDebugLog(' - UpgradeRüstungshaki');
                $this->UpgradeRüstungshaki($action->GetPrice());
            }
            else if ($action->GetID() == 18) //Upgrade
            {
                $this->AddDebugLog(' - Upgrade Aura der Phönixflammen');
                $this->UpgradeAura(128, $action->GetPrice());
            }
            else if ($action->GetID() == 19) //Upgrade
            {
                $this->AddDebugLog(' - Upgrade Aura vom Gear');
                $this->UpgradeAura(129, $action->GetPrice());
            }
            else if ($action->GetID() == 20) //Upgrade
            {
                $this->AddDebugLog(' - Upgrade Aura der Wüste');
                $this->UpgradeAura(126, $action->GetPrice());
            }
            else if ($action->GetID() == 21) //Upgrade
            {
                $this->AddDebugLog(' - Upgrade Aura des Dämonengottes');
                $this->UpgradeAura(127, $action->GetPrice());
            }
            else if ($action->GetID() == 22) //Upgrade
            {
                $this->AddDebugLog(' - Upgrade Aura des Teufels');
                $this->UpgradeAura(125, $action->GetPrice());
            }
            else if ($action->GetID() == 23) //Upgrade
            {
                $this->AddDebugLog(' - Upgrade Aura der Waffen');
                $this->UpgradeAura(130, $action->GetPrice());
            }
        }
        else if ($times != 0 && $action->GetType() == 7)
        {
            $itemManager = new itemManager($this->database);
            $itemID = $action->GetEarnItem();
            $item = $itemManager->GetItem($itemID);
            $this->AddDebugLog(' - Earn item ' . $item->GetName() . ' (' . $itemID . ')');
            $this->AddItems($item, $item, $times);
        }
        else if ($times != 0 && $action->GetType() == 8)
        {
            if ($action->GetStats() != 0)
            {
                if ($this->GetStats() == 0)
                {
                    $update = $update . ',statspopup="1"';
                    $this->SetStatsPopup(true);
                }
                $this->AddDebugLog(' - Action Stats: ' . number_format($action->GetStats(), '0', '', '.'));
                $statsGain = $action->GetStats() * $times;
                $newPoints = $this->GetStats() + $statsGain;
                $this->AddDebugLog(' - Statsgain: ' . number_format($statsGain, '0', '', '.'));
                $this->AddDebugLog(' - Update Stats to: ' . number_format($newPoints, '0', '', '.'));
                $update = $update . ',stats="' . $newPoints . '"';
                $this->SetStats($newPoints);
            }

            $itementry = array(316 => 0, 315=>0, 103=>0, 34=>0, 71=>0, 319=>0, 60=>0, 102=>0, 31=>0, 355=>0);
            $PMManager = new PMManager($this->database, $this->GetID());
            for ($it = 0; $it < $times; ++$it)
            {
                $rand = rand(1, 100);
                if($this->GetRace() == "Pirat") { $luck = rand(1, 100); } else { $luck = 26; }
                if ($rand == 1 || $luck == 1) // Seltener Splitter
                {
                    $itementry[316] += 1;
                }
                if ($rand > 1 && $rand <= 4 || $luck > 1 && ($luck < 3)) // Einfacher Splitter
                {
                    $itementry[315] += 1;
                }
                if ($rand > 4 && $rand <= 9 || $luck > 2 && ($luck < 7)) // Geldsack
                {
                    $itementry[103] += 1;
                }
                if ($rand > 9 && $rand <= 14 || $luck > 6 && ($luck < 10)) // Starke Medizin
                {
                    $itementry[34] += 1;
                }
                if ($rand > 14 && $rand <= 24 || $luck > 9 && ($luck < 11)) // Schmiede Werkzeug
                {
                    $itementry[71] += 1;
                }
                if ($rand > 24 && $rand <= 37 || $luck > 10 && ($luck < 19)) // Waffen Truhe
                {
                    $itementry[319] += 1;
                }
                if ($rand > 37 && $rand <= 52 || $luck > 18 && ($luck < 21)) // Schatztruhe
                {
                    $itementry[60] += 1;
                }
                if ($rand > 52 && $rand <= 70 || $luck > 20 && ($luck < 23)) // Regenbogen Truhe
                {
                    $itementry[102] += 1;
                }
                if ($rand > 70 && $rand <= 80 || $luck > 22 && ($luck < 25)) // Schwache Medizin
                {
                    $itementry[31] += 1;
                }
                if ($rand > 80 && $rand <= 100 || $luck > 24 && ($luck < 26)) // Material Truhe
                {
                    $itementry[355] += 1;
                }
            }
            if($itementry[316] > 0 || $itementry[315] > 0 || $itementry[103] > 0 || $itementry[34] > 0 || $itementry[71] > 0 || $itementry[319] > 0 || $itementry[60] > 0 || $itementry[102] > 0 || $itementry[31] > 0 || $itementry[355] > 0)
            {
                $itemManager = new itemManager($this->database);
                $pm = "<p style='text-align:center'>Glückwunsch <b>" . $this->GetName() . "</b>,<br/>du hast auf deiner Schatzsuche einiges gefunden:</p> <table>";
                if($itementry[316] > 0)
                {
                    $this->AddItems($itemManager->GetItem(316), $itemManager->GetItem(316), $itementry[316]);
                    $item = $itemManager->GetItem(316);
                    $pm .= "<tr><td align='right'><img src='img/items/" . $item->GetImage() . ".png' alt='".$item->GetName()."' title='".$item->GetName()."' width='80px' height='80px'/></td>
					<td>". number_format($itementry[316], 0, ',', '.')."x " . $item->GetName() . "</td></tr>";
                }
                if($itementry[102] > 0)
                {
                    $this->AddItems($itemManager->GetItem(102), $itemManager->GetItem(102), $itementry[102]);
                    $item = $itemManager->GetItem(102);
                    $pm .= "<tr><td align='right'><img src='img/items/" . $item->GetImage() . ".png' alt='".$item->GetName()."' title='".$item->GetName()."' width='80px' height='80px'/></td>
					<td>". number_format($itementry[102], 0, ',', '.')."x " . $item->GetName() . "</td></tr>";
                }
                if($itementry[315] > 0)
                {
                    $this->AddItems($itemManager->GetItem(315), $itemManager->GetItem(315), $itementry[315]);
                    $item = $itemManager->GetItem(315);
                    $pm .= "<tr><td align='right'><img src='img/items/" . $item->GetImage() . ".png' alt='".$item->GetName()."' title='".$item->GetName()."' width='80px' height='80px'/></td>
					<td>". number_format($itementry[315], 0, ',', '.')."x " . $item->GetName() . "</td></tr>";
                }
                if($itementry[103] > 0)
                {
                    $this->AddItems($itemManager->GetItem(103), $itemManager->GetItem(103), $itementry[103]);
                    $item = $itemManager->GetItem(103);
                    $pm .= "<tr><td align='right'><img src='img/items/" . $item->GetImage() . ".png' alt='".$item->GetName()."' title='".$item->GetName()."' width='80px' height='80px'/></td>
					<td>". number_format($itementry[103], 0, ',', '.')."x " . $item->GetName() . "</td></tr>";
                }
                if($itementry[34] > 0)
                {
                    $this->AddItems($itemManager->GetItem(34), $itemManager->GetItem(34), $itementry[34]);
                    $item = $itemManager->GetItem(34);
                    $pm .= "<tr><td align='right'><img src='img/items/" . $item->GetImage() . ".png' alt='".$item->GetName()."' title='".$item->GetName()."' width='80px' height='80px'/></td>
					<td>". number_format($itementry[34], 0, ',', '.')."x " . $item->GetName() . "</td></tr>";
                }
                if($itementry[71] > 0)
                {
                    $this->AddItems($itemManager->GetItem(71), $itemManager->GetItem(71), $itementry[71]);
                    $item = $itemManager->GetItem(71);
                    $pm .= "<tr><td align='right'><img src='img/items/" . $item->GetImage() . ".png' alt='".$item->GetName()."' title='".$item->GetName()."' width='80px' height='80px'/></td>
					<td>". number_format($itementry[71], 0, ',', '.')."x " . $item->GetName() . "</td></tr>";
                }
                if($itementry[319] > 0)
                {
                    $this->AddItems($itemManager->GetItem(319), $itemManager->GetItem(319), $itementry[319]);
                    $item = $itemManager->GetItem(319);
                    $pm .= "<tr><td align='right'><img src='img/items/" . $item->GetImage() . ".png' alt='".$item->GetName()."' title='".$item->GetName()."' width='80px' height='80px'/></td>
					<td>". number_format($itementry[319], 0, ',', '.')."x " . $item->GetName() . "</td></tr>";
                }
                if($itementry[60] > 0)
                {
                    $this->AddItems($itemManager->GetItem(60), $itemManager->GetItem(60), $itementry[60]);
                    $item = $itemManager->GetItem(60);
                    $pm .= "<tr><td align='right'><img src='img/items/" . $item->GetImage() . ".png' alt='".$item->GetName()."' title='".$item->GetName()."' width='80px' height='80px'/></td>
					<td>". number_format($itementry[60], 0, ',', '.')."x " . $item->GetName() . "</td></tr>";
                }
                if($itementry[31] > 0)
                {
                    $this->AddItems($itemManager->GetItem(31), $itemManager->GetItem(31), $itementry[31]);
                    $item = $itemManager->GetItem(31);
                    $pm .= "<tr><td align='right'><img src='img/items/" . $item->GetImage() . ".png' alt='".$item->GetName()."' title='".$item->GetName()."' width='80px' height='80px'/></td>
					<td>". number_format($itementry[31], 0, ',', '.')."x " . $item->GetName() . "</td></tr>";
                }
                if($itementry[355] > 0)
                {
                    $this->AddItems($itemManager->GetItem(355), $itemManager->GetItem(355), $itementry[355]);
                    $item = $itemManager->GetItem(355);
                    $pm .= "<tr><td align='right'><img src='img/items/" . $item->GetImage() . ".png' alt='".$item->GetName()."' title='".$item->GetName()."' width='80px' height='80px'/></td>
					<td>". number_format($itementry[355], 0, ',', '.')."x " . $item->GetName() . "</td></tr>";
                }
            }
            $pm = "<center>".$pm."</table></center>";
            $PMManager->SendPM(0, 'img/system.png', 'SYSTEM', 'Siehe Da', $pm, $this->GetName(), 1);
        }
        else if ($times != 0 && $action->GetType() == 15)
        {
            if ($action->GetStats() != 0)
            {
                if ($this->GetStats() == 0)
                {
                    $update = $update . ',statspopup="1"';
                    $this->SetStatsPopup(true);
                }
                $this->AddDebugLog(' - Action Stats: ' . number_format($action->GetStats(), '0', '', '.'));
                $statsGain = $action->GetStats() * $times;
                $newPoints = $this->GetStats() + $statsGain;
                $spctrainings = $this->GetSpecialTrainings();
                if ($spctrainings != '')
                    $spctrainings .= ';' . $action->GetID();
                else
                    $spctrainings = strval($action->GetID());
                $this->SetSpecialTrainings($spctrainings);
                $this->AddDebugLog(' - Statsgain: ' . number_format($statsGain, '0', '', '.'));
                $this->AddDebugLog(' - Update Stats to: ' . number_format($newPoints, '0', '', '.'));
                $update = $update . ',stats="' . $newPoints . '",spcialtrainuses="' . $spctrainings . '"';
                $this->SetStats($newPoints);
            }
        }

        if ($times != 0)
        {
            $titelManager = new titelManager($this->database);
            $titelManager->AddTitelAction($this, $times, $action->GetID());
        }

        $this->SetAction(0);
        $this->SetTravelAction(0);
        $this->SetActionTime(0);
        $this->SetTravelActionTime(0);

        $this->AddDebugLog(' ');
        $debuglog = $this->GetDebugLog();
        $update = $update . ',debuglog="' . $debuglog . '"';
        $this->database->Update($update, 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function CalculateTravelAction($cancel = false, $force = false)
    {
        if (!$cancel && ($this->GetTravelAction() == 0 || $this->GetTravelActionCountdown() > 0))
        {
            return;
        }

        $this->AddDebugLog('CalculateTravelAction');

        $action = $this->actionManager->GetAction($this->GetTravelAction());
        $this->AddDebugLog(' - Action: ' . $action->GetName());
        $this->AddDebugLog(' - ActionStart: ' . $this->GetTravelActionStart() . ' = ' . strtotime($this->GetTravelActionStart()));
        $this->AddDebugLog(' - Time Now: ' . date("Y-m-d H:i:s") . ' = ' . strtotime("now"));

        if ($cancel)
            $this->AddDebugLog(' - Canceled!');
        if ($force)
            $this->AddDebugLog(' - Forced!');

        $countdown = $this->GetTravelActionCountdown();
        $actionTimes = 0;
        $elapsedMinutes = 0;
        if ($countdown <= 0)
        {
            $elapsedMinutes = $this->GetTravelActionTime();
        }
        else
        {
            $actionTimes = $this->GetTravelActionTime();
            $this->AddDebugLog(' - actionTimes: ' . $actionTimes);
            $leftMinutes = floor($countdown / 60);
            $elapsedMinutes = $actionTimes - $leftMinutes;
        }

        if ($elapsedMinutes < 0)
        {
            $elapsedMinutes = 0;
        }


        $actionMinutes = $action->GetMinutes();
        $times = 0;
        if ($actionMinutes != 0)
            $times = floor($elapsedMinutes / $actionMinutes);
        else
            $times = 1;
        $update = 'travelaction="0",travelactiontime="0"';

        $this->AddDebugLog(' - countdown: ' . $countdown);
        $this->AddDebugLog(' - elapsedMinutes: ' . $elapsedMinutes);
        $this->AddDebugLog(' - actionMinutes: ' . $actionMinutes);
        $this->AddDebugLog(' - times: ' . $times);

        if ($times != 0 && $action->IsStory())
        {
            $newStory = $this->GetStory() + 1;
            $this->AddDebugLog(' - Update Story to: ' . number_format($newStory, '0', '', '.'));
            $update = $update . ',story="' . $newStory . '"';
            $this->SetStory($newStory);
        }

        if ($times != 0 && $action->IsSideStory())
        {
            $newSideStory = $this->GetSideStory() + 1;
            $this->AddDebugLog(' - Update SideStory to: ' . number_format($newSideStory, '0', '', '.'));
            $update = $update . ',sidestory="' . $newSideStory . '"';
            $this->SetSideStory($newSideStory);
        }

        if ($cancel && $this->GetSkillPointLearn() != 0)
        {
            $newPoints = $this->GetSkillPoints() + $this->GetSkillPointLearn();
            $this->AddDebugLog(' - Update Skillpoints to: ' . number_format($newPoints, '0', '', '.'));
            $update = $update . ',skillpoints="' . $newPoints . '"';
            $this->SetSkillPoints($newPoints);
        }
        $update = $update . ',skillpointlearn="0"';
        $this->SetSkillPointLearn(0);

        if ($action->GetType() == 4) {
            $ortTravelID = 2;
            $planetTravelID = 13;
            if ($action->GetID() == $planetTravelID || $action->GetPlanet() != 0)
            {
                $secondsLeft = $this->GetTravelActionCountdown();
                if ($secondsLeft <= 0) {
                    $travelPlanet = new Planet($this->database, $this->GetTravelPlanet());
                    $travelPlanet->GetID() == 1 ? $StartingPlace = 16 : $StartingPlace = $travelPlanet->GetStartingPlace();
                    if($action->GetPlace() != 0) {
                        $StartingPlace = $action->GetPlace();
                    }
                    $newPlace = new Place($this->database, $StartingPlace, $this->actionManager);
                    $x = $newPlace->GetX();
                    $y = $newPlace->GetY();
                    $this->AddDebugLog(' - travel to planet ' . $travelPlanet->GetName());
                    $this->AddDebugLog(' - travel to place ' . $newPlace->GetName());
                    $this->SetTravelPlanet(0);
                    $this->SetPlanet($travelPlanet->GetID());
                    $this->SetPlace($newPlace->GetID());
                    $this->SetX($x);
                    $this->SetY($y);
                    if ($action->GetStats() != 0)
                    {
                        if ($this->GetStats() == 0)
                        {
                            $update = $update . ',statspopup="1"';
                            $this->SetStatsPopup(true);
                        }
                        $this->AddDebugLog(' - Action Stats: ' . number_format($action->GetStats(), '0', '', '.'));
                        $statsGain = $action->GetStats() * $times;
                        $newPoints = $this->GetStats() + $statsGain;
                        $this->AddDebugLog(' - Statsgain: ' . number_format($statsGain, '0', '', '.'));
                        $this->AddDebugLog(' - Update Stats to: ' . number_format($newPoints, '0', '', '.'));
                        $update = $update . ',stats="' . $newPoints . '"';
                        $this->SetStats($newPoints);
                    }
                    $update = $update . ',planet="' . $travelPlanet->GetID() . '", travelplanet="",x="' . $x . '",y="' . $y . '",place="' . $newPlace->GetID() . '"';

                    $traveledTime = $this->GetTravelActionTime() - round($secondsLeft / 60);
                    $tag = date("w");
                    if ($traveledTime > 0) {
                        $travelTimeLeft = $this->GetTravelTimeLeft() + $traveledTime;
                        if ($this->TravelSpeededUp() == 0 && $cancel == false && $force == false && $action->GetPlanet() == 0 && $tag != 5) {

                            $rand = rand(1, 100);
                            $luck = ($this->data['race'] == "Pirat") ? 45 : 35;
                            $itemManager = new itemManager($this->database);
                            $pPlanet = new Planet($this->database, $this->GetPlanet(), null);
                            $PMManager = new PMManager($this->database, $this->GetID());
                            if ($rand <= $luck) {
                                $this->AddItems($itemManager->GetItem(60), $itemManager->GetItem(60), 1);
                                $pm = "<p style='text-align:center;'>Glückwunsch <b>" . $this->GetName() . "</b>,<br/>du hast auf deiner Reise nach <b>" . $pPlanet->GetName() . "</b> eine Schatztruhe gefunden,<br/>öffne sie, vielleicht ist sie voller Schätze.</p>";
                                $PMManager->SendPM(0, 'img/system.png', 'SYSTEM', 'Siehe Da', $pm, $this->GetName(), 1);
                            }
                            $luck = ($this->GetRace() == "Pirat") ? 55 : 65;
                            if ($rand >= $luck) {
                                $this->AddItems($itemManager->GetItem(102), $itemManager->GetItem(102), 1);
                                $pm = "<p style='text-align:center;'>Glückwunsch <b>" . $this->GetName() . "</b>,<br/>du hast auf deiner Reise nach <b>" . $pPlanet->GetName() . "</b> eine Regenbogen Truhe gefunden,<br/>öffne sie, vielleicht ist sie voller Schätze.</p>";
                                $PMManager->SendPM(0, 'img/system.png', 'SYSTEM', 'Siehe Da', $pm, $this->GetName(), 1);
                            }
                            $luckmin = ($this->GetRace() == "Pirat") ? 45 : 35;
                            $luckmax = ($this->GetRace() == "Pirat") ? 55 : 65;
                            if ($rand > $luckmin && $rand < $luckmax) {
                                $this->AddItems($itemManager->GetItem(103), $itemManager->GetItem(103), 1);
                                $pm = "<p style='text-align:center;'>Glückwunsch <b>" . $this->GetName() . "</b>,<br/>du hast auf deiner Reise nach <b>" . $pPlanet->GetName() . "</b> einen Geldsack gefunden,<br/>öffne sie, vielleicht befindet sich ja etwas in diesem.</p>";
                                $PMManager->SendPM(0, 'img/system.png', 'SYSTEM', 'Siehe Da', $pm, $this->GetName(), 1);
                            }
                        }
                        $this->SetTravelSpeededUp(0);
                        $update = $update . ',traveltimeleft="' . $travelTimeLeft . '",speededup="0"';
                    }
                }
            }
            else if ($action->GetID() == $ortTravelID || $action->GetID() == 26)
            {
                $place = $this->GetTravelPlace();
                $x = 0;
                $y = 0;
                $secondsLeft = $this->GetTravelActionCountdown();
                if ($secondsLeft > 0 && ($cancel || $force))
                {
                    $place = $this->GetPlace();

                    $previousPlace = new Place($this->database, $this->GetPreviousPlace(), null);
                    $travelPlace = new Place($this->database, $this->GetTravelPlace(), null);

                    $pX = $previousPlace->GetX();
                    $pY = $previousPlace->GetY();
                    if ($this->GetPreviousPlace() == -1)
                    {
                        $pX = $this->GetX();
                        $pY = $this->GetY();
                    }
                    $travelTime = abs($travelPlace->GetX() - $pX) + abs($travelPlace->GetY() - $pY);
                    $tempX = $travelPlace->GetX() - $pX;
                    $tempY = $travelPlace->GetY() - $pY;

                    $maxSeconds = $this->GetTravelActionTime() * 60;
                    $secondsPassed = 0;
                    if ($maxSeconds != 0)
                    {
                        $secondsPassed = 1 - ($secondsLeft / $maxSeconds);
                    }
                    $xAdd = round($tempX * $secondsPassed);
                    $yAdd = round($tempY * $secondsPassed);
                    if ($xAdd == 0 && $yAdd == 0)
                    {
                        $x = $this->GetX();
                        $y = $this->GetY();
                    }
                    else
                    {
                        $x = $pX + $xAdd;
                        $y = $pY + $yAdd;
                    }
                }

                $pPlace = new Place($this->database, $place, null);
                $this->AddDebugLog(' - travel to place ' . $pPlace->GetName());
                $this->AddDebugLog(' - travel to x ' . $x);
                $this->AddDebugLog(' - travel to y ' . $y);
                $this->SetPreviousPlace('');
                $this->SetPlace($place);
                $this->SetX($x);
                $this->SetY($y);
                $update = $update . ',previousplace="",x="' . $x . '",y="' . $y . '",place="' . $place . '"';

                $traveledTime = $this->GetTravelActionTime() - round($secondsLeft / 60);
                if ($traveledTime > 0)
                {
                    $leftTime = $this->GetTravelTimeLeft();
                    if (!is_numeric($leftTime))
                        $leftTime = 0;
                    $travelTimeLeft = $leftTime + $traveledTime;
                    if ($this->TravelSpeededUp() == 0 && $cancel == false && $force == false && date("w") != 5)
                    {
                        $rand = rand(1, 100);
                        $luck = ($this->data['race'] == "Pirat") ? 45 : 35;
                        $itemManager = new itemManager($this->database);
                        $PMManager = new PMManager($this->database, $this->GetID());
                        if ($rand <= $luck)
                        {
                            $this->AddItems($itemManager->GetItem(60), $itemManager->GetItem(60), 1);
                            $pm = "<p style='text-align:center;'>Glückwunsch <b>" . $this->GetName() . "</b>,<br/>du hast auf deiner Reise nach <b>" . $pPlace->GetName() . "</b> eine Schatztruhe gefunden,<br/>öffne sie, vielleicht ist sie voller Schätze.</p>";
                            $PMManager->SendPM(0, 'img/system.png', 'SYSTEM', 'Siehe Da', $pm, $this->GetName(), 1);
                        }
                        $luck = ($this->GetRace() == "Pirat") ? 55 : 65;
                        if ($rand >= $luck)
                        {
                            $this->AddItems($itemManager->GetItem(102), $itemManager->GetItem(102), 1);
                            $pm = "<p style='text-align:center;'>Glückwunsch <b>" . $this->GetName() . "</b>,<br/>du hast auf deiner Reise nach <b>" . $pPlace->GetName() . "</b> eine Regenbogen Truhe gefunden,<br/>öffne sie, vielleicht ist sie voller Schätze.</p>";
                            $PMManager->SendPM(0, 'img/system.png', 'SYSTEM', 'Siehe Da', $pm, $this->GetName(), 1);
                        }
                        $luckmin = ($this->GetRace() == "Pirat") ? 45 : 35;
                        $luckmax = ($this->GetRace() == "Pirat") ? 55 : 65;
                        if ($rand > $luckmin && $rand < $luckmax)
                        {
                            $this->AddItems($itemManager->GetItem(103), $itemManager->GetItem(103), 1);
                            $pm = "<p style='text-align:center;'>Glückwunsch <b>" . $this->GetName() . "</b>,<br/>du hast auf deiner Reise nach <b>" . $pPlace->GetName() . "</b> einen Geldsack gefunden,<br/>öffne ihn, vielleicht befindet sich ja etwas in diesem.</p>";
                            $PMManager->SendPM(0, 'img/system.png', 'SYSTEM', 'Siehe Da', $pm, $this->GetName(), 1);
                        }
                    }
                    $this->SetTravelSpeededUp(0);
                    $update = $update . ',traveltimeleft="' . $travelTimeLeft . '",speededup="0"';
                }
            }
        }
        $this->SetTravelAction(0);
        $this->SetTravelActionTime(0);

        $this->AddDebugLog(' ');
        $debuglog = $this->GetDebugLog();
        $update = $update . ',debuglog="' . $debuglog . '"';
        $this->database->Update($update, 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function GetInventory() : Inventory
    {
        return $this->inventory;
    }

    public function SetInventory($inventory)
    {
        $this->inventory = $inventory;
    }

    public function IsLogged()
    {
        return $this->data['id'] != 0;
    }

    public function IsOnline()
    {
        $lastaction = strtotime($this->GetLastAction());
        $difference = time() - $lastaction;
        $hours = 30 * 60;
        return $difference < $hours;
    }

    public function IsBanned(): bool
    {
        return $this->data['banned'] == 1;
    }

    public function IsDeleted(): bool
    {
        return $this->data['deleted'] == 1;
    }

    public function GetBanReason()
    {
        return $this->data['banreason'];
    }

    public function GetTravelTimeLeft()
    {
        return $this->data['traveltimeleft'];
    }

    public function SetTravelTimeLeft($value)
    {
        $this->data['traveltimeleft'] = $value;
    }

    public function SetBanned($value)
    {
        $this->data['banned'] = $value;
    }

    public function SetBanReason($value)
    {
        $this->data['banreason'] = $value;
    }

    public function HasSpecialTrainingUsed($value)
    {
        $trainingss = $this->data['spcialtrainuses'];
        $trainings = explode(";", $trainingss);
        if(in_array($value, $trainings))
        {
          return count($trainings);
        }
    }

    public function GetSpecialTrainings()
    {
        return $this->data['spcialtrainuses'];
    }

    public function SetSpecialTrainings($value)
    {
        $this->data['spcialtrainuses'] = $value;
    }

    public function GetCreationSince()
    {
        $created = strtotime($this->data['created']);
        $time = time();

        return $time - $created;
    }

    public function GetCreationTime()
    {
        return $this->data['created'];
    }

    public function ChangeUserName($oldName, $newName)
    {
        $this->database->Update("openmessage = REPLACE(openmessage, '".$oldName."', '".$newName."')", "ticket", "openmessage regexp '(^|[[:space:]])".$oldName."([[:space:]]|$)'");
        $this->database->Update("verlauf = REPLACE(verlauf, '".$oldName."', '".$newName."')", "ticket", "verlauf regexp '(^|[[:space:]])".$oldName."([[:space:]]|$)'");
        $this->database->Update("name = '".$newName."'", "statslist", "name regexp '(^|[[:space:]])".$oldName."([[:space:]]|$)'");
        $this->database->Update("sendername = '".$newName."'", "pms", "sendername regexp '(^|[[:space:]])".$oldName."([[:space:]]|$)'");
        $this->database->Update("recceivername = '".$newName."'", "pms", "recceivername regexp '(^|[[:space:]])".$oldName."([[:space:]]|$)'");
        $this->database->Update("text = REPLACE(text, '".$oldName."', '".$newName."')", "pms", "text regexp '(^|[[:space:]])".$oldName."([[:space:]]|$)'");
        $this->database->Update("topic = REPLACE(topic, '".$oldName."', '".$newName."')", "pms", "topic regexp '(^|[[:space:]])".$oldName."([[:space:]]|$)'");
        $this->database->Update("authorname = '".$newName."'", "News", "authorname LIKE '".$oldName."'");
        $this->database->Update("title = REPLACE(title, '".$oldName."', '".$newName."')", "News", "title regexp '(^|[[:space:]])".$oldName."([[:space:]]|$)'");
        $this->database->Update("text = REPLACE(text, '".$oldName."', '".$newName."')", "News", "text regexp '(^|[[:space:]])".$oldName."([[:space:]]|$)'");
        $this->database->Update("likes = REPLACE(likes, '".$oldName."', '".$newName."')", "News", "likes regexp '(^|[[:space:]])".$oldName."([[:space:]]|$)'");
        $this->database->Update("dislikes = REPLACE(dislikes, '".$oldName."', '".$newName."')", "News", "dislikes regexp '(^|[[:space:]])".$oldName."([[:space:]]|$)'");
        $this->database->Update("text = REPLACE(text, '".$oldName."', '".$newName."')", "meldungen", "text regexp '(^|[[:space:]])".$oldName."([[:space:]]|$)'");
        $this->database->Update("receiver = '".$newName."'", "meldungen", "receiver LIKE '".$oldName."'");
        $this->database->Update("sender = '".$newName."'", "meldungen", "sender LIKE '".$oldName."'");
        $this->database->Update("seller = '".$newName."'", "market", "seller LIKE '".$oldName."'");
        $this->database->Update("name = REPLACE(name, '".$oldName."', '".$newName."')", "lastfights", "name regexp '(^|[[:space:]])".$oldName."([[:space:]]|$)'");
        $this->database->Update("text = REPLACE(text, '".$oldName."', '".$newName."')", "lastfights", "text regexp '(^|[[:space:]])".$oldName."([[:space:]]|$)'");
        $this->database->Update("debuglog = REPLACE(debuglog, '".$oldName."', '".$newName."')", "lastfights", "debuglog regexp '(^|[[:space:]])".$oldName."([[:space:]]|$)'");
        $this->database->Update("name = REPLACE(name, '".$oldName."', '".$newName."')", "fights", "name regexp '(^|[[:space:]])".$oldName."([[:space:]]|$)'");
        $this->database->Update("text = REPLACE(text, '".$oldName."', '".$newName."')", "fights", "text regexp '(^|[[:space:]])".$oldName."([[:space:]]|$)'");
        $this->database->Update("debuglog = REPLACE(debuglog, '".$oldName."', '".$newName."')", "fights", "debuglog regexp '(^|[[:space:]])".$oldName."([[:space:]]|$)'");
        $this->database->Update("log = REPLACE(log, '".$oldName."', '".$newName."')", "clans", "log regexp '(^|[[:space:]])".$oldName."([[:space:]]|$)'");
        $this->database->Update("shoutbox = REPLACE(shoutbox, '".$oldName."', '".$newName."')", "clans", "shoutbox regexp '(^|[[:space:]])".$oldName."([[:space:]]|$)'");
        $this->database->Update("debuglog = REPLACE(debuglog, '".$oldName."', '".$newName."')", "accounts", "debuglog regexp '(^|[[:space:]])".$oldName."([[:space:]]|$)'");
        $this->database->Update("multitext = REPLACE(multitext, '".$oldName."', '".$newName."')", "accounts", "multitext regexp '(^|[[:space:]])".$oldName."([[:space:]]|$)'");
        $this->database->Update("name = '".$newName."'", "accounts", "name LIKE '".$oldName."'");
    }

    public function TrackFailLogin($acc, $ip)
    {
        $acc = $this->database->EscapeString($acc);
        $date = date("Y-m-d H:i:s");
        $text = "folgende IP: ".$ip." hat sich versucht in den User ".$acc." einzuloggen";
        $result = $this->database->Insert('text, receiver, sender, status, date, type', '"' . $text . '", 1, 1, 0, "'.$date.'", 2',  'meldungen');
    }

    public function GetReviveDays()
    {
        return $this->data['revivedays'];
    }

    public function SetReviveDays($value)
    {
        $this->database->Update('revivedays="'.$value.'"', 'accounts', 'id="'.$this->GetID().'"');
    }

    public function CreateCharacter($rasse, $pfad, $chara, $raceimage, $mainID, $email, $donor, $discordID, $IsMulti)
    {
        $rasse = $this->database->EscapeString($rasse);
        $chara = $this->database->EscapeString($chara);
        $pfad = $this->database->EscapeString($pfad);
        $raceimage = $this->database->EscapeString($raceimage);
        $startbuff = 0;
        $statssincestart = 0;
        if(!$donor)
            $donor = '';
        else
            $donor = 76;
		
		$year = 2025;
		$month = 1;
		$day = 19;
		$hour = 12;
		$minute = 0;
		$second = 0;
		$time = mktime($hour, $minute, $second, $month, $day, $year);
		
        $now = new DateTime("now");
        $hourssinceopening = floor(($now->getTimestamp() - $time) / 3600);
        if ($hourssinceopening >= 1)
            $statssincestart = $hourssinceopening * 6 + $startbuff;

        if ($chara == '')
        {
            return 2;
        }

        $result = $this->database->Select('name', 'accounts', 'name = "' . $chara . '"', 1);
        if ($result)
        {
            $exist = $result->num_rows > 0;
            $result->close();
            if ($exist)
            {
                return 1;
            }
        }

        $PfadItemID = 0;
        $menge = 0;
        if ($pfad == 'Logia')
        {
            $PfadItemID = 54;
            $menge = 100;
        }
        else if ($pfad == 'Paramecia')
        {
            $PfadItemID = 53;
            $menge = 100;
        }
        else if ($pfad == 'Zoan')
        {
            $PfadItemID = 52;
            $menge = 100;
        }
        else if ($pfad == 'Schwertkaempfer')
        {
            $PfadItemID = 56;
            $menge = 70;
        }
        else if ($pfad == 'Schwarzfuss')
        {
            $PfadItemID = 57;
            $menge = 70;
        }
        else if ($pfad == 'Karatekämpfer')
        {
            $PfadItemID = 55;
            $menge = 70;
        }
        $place = 1; // Windmühlendorf
        $planet = 1; // East Blue
        $attacks = '1;2;4';
        $fightAttacks = $attacks;
        $berry = 2000;
        $stats = 10;
        $skillpunkte = 1;
        $this->database->Insert(
            'name,userid,race, pfad, pfad2, attacks, zeni, stats, lp, mlp, kp, mkp, attack, defense,accuracy,reflex, skillpoints, fightattacks, place, planet, raceimage, created, lastaction, titels, discordid, ismulti, chatactive, activeUserID',
            '"' . $chara . '","' . $mainID . '","' . $rasse . '", "None", "None", "' . $attacks . '","' . $berry . '"
			,"' . ($statssincestart) . '","' . ($stats * 10) . '","' . ($stats * 10) . '","' . ($stats * 10) . '","' . ($stats * 10) . '","' . ($stats * 2) . '","' . $stats . '","' . ($stats * 10) . '","' . ($stats * 10) . '"
			,"' . $skillpunkte . '" ,"' . $fightAttacks . '","' . $place . '","' . $planet . '","' . $raceimage . '",CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), "'.$donor.'", "'.$discordID.'", "'.$IsMulti.'", 0, "'.$mainID.'"',
            'accounts'
        );

        $id = $this->database->GetLastID();
        $this->database->Insert('statsid,visualid,ownerid,amount', '"' . $PfadItemID . '", "' . $PfadItemID . '", "' . $id . '", "' . $menge . '"', 'inventory');


        // ---- Starthilfe ---- //
        $this->database->Insert('statsid, visualid, ownerid, amount', '49, 49, ' . $id . ', 1', 'inventory');
        $this->database->Insert('statsid, visualid, ownerid, amount', '50, 50, ' . $id . ', 1', 'inventory');
        $this->database->Insert('statsid, visualid, ownerid, amount', '51, 51, ' . $id . ', 1', 'inventory');
        $this->database->Insert('statsid, visualid, ownerid, amount', '8, 8, ' . $id . ', 5', 'inventory');
        $this->database->Insert('statsid, visualid, ownerid, amount', '1, 1, ' . $id . ', 1', 'inventory');
        // -------------------- //

        $result = $this->database->Select('COUNT(id) as total', 'accounts', '');
        $rank = 0;
        if ($result)
        {
            $row = $result->fetch_assoc();
            $rank = $row['total'];
            $result->close();
        }
        $this->SetRank($rank);

        $this->database->Update('rank="' . $rank . '"', 'accounts', 'id = ' . $id . '', 1);

        $text = 'Charaktererstellung - Name: <a href="?p=profil&id=' . $id . '">' . $chara . '</a> ID: ' . $id . ' E-Mail: ' . $email;
        $this->Track($text, $id, 'System', 1);
        return 0;
    }

    public function ChangeFakeKI($ki)
    {
        $this->SetFakeKI($ki);
    }

    public function ChangeDesign($design)
    {
        $design = $this->database->EscapeString($design);
        $this->SetDesign($design);
        $set = 'design="' . $design . '"';
        $this->database->Update($set, 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function ChangeProfileBG($profilebg)
    {
        $profilebg = $this->database->EscapeString($profilebg);
        $this->SetProfileBG($profilebg);
        $set = 'profilebg="' . $profilebg . '"';
        $this->database->Update($set, 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function ChangeBackground($background)
    {
        $background = $this->database->EscapeString($background);
        $this->SetBackground($background);
        $set = 'background="' . $background . '"';
        $this->database->Update($set, 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function ChangeHeader($header)
    {
        $header = $this->database->EscapeString($header);
        $this->SetHeader($header);
        $set = 'header="' . $header . '"';
        $this->database->Update($set, 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function ChangePvPPic($pvppic)
    {
        //$pvppic = $this->database->EscapeString($pvppic);
        $image = $this->database->EscapeString($pvppic);
        if ($image == '') $image = $this->GetPvPImage();
        $this->SetPvPImage($image);
        $set = 'kopfgeldimage="' . $image . '"';
        $this->database->Update($set, 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function ChangeTitel($titel, $number)
    {
        $titel = $this->database->EscapeString($titel);
        if ($number == 0)
        {
            $this->SetTitel($titel);
            $this->SetRankOne($titel);
            $set = 'rankone="' . $titel . '", titel="' . $titel . '"';
        }
        else if ($number == 1)
        {
            $this->SetRankTwo($titel);
            $set = 'ranktwo="' . $titel . '"';
        }
        else if ($number == 2)
        {
            $this->SetRankThree($titel);
            $set = 'rankthree="' . $titel . '"';
        }
        $this->database->Update($set, 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function ChangeProfile($text, $image, $chatactivate, $onlineactive)
    {
        $formatedText = $this->database->EscapeString($text);
        $image = $this->database->EscapeString($image);
        $onlineactive = $this->database->EscapeString($onlineactive);
        if ($image == '') $image = 'img/imagefail.png';
        $chatActive = 0;
        if ($chatactivate == 1)
        {
            $chatActive = 1;
        }
        $this->data['onlinestatus'] = $onlineactive;
        $set = 'text="' . $formatedText . '", charimage="' . $image . '", chatactive="' . $chatActive . '", onlinestatus="' . $onlineactive . '"';
        $this->database->Update($set, 'accounts', 'id = ' . $this->data['id'], 1);
        $this->SetText($text);
        $this->SetImage($image);
        $this->SetChatActive($chatActive);
    }

    public function UpdateFight($id)
    {
        $this->database->Update('fight="' . $id . '"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
        $this->SetFight($id);
    }

    public function CloseStatsPopup()
    {
        $this->database->Update('statspopup="0"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
        $this->SetStatsPopup(false);
    }

    public function ResetHealth()
    {
        $this->database->Update('lp = "' . $this->GetMaxLP() . '",kp = "' . $this->GetMaxKP() . '"', 'accounts', 'id = ' . $this->data['id'], 1);
        $this->SetLP($this->GetMaxLP());
        $this->SetKP($this->GetMaxKP());
    }

    public function IncreaseStats($lp, $kp, $attack, $defense)
    {
        $this->AddDebugLog('IncreaseStats');

        $nlp = $this->GetLP() + ($lp * 10);
        $this->SetLP($nlp);
        $nmlp = $this->GetMaxLP() + ($lp * 10);
        $this->AddDebugLog(' - LP from ' . number_format($this->GetMaxLP(), '0', '', '.') . ' to ' . number_format($nmlp, '0', '', '.'));
        $this->SetMaxLP($nmlp);

        $nkp = $this->GetKP() + ($kp * 10);
        $this->SetKP($nkp);
        $nmkp = $this->GetMaxKP() + ($kp * 10);
        $this->AddDebugLog(' - AD from ' . number_format($this->GetMaxKP(), '0', '', '.') . ' to ' . number_format($nmkp, '0', '', '.'));
        $this->SetMaxKP($nmkp);

        $nattack = $this->GetAttack() + ($attack * 2);
        //$nattack = $this->GetAttack() + $attack; TODO: Reupload
        $this->AddDebugLog(' - ATK from ' . number_format($this->GetAttack(), '0', '', '.') . ' to ' . number_format($nattack, '0', '', '.'));
        $this->SetAttack($nattack);

        $ndefense = $this->GetDefense() + $defense;
        $this->AddDebugLog(' - DEF from ' . number_format($this->GetDefense(), '0', '', '.') . ' to ' . number_format($ndefense, '0', '', '.'));
        $this->SetDefense($ndefense);

        $stats = $this->GetStats();
        $stats = $stats - $lp;
        $stats = $stats - $kp;
        $stats = $stats - $attack;
        $stats = $stats - $defense;
        $this->AddDebugLog(' - Stats from ' . number_format($this->GetStats(), '0', '', '.') . ' to ' . number_format($stats, '0', '', '.'));
        $this->SetStats($stats);
        $this->SetStatsPopup(false);

        $this->AddDebugLog(' ');

        if ($this->HasStatsResetted() == 0 || $this->HasStatsResetted() == 1 && $this->GetResettedStatsAmount() == 0)
        {
            $statslimit = $this->GetAssignedStats() + ($lp + $kp + $attack + $defense);
            $update = 'assignedstats="' . $statslimit . '"';
        }
        else if ($this->HasStatsResetted() == 1 && $this->GetResettedStatsAmount() > 0)
        {
            $statslimit = ($lp + $kp + $attack + $defense);
            $update = 'resetamount="' . ($this->GetResettedStatsAmount() - $statslimit) . '"';
            if (($this->GetResettedStatsAmount() - $statslimit) <= 0)
                $update = $update . ', statsresetted="0"';
        }

        $update = $update . ', statspopup="0"';
        $update = $update . ', stats="' . $stats . '"';
        $update = $update . ', lp="' . $nlp . '"';
        $update = $update . ', mlp="' . $nmlp . '"';
        $update = $update . ', kp="' . $nkp . '"';
        $update = $update . ', mkp="' . $nmkp . '"';
        $update = $update . ', attack="' . $nattack . '"';
        $update = $update . ', defense="' . $ndefense . '"';
        $this->database->Update($update, 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function HasAttack($value)
    {
        $attacks = explode(";", $this->GetAttacks());
        if(in_array($value, $attacks)) return true;
        return false;
    }

    public function EndAction()
    {
        $this->SetActionStart(0);
        $this->CalculateAction();
        $this->CalculateTravelAction();
    }

    public function EndTravelAction()
    {
        $this->SetTravelActionStart(0);
        $this->CalculateTravelAction();
    }

    public function GetVivrecard()
    {

        return $this->data['vivrecard'];
    }

    public function SetVivrecard($value)
    {
        $this->database->Update('vivrecard="'.$value.'"', 'accounts', 'id="'.$this->GetID().'"');
    }

    public function CancelAction($force = false, $cancelTravel = false)
    {
        if ($this->GetTravelAction() != 0 && $cancelTravel == true)
            $this->CalculateTravelAction(true, $force);

        $trainingAction = 1; // Allgemeines Training
        $rehabilitationsAction = 8; // Rehabilitations Training
        if ($force && ($this->GetAction() == $trainingAction || $this->GetAction() == $rehabilitationsAction))
            return;

        if ($this->GetAction() != 0)
            $this->CalculateAction(true, $force);
    }

    public function RefreshAction()
    {
        $this->AddDebugLog("");
        $this->AddDebugLog("Refresh Action");
        $action = $this->actionManager->GetAction($this->GetAction());

        $actionTimes = $this->GetActionTime();
        $newMinutes = ceil($this->GetActionCountdown() / 60);
        $elapsedMinutes = $actionTimes - $newMinutes;
        $times = floor($elapsedMinutes / $action->GetMinutes());
        $statpoints = $this->GetStats() + $times * $action->GetStats();

        $currentActionStartDateTime = date_create_from_format("Y-m-d H:i:s", $this->GetActionStart());
        $dateIntervalToRemove = new DateInterval("PT" . $times . "H");
        $newStart = date_format(date_add($currentActionStartDateTime, $dateIntervalToRemove), "Y-m-d H:i:s");

        $newMinutes = $actionTimes - ($times * 60);

        $this->SetActionTime($newMinutes);
        $this->SetActionStart($newStart);
        $this->SetStats($statpoints);
        $this->SetStatsPopup(1);
        $this->AddDebugLog("New Minutes: " . $newMinutes . "<br/>New Start: " . $newStart);
        $this->database->Update('stats="' . $statpoints . '", actionstart="' . $newStart . '",actiontime="' . $newMinutes . '", debuglog="'.$this->GetDebugLog().'"', 'accounts', 'id = ' . $this->GetID() . '', 1);

        if($action->GetID() == 9)
        {
            $itementry = array(316 => 0, 315=>0, 103=>0, 34=>0, 71=>0, 319=>0, 60=>0, 102=>0, 31=>0, 355=>0);
            $PMManager = new PMManager($this->database, $this->GetID());
            for ($it = 0; $it < $times; ++$it)
            {
                $rand = rand(1, 100);
                if($this->GetRace() == "Pirat") { $luck = rand(1, 100); } else { $luck = 26; }
                if ($rand == 1 || $luck == 1) // Seltener Splitter
                {
                    $itementry[316] += 1;
                }
                if ($rand > 1 && $rand <= 4 || $luck > 1 && ($luck < 3)) // Einfacher Splitter
                {
                    $itementry[315] += 1;
                }
                if ($rand > 4 && $rand <= 9 || $luck > 2 && ($luck < 7)) // Geldsack
                {
                    $itementry[103] += 1;
                }
                if ($rand > 9 && $rand <= 14 || $luck > 6 && ($luck < 10)) // Starke Medizin
                {
                    $itementry[34] += 1;
                }
                if ($rand > 14 && $rand <= 24 || $luck > 9 && ($luck < 11)) // Sehr starke Medizin
                {
                    $itementry[8] += 1;
                }
                if ($rand > 24 && $rand <= 37 || $luck > 10 && ($luck < 19)) // Waffen Truhe
                {
                    $itementry[319] += 1;
                }
                if ($rand > 37 && $rand <= 52 || $luck > 18 && ($luck < 21)) // Schatztruhe
                {
                    $itementry[60] += 1;
                }
                if ($rand > 52 && $rand <= 70 || $luck > 20 && ($luck < 23)) // Regenbogen Truhe
                {
                    $itementry[102] += 1;
                }
                if ($rand > 70 && $rand <= 80 || $luck > 22 && ($luck < 25)) // Schwache Medizin
                {
                    $itementry[31] += 1;
                }
                if ($rand > 80 && $rand <= 100 || $luck > 24 && ($luck < 26)) // Material Truhe
                {
                    $itementry[355] += 1;
                }
            }
            if($itementry[316] > 0 || $itementry[315] > 0 || $itementry[103] > 0 || $itementry[34] > 0 || $itementry[71] > 0 || $itementry[319] > 0 || $itementry[60] > 0 || $itementry[102] > 0 || $itementry[31] > 0 || $itementry[355] > 0)
            {
                $itemManager = new itemManager($this->database);
                $pm = "<p style='text-align:center'>Glückwunsch <b>" . $this->GetName() . "</b>,<br/>du hast auf deiner Schatzsuche einiges gefunden:</p> <table>";
                if($itementry[316] > 0)
                {
                    $this->AddItems($itemManager->GetItem(316), $itemManager->GetItem(316), $itementry[316]);
                    $item = $itemManager->GetItem(316);
                    $pm .= "<tr><td align='right'><img src='img/items/" . $item->GetImage() . ".png' alt='".$item->GetName()."' title='".$item->GetName()."' width='80px' height='80px'/></td>
					<td>". number_format($itementry[316], 0, ',', '.')."x " . $item->GetName() . "</td></tr>";
                }
                if($itementry[102] > 0)
                {
                    $this->AddItems($itemManager->GetItem(102), $itemManager->GetItem(102), $itementry[102]);
                    $item = $itemManager->GetItem(102);
                    $pm .= "<tr><td align='right'><img src='img/items/" . $item->GetImage() . ".png' alt='".$item->GetName()."' title='".$item->GetName()."' width='80px' height='80px'/></td>
					<td>". number_format($itementry[102], 0, ',', '.')."x " . $item->GetName() . "</td></tr>";
                }
                if($itementry[315] > 0)
                {
                    $this->AddItems($itemManager->GetItem(315), $itemManager->GetItem(315), $itementry[315]);
                    $item = $itemManager->GetItem(315);
                    $pm .= "<tr><td align='right'><img src='img/items/" . $item->GetImage() . ".png' alt='".$item->GetName()."' title='".$item->GetName()."' width='80px' height='80px'/></td>
					<td>". number_format($itementry[315], 0, ',', '.')."x " . $item->GetName() . "</td></tr>";
                }
                if($itementry[103] > 0)
                {
                    $this->AddItems($itemManager->GetItem(103), $itemManager->GetItem(103), $itementry[103]);
                    $item = $itemManager->GetItem(103);
                    $pm .= "<tr><td align='right'><img src='img/items/" . $item->GetImage() . ".png' alt='".$item->GetName()."' title='".$item->GetName()."' width='80px' height='80px'/></td>
					<td>". number_format($itementry[103], 0, ',', '.')."x " . $item->GetName() . "</td></tr>";
                }
                if($itementry[34] > 0)
                {
                    $this->AddItems($itemManager->GetItem(34), $itemManager->GetItem(34), $itementry[34]);
                    $item = $itemManager->GetItem(34);
                    $pm .= "<tr><td align='right'><img src='img/items/" . $item->GetImage() . ".png' alt='".$item->GetName()."' title='".$item->GetName()."' width='80px' height='80px'/></td>
					<td>". number_format($itementry[34], 0, ',', '.')."x " . $item->GetName() . "</td></tr>";
                }
                if($itementry[71] > 0)
                {
                    $this->AddItems($itemManager->GetItem(71), $itemManager->GetItem(71), $itementry[71]);
                    $item = $itemManager->GetItem(71);
                    $pm .= "<tr><td align='right'><img src='img/items/" . $item->GetImage() . ".png' alt='".$item->GetName()."' title='".$item->GetName()."' width='80px' height='80px'/></td>
					<td>". number_format($itementry[71], 0, ',', '.')."x " . $item->GetName() . "</td></tr>";
                }
                if($itementry[319] > 0)
                {
                    $this->AddItems($itemManager->GetItem(319), $itemManager->GetItem(319), $itementry[319]);
                    $item = $itemManager->GetItem(319);
                    $pm .= "<tr><td align='right'><img src='img/items/" . $item->GetImage() . ".png' alt='".$item->GetName()."' title='".$item->GetName()."' width='80px' height='80px'/></td>
					<td>". number_format($itementry[319], 0, ',', '.')."x " . $item->GetName() . "</td></tr>";
                }
                if($itementry[60] > 0)
                {
                    $this->AddItems($itemManager->GetItem(60), $itemManager->GetItem(60), $itementry[60]);
                    $item = $itemManager->GetItem(60);
                    $pm .= "<tr><td align='right'><img src='img/items/" . $item->GetImage() . ".png' alt='".$item->GetName()."' title='".$item->GetName()."' width='80px' height='80px'/></td>
					<td>". number_format($itementry[60], 0, ',', '.')."x " . $item->GetName() . "</td></tr>";
                }
                if($itementry[31] > 0)
                {
                    $this->AddItems($itemManager->GetItem(31), $itemManager->GetItem(31), $itementry[31]);
                    $item = $itemManager->GetItem(31);
                    $pm .= "<tr><td align='right'><img src='img/items/" . $item->GetImage() . ".png' alt='".$item->GetName()."' title='".$item->GetName()."' width='80px' height='80px'/></td>
					<td>". number_format($itementry[31], 0, ',', '.')."x " . $item->GetName() . "</td></tr>";
                }
                if($itementry[355] > 0)
                {
                    $this->AddItems($itemManager->GetItem(355), $itemManager->GetItem(355), $itementry[355]);
                    $item = $itemManager->GetItem(355);
                    $pm .= "<tr><td align='right'><img src='img/items/" . $item->GetImage() . ".png' alt='".$item->GetName()."' title='".$item->GetName()."' width='80px' height='80px'/></td>
					<td>". number_format($itementry[355], 0, ',', '.')."x " . $item->GetName() . "</td></tr>";
                }
            }
            $pm = "<center>".$pm."</table></center>";
            $PMManager->SendPM(0, 'img/system.png', 'SYSTEM', 'Siehe Da', $pm, $this->GetName(), 1);
        }
    }

    public function RefreshAction2()
    {
        $action = $this->actionManager->GetAction($this->GetAction());

        $actionTimes = $this->GetActionTime();
        $newMinutes = ceil($this->GetActionCountdown() / 60);
        $elapsedMinutes = $actionTimes - $newMinutes;
        $times = floor($elapsedMinutes / $action->GetMinutes());

        $currentActionStartDateTime = date_create_from_format("Y-m-d H:i:s", $this->GetActionStart());
        $dateIntervalToRemove = new DateInterval("PT" . $times . "H");
        $newStart = date_format(date_add($currentActionStartDateTime, $dateIntervalToRemove), "Y-m-d H:i:s");

        $newMinutes = $actionTimes - ($times * 60);

        $this->SetActionTime($newMinutes);
        $this->SetActionStart($newStart);
        $this->AddDebugLog("New Minutes: " . $newMinutes . "<br/>New Start: " . $newStart);
    }

    public function CancelTravelAction()
    {
        $this->CalculateTravelAction(true, false);
    }

    public function Learn($action, $minutes, $attack, $skillpoints = 0)
    {
        $newPoints = $this->GetSkillPoints() - $skillpoints;
        $newPointLearn = $this->GetSkillPointLearn() + $skillpoints;
        $this->DoAction($action, $minutes, ',learningattack="' . $attack . '",skillpointlearn="' . $newPointLearn . '",skillpoints="' . $newPoints . '"');
        $this->SetLearningAttack($attack);
        $this->SetSkillPoints($newPoints);
        $this->SetSkillPointLearn($newPointLearn);
    }

    public function TravelPlanet($planet, $minutes, $action)
    {
        $update = '';
        if($planet != 0)
            $update = ',travelplanet="' . $planet . '"';
        $this->DoTravelAction($action, $minutes, $update);
        $this->SetTravelPlanet($planet);
    }

    public function Teleport($planet, $place, $cost)
    {
        $newKP = $this->GetKP() - $cost;
        $update = 'planet="' . $planet . '", place = "' . $place . '", kp = ' . $newKP;
        $this->SetKP($newKP);
        $this->SetPlanet($planet);
        $this->SetPlace($place);
        $this->database->Update($update, 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function CopyStats($otherPlayer)
    {
        $this->SetMaxLP($otherPlayer->GetMaxLP());
        $this->SetMaxKP($otherPlayer->GetMaxKP());
        $this->SetLP($otherPlayer->GetMaxLP());
        $this->SetKP($otherPlayer->GetMaxKP());
        $this->SetAttack($otherPlayer->GetAttack());
        $this->SetDefense($otherPlayer->GetDefense());
        $this->database->Update('lp=' . $this->GetMaxLP() . ', mlp=' . $this->GetMaxLP() . ', kp=' . $this->GetMaxKP() . ', mkp=' . $this->GetMaxKP() . ', attack=' . $this->GetAttack() . ', defense=' . $this->GetDefense(), 'accounts', 'id=' . $this->GetID());
    }

    public function CopyEquip($otherPlayer)
    {
        $itemManager = new ItemManager($this->database);
        $this->SetEquippedStats($otherPlayer->GetEquippedStats());

        $result = $this->database->Select('id, statsid, visualid', 'inventory', 'ownerid=' . $this->GetID() . ' AND equipped=1');
        if ($result)
        {
            while ($row = $result->fetch_assoc())
            {
                $item = $itemManager->GetItem($row['statsid']);
                if ($item->GetSlot() >= 1) {
                    if($row['statsid'] != $row['visualid'])
                        $visual[$item->GetSlot()] = $row['visualid'];
                    $this->database->Delete('inventory', 'id=' . $row['id']);
                }
            }
        }
        $result->close();

        $result2 = $this->database->Select('id, statsid, visualid, upgrade', 'inventory', 'ownerid=' . $otherPlayer->GetID() . ' AND equipped=1');
        if ($result2)
        {
            while ($row = $result2->fetch_assoc()) {
                $item = $itemManager->GetItem($row['statsid']);
                if ($item->GetSlot() >= 1) {
                    if(!isset($visual[$item->GetSlot()]))
                        $visual[$item->GetSlot()] = $row['visualid'];
                    $this->database->Insert('statsid, visualid, upgrade, equipped, ownerid, amount', $row['statsid'] . ', ' . $visual[$item->GetSlot()] . ', ' . $row['upgrade'] . ', 1, ' . $this->GetID() . ', 1', 'inventory');

                }
            }
        }
        $result2->close();
        $update ='equippedstats="' . $this->GetEquippedStats() . '"';
        $this->database->Update($update, 'accounts', 'id=' . $this->GetID());
    }

    public function CopyAll($otherPlayer)
    {
        $itemManager = new ItemManager($this->database);
        $this->SetMaxLP($otherPlayer->GetMaxLP());
        $this->SetMaxKP($otherPlayer->GetMaxKP());
        $this->SetLP($otherPlayer->GetMaxLP());
        $this->SetKP($otherPlayer->GetMaxKP());
        $this->SetAttack($otherPlayer->GetAttack());
        $this->SetDefense($otherPlayer->GetDefense());
        $this->SetAttacks($otherPlayer->GetAttacks());
        $this->SetFightAttacks($otherPlayer->GetFightAttacks());
        $this->SetStory($otherPlayer->GetStory());
        $this->SetSideStory($otherPlayer->GetSideStory());
        $this->SetLevel($otherPlayer->GetLevel());
        $this->SetStartingPowerup($otherPlayer->GetStartingPowerup());
        $this->SetEquippedStats($otherPlayer->GetEquippedStats());
        $visual = array();

        $result = $this->database->Select('id, statsid, visualid', 'inventory', 'ownerid=' . $this->GetID() . ' AND equipped=1');
        if ($result)
        {
            while ($row = $result->fetch_assoc())
            {
                $item = $itemManager->GetItem($row['statsid']);
                if ($item->GetSlot() >= 1) {
                    if($row['statsid'] != $row['visualid'])
                        $visual[$item->GetSlot()] = $row['visualid'];
                    $this->database->Delete('inventory', 'id=' . $row['id']);
                }
            }
        }
        $result->close();

        $result2 = $this->database->Select('id, statsid, visualid, upgrade', 'inventory', 'ownerid=' . $otherPlayer->GetID() . ' AND equipped=1');
        if ($result2)
        {
            while ($row = $result2->fetch_assoc()) {
                $item = $itemManager->GetItem($row['statsid']);
                if ($item->GetSlot() >= 1) {
                    if(!isset($visual[$item->GetSlot()]))
                        $visual[$item->GetSlot()] = $row['visualid'];
                    $this->database->Insert('statsid, visualid, upgrade, equipped, ownerid, amount', $row['statsid'] . ', ' . $visual[$item->GetSlot()] . ', ' . $row['upgrade'] . ', 1, ' . $this->GetID() . ', 1', 'inventory');

                }
            }
        }
        $result2->close();
        $update =
            'lp="' . $this->GetMaxLP() .
            '", mlp="' . $this->GetMaxLP() .
            '", kp="' . $this->GetMaxKP() .
            '", mkp="' . $this->GetMaxKP() .
            '", attack="' . $this->GetAttack() .
            '", defense="' . $this->GetDefense() .
            '", attacks="' . $this->GetAttacks() .
            '", fightattacks="' . $this->GetFightAttacks() .
            '", level="' . $this->GetLevel() .
            '", story="' . $this->GetStory() .
            '", sidestory="' . $this->GetSideStory() .
            '", pfad="' . $otherPlayer->GetPfad(1) .
            '", pfad2="' . $otherPlayer->GetPfad(2) .
            '", powerupstart="' . $this->GetStartingPowerup() .
            '", equippedstats="' . $this->GetEquippedStats() .
            '", titelstats="' . $otherPlayer->GetTitelStats() . '"';
        $this->database->Update($update, 'accounts', 'id=' . $this->GetID());
    }

    public function Travel($place, $minutes, $action, $x, $y, $setAufReise = true)
    {
        if ($action->GetID() != 2 && $action->GetID() != 26)
        {
            $previousPlace = $this->GetPlace();
            $newPlace = $this->GetPlace();
            if ($setAufReise)
            {
                $newPlace = -1;
            }
            $update = ',x="' . $x . '",y="' . $y . '",previousplace="' . $previousPlace . '", place="' . $newPlace . '", travelplace="' . $place . '", travelplanet="' . $this->GetPlanet() . '"';
            $this->DoAction($action, $minutes, $update);
            $this->SetTravelPlanet($this->GetPlanet());
            $this->SetTravelPlace($place);
            $this->SetPreviousPlace($previousPlace);
            $this->SetPlace($newPlace);
        }
        else
        {
            $previousPlace = $this->GetPlace();
            $newPlace = $this->GetPlace();
            if ($setAufReise)
            {
                $newPlace = -1;
            }
            $update = ',x="' . $x . '",y="' . $y . '",previousplace="' . $previousPlace . '", place="' . $newPlace . '", travelplace="' . $place . '"';
            $this->DoTravelAction($action, $minutes, $update);
            $this->SetTravelPlace($place);
            $this->SetPreviousPlace($previousPlace);
            $this->SetPlace($newPlace);
        }
    }

    public function DoTravelAction($action, $minutes, $updateArgs = '')
    {
        $timestamp = date("Y-m-d H:i:s");

        $hours = $minutes / 60;
        $price = round($action->GetPrice() * $hours);
        $berry = $this->GetBerry() - $price;
        $this->SetBerry($berry);

        $this->SetTravelAction($action->GetID());

        $this->SetTravelActionTime($minutes);
        $this->SetTravelActionStart($timestamp);

        $this->database->Update('zeni="' . $berry . '",travelaction="' . $action->GetID() . '",travelactionstart="' . $timestamp . '",travelactiontime="' . $minutes . '"' . $updateArgs, 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function DoAction($action, $minutes, $updateArgs = '')
    {
        $timestamp = date("Y-m-d H:i:s");

        $hours = $minutes / 60;
        $price = round($action->GetPrice() * $hours);
        $berry = $this->GetBerry() - $price;
        $this->SetBerry($berry);

        if ($action->GetItems() != "")
        {
            $items = explode(";", $action->GetItems());
            foreach($items as &$itemDataArray)
            {
                $itemData = explode('@',$itemDataArray);
                $itemID = $itemData[0];
                $itemAmount = $itemData[1];
                $statstype = 0;
                $upgrade = 0;
                $this->RemoveItemsByID($itemID, $itemID, $statstype, $upgrade, $itemAmount * $hours);
            }
        }

        $this->SetAction($action->GetID());

        $this->SetActionTime($minutes);
        $this->SetActionStart($timestamp);

        $this->database->Update('zeni="' . $berry . '",action="' . $action->GetID() . '",actionstart="' . $timestamp . '",actiontime="' . $minutes . '"' . $updateArgs, 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function UpdateDailyNPCFights()
    {
        $this->database->Update('dailynpcfights="' . $this->GetDailyNPCFights() . '"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function AddStats($stats)
    {
        if ($this->GetStats() == 0)
        {
            $this->SetStatsPopup(true);
        }
        $nStats = $this->GetStats() + $stats;

        $this->AddDebugLog('AddStats');
        $this->AddDebugLog(' - From ' . number_format($this->GetStats(), '0', '', '.') . ' to ' . number_format($nStats, '0', '', '.'));
        $this->AddDebugLog(' ');

        $this->SetStats($nStats);
        $this->database->Update('statspopup="' . $this->GetStatsPopup() . '",stats="' . $this->GetStats() . '"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function AddStats2($stats)
    {
        $nStats = $this->GetStats() + $stats;

        $this->AddDebugLog('AddStats');
        $this->AddDebugLog(' - From ' . number_format($this->GetStats(), '0', '', '.') . ' to ' . number_format($nStats, '0', '', '.'));
        $this->AddDebugLog(' ');

        $this->SetStats($nStats);
        $this->database->Update('statspopup="' . $this->GetStatsPopup() . '",stats="' . $this->GetStats() . '"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function JumpStory($story)
    {
        $this->SetStory($story->GetID());
        $this->SetPlace($story->GetPlace());
        $this->SetPlanet($story->GetPlanet());
        $result = $this->database->Select('SUM(levelup) AS Levels', 'story', 'id < ' . $this->GetStory());
        if ($result)
        {
            if ($row = $result->fetch_assoc())
            {
                $this->SetLevel($row['Levels'] + 1);
            }
        }
        $result->close();
        $this->database->Update('story="' . $this->GetStory() . '", level="' . $this->GetLevel() . '", place="' . $this->GetPlace() . '", planet="' . $this->GetPlanet() . '"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function JumpSideStory($story)
    {
        $this->SetSideStory($story);
        $this->database->Update('sidestory="' . $this->GetSideStory() . '"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function MapJump($place, $planet, $x, $y)
    {
        $this->SetPlace($place);
        $this->SetPlanet($planet);
        $this->SetX($x);
        $this->SetY($y);
        $this->database->Update('place="' . $place . '",planet="' . $planet . '",x="' . $x . '",y="' . $y . '"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function OceanJump($planet, $place, $x, $y)
    {
        $this->SetPlanet($planet);
        $this->SetPlace($place);
        $this->SetX($x);
        $this->SetY($y);
        $this->database->Update('place="' . $place . '",planet="' . $planet . '",x="' . $x . '",y="' . $y . '"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function ContinueStory($levelup, $berry, $gold, $items, $skillpoints, $pvp, $itemManager)
    {
        $this->AddDebugLog('ContinueStory');

        $nStory = $this->GetStory() + 1;

        $this->AddDebugLog(' - Story From ' . number_format($this->GetStory(), '0', '', '.') . ' to ' . number_format($nStory, '0', '', '.'));

        $this->SetStory($nStory);
        if ($levelup)
        {
            $nLevel = $this->GetLevel() + 1;
            $this->AddDebugLog(' - Level From ' . number_format($this->GetLevel(), '0', '', '.') . ' to ' . number_format($nLevel, '0', '', '.'));
            $this->SetLevel($nLevel);
            if ($this->GetStats() == 0)
            {
                $this->SetStatsPopup(true);
            }
            $nStats = $this->GetStats() + 25;
            $this->AddDebugLog(' - Stats From ' . number_format($this->GetStats(), '0', '', '.') . ' to ' . number_format($nStats, '0', '', '.'));
            $this->SetStats($nStats);
        }
        if ($pvp != 0)
        {
            $nPvP = $this->GetPvP() + $pvp;
            $this->AddDebugLog(' - Kopfgeld From ' . number_format($this->GetPvP(), '0', '', '.') . ' to ' . number_format($nPvP, '0', '', '.'));
            $this->SetPvP($nPvP);
        }
        if ($berry != 0)
        {
            $nBerry = $this->GetBerry() + $berry;
            $this->AddDebugLog(' - Berry From ' . number_format($this->GetBerry(), '0', '', '.') . ' to ' . number_format($nBerry, '0', '', '.'));
            $this->SetBerry($nBerry);
        }
        if ($gold != 0)
        {
            $nGold = $this->GetGold() + $gold;
            $this->AddDebugLog(' - Gold From ' . number_format($this->GetGold(), '0', '', '.') . ' to ' . number_format($nGold, '0', '', '.'));
            $this->SetGold($nGold);
        }
        if ($skillpoints != 0)
        {
            $nSkillPoints = $this->GetSkillPoints() + $skillpoints;
            $this->AddDebugLog(' - SkillPoints From ' . number_format($this->GetSkillPoints(), '0', '', '.') . ' to ' . number_format($nSkillPoints, '0', '', '.'));
            $this->SetSkillPoints($nSkillPoints);
        }

        if (isset($items) && count($items) > 0)
        {
            foreach ($items as &$item)
            {
                $itemData = explode('@', $item);
                $itemObject = $itemManager->GetItem($itemData[0]);
                $this->AddItems($itemObject, $itemObject, $itemData[1]);
            }
        }
        $this->AddDebugLog(' ');
        $titelManager = new titelManager($this->database);
        $titelManager->AddTitelStory($this, $this->GetStory());
        $this->database->Update('skillpoints=skillpoints+' . $skillpoints . ',statspopup="' . $this->GetStatsPopup() . '",stats="' . $this->GetStats() . '",story="' . $this->GetStory() . '",level="' . $this->GetLevel() . '",zeni="' . $this->GetBerry() . '",kopfgeld="' . $this->GetPvP() . '"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function ContinueSideStory($levelup, $berry, $gold, $items, $skillpoints, $pvp, $itemManager)
    {
        $this->AddDebugLog('ContinueSideStory');

        $nSideStory = $this->GetSideStory() + 1;

        $this->AddDebugLog(' - Story From ' . number_format($this->GetSideStory(), '0', '', '.') . ' to ' . number_format($nSideStory, '0', '', '.'));

        $this->SetStory($nSideStory);
        if ($levelup)
        {
            $nLevel = $this->GetLevel() + 1;
            $this->AddDebugLog(' - Level From ' . number_format($this->GetLevel(), '0', '', '.') . ' to ' . number_format($nLevel, '0', '', '.'));
            $this->SetLevel($nLevel);
            if ($this->GetStats() == 0)
            {
                $this->SetStatsPopup(true);
            }
            $nStats = $this->GetStats() + 10;
            $this->AddDebugLog(' - Stats From ' . number_format($this->GetStats(), '0', '', '.') . ' to ' . number_format($nStats, '0', '', '.'));
            $this->SetStats($nStats);
        }
        if ($pvp != 0)
        {
            $nPvP = $this->GetPvP() + $pvp;
            $this->AddDebugLog(' - Kopfgeld From ' . number_format($this->GetPvP(), '0', '', '.') . ' to ' . number_format($nPvP, '0', '', '.'));
            $this->SetPvP($nPvP);
        }
        if ($berry != 0)
        {
            $nBerry = $this->GetBerry() + $berry;
            $this->AddDebugLog(' - Berry From ' . number_format($this->GetBerry(), '0', '', '.') . ' to ' . number_format($nBerry, '0', '', '.'));
            $this->SetBerry($nBerry);
        }
        if ($gold != 0)
        {
            $nGold = $this->GetGold() + $gold;
            $this->AddDebugLog(' - Gold From ' . number_format($this->GetGold(), '0', '', '.') . ' to ' . number_format($nGold, '0', '', '.'));
            $this->SetGold($nGold);
        }
        if ($skillpoints != 0)
        {
            $nSkillPoints = $this->GetSkillPoints() + $skillpoints;
            $this->AddDebugLog(' - SkillPoints From ' . number_format($this->GetSkillPoints(), '0', '', '.') . ' to ' . number_format($nSkillPoints, '0', '', '.'));
            $this->SetSkillPoints($nSkillPoints);
        }

        if (isset($items) && count($items) > 0)
        {
            foreach ($items as &$item)
            {
                $itemData = explode('@', $item);
                $itemObject = $itemManager->GetItem($itemData[0]);
                $this->AddItems($itemObject, $itemObject, $itemData[1]);
            }
        }
        $this->AddDebugLog(' ');
        $titelManager = new titelManager($this->database);
        $titelManager->AddTitelSideStory($this, $this->GetSideStory());
        $this->database->Update('skillpoints=skillpoints+' . $skillpoints . ',statspopup="' . $this->GetStatsPopup() . '",stats="' . $this->GetStats() . '",sidestory="' . $this->GetSideStory() . '",level="' . $this->GetLevel() . '",zeni="' . $this->GetBerry() . '",kopfgeld="' . $this->GetPvP() . '"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function JoinTournament($tid)
    {
        $this->SetPendingTournament($tid);
        $this->database->Update('pendingtournament="' . $tid . '"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }
    public function LeaveTournament()
    {
        $tid = 0;
        $this->SetPendingTournament($tid);
        $this->database->Update('pendingtournament="' . $tid . '"', 'accounts', 'id = ' . $this->data['id'] . '', 1);
    }

    public function GetExtraDungeon($id) : int
    {
        $dungeons = $this->GetExtraDungeons();
        $i = 0;
        while (isset($dungeons[$i]))
        {
            if ($dungeons[$i][0] == $id)
            {
                return $dungeons[$i][1];
            }
            ++$i;
        }
        return 0;
    }

    public function GetExtraDungeons() : array
    {
        if($this->data['extradungeons'] == '') return array();
        $dungeons = array();
        $dungeonsData = explode(';', $this->data['extradungeons']);
        $i = 0;
        while (isset($dungeonsData[$i]))
        {
            $dungeonData = explode('@', $dungeonsData[$i]);
            $dungeons[] = $dungeonData;
            ++$i;
        }
        return $dungeons;
    }

    public function AddExtraDungeon($event) : void
    {
        $dungeons = $this->GetExtraDungeons();
        $i = 0;
        $found = false;
        while (isset($dungeons[$i]))
        {
            if ($dungeons[$i][0] == $event)
            {
                $dungeons[$i][1] = $dungeons[$i][1] + 1;
                $found = true;
                break;
            }
            ++$i;
        }

        if (!$found)
        {
            $dungeon = array($event, 1);
            $dungeons[] = $dungeon;
        }

        $i = 0;
        $string = '';
        while (isset($dungeons[$i]))
        {
            if ($string == '')
            {
                $string = implode('@', $dungeons[$i]);
            }
            else
            {
                $string = $string . ';' . implode('@', $dungeons[$i]);
            }
            ++$i;
        }
        $this->data['extradungeons'] = $string;
        $this->database->Update('`extradungeons`="' . $string . '"', 'accounts', 'id=' . $this->data['id'], 1);
    }

    public function IsValid()
    {
        return $this->valid;
    }

    public function GetChatActive()
    {
        return $this->IsLogged() && $this->data['chatactive'] || isset($_POST['chatactive']);
    }

    public function GetOnlineStatus()
    {
        return $this->data['onlinestatus'];
    }

    public function SetChatActive($value)
    {
        $this->data['chatactive'] = $value;
        $result = $this->database->Update('chatactive="'.$value.'"', 'accounts', 'id="'.$this->GetID().'"');
    }

    public function GetID()
    {
        return $this->data['id'];
    }

    public function SetActiveUserID($userid): void
    {
        $this->data['activeUserID'] = $userid;
        $this->database->Update('activeUserID='.$userid, 'accounts', 'id='.$this->GetID());
    }

    public function GetActiveUserID(): int
    {
        return $this->data['activeUserID'];
    }

    public function GetName()
    {
        return $this->data['name'];
    }
    public function SetName($value)
    {
        $this->data['name'] = $value;
    }

    public function GetGroup()
    {
        if ($this->data['group'] == '')
        {
            return null;
        }
        return explode(';', $this->data['group']);
    }

    public function SetGroup($value)
    {
        $this->data['group'] = $value;
    }

    public function GetGroupInvite()
    {
        return $this->data['groupinvite'];
    }

    public function SetGroupInvite($value)
    {
        $this->data['groupinvite'] = $value;
    }

    public function GetTowerFloor()
    {
        return $this->data['towerfloor'];
    }

    public function GetNPCWonItems()
    {
        return $this->data['npcwonitems'];
    }

    public function SetNPCWonItems($value)
    {
        $this->data['npcwonitems'] = $value;
    }

    public function SetNPCWonItemsType($value)
    {
        $this->data['npcwonitemtype'] = $value;
    }

    public function SetNPCWonItemsDungeon($value)
    {
        $this->data['npcwonitemdungeon'] = $value;
    }

    public function GetNPCWonItemsType()
    {
        return $this->data['npcwonitemtype'];
    }

    public function GetDungeon()
    {
        return $this->data['dungeon'];
    }

    public function SetDungeon($value)
    {
        $this->data['dungeon'] = $value;
        $this->database->Update('dungeon="'.$value.'"', 'accounts', 'id="'.$this->data['id'].'"');
    }

    public function IsNPCWonItemsDungeon()
    {
        return $this->data['npcwonitemdungeon'];
    }
    public function GetPendingTournament()
    {
        return $this->data['pendingtournament'];
    }

    public function SetPendingTournament($value)
    {
        $this->data['pendingtournament'] = $value;
    }

    public function GetDeathTime()
    {
        return $this->data['deathtime'];
    }

    public function GetReviveTime()
    {
        $deathTime = strtotime($this->GetDeathTime());
        $timeDiffSeconds = time() - $deathTime;
        if ($this->GetRace() == 'Marine' || $this->GetLevel() < 5)
        {
            $neededSeconds = 7 * 24 * 60 * 60; // 7 Days
        }
        else
        {
            $neededSeconds = 7 * 24 * 60 * 60; // 7 Days
        }

        return $neededSeconds - $timeDiffSeconds;
    }

    public function Revive()
    {
        $deathPlanet = $this->GetDeathPlanet();
        $deathPlace = $this->GetDeathPlace();
        if ($deathPlanet == 2 || $deathPlanet == 0 || $deathPlace == 0)
        {
            $deathPlanet = 1; // East Blue
            $deathPlace = 1; // Windmühlendorf
        }
        $this->SetPlanet($deathPlanet);
        $this->SetPlace($deathPlace);
        $this->database->Update('planet="' . $deathPlanet . '", place="' . $deathPlace . '", impeldownpopup=0', 'accounts', 'id = ' . $this->GetID() . '', 1);
        if($this->GetReviveDays() > 0)
        {
            $this->SetReviveDays($this->GetReviveDays() - 1);
        }
        else
        {
            $this->SetReviveDays(2);
        }
    }

    public function GetTournament()
    {
        return $this->data['tournament'];
    }

    public function SetTournament($value)
    {
        $this->data['tournament'] = $value;
    }

    public function IsGroupLeader()
    {
        return $this->data['groupleader'];
    }

    public function SetGroupLeader($value)
    {
        $this->data['groupleader'] = $value;
    }

    public function GetStory()
    {
        return $this->data['story'];
    }

    public function GetEloPoints()
    {
        return $this->data['elopoints'];
    }

    public function GetLastEloRank()
    {
        return $this->data['last_elo_rank'];
    }

    public function SetLastEloRank()
    {
    $value = $this->data['rank'];
    $this->database->Update('last_elo_rank="'.$value.'"', 'accounts', 'id="'.$this->data['id'].'"');
    }

    public function AddEloPoints($value)
    {
        $this->data['elopoints'] += $value;
        $this->database->Update('elopoints="'.$this->data['elopoints'].'"', 'accounts', 'id='.$this->data['id']);
    }

    public function SetEloPoints($value)
    {
        $this->data['elopoints'] = $value;
        $this->database->Update('elopoints="'.$value.'"', 'accounts', 'id="'.$this->data['id'].'"');
    }

    public function GetDayliLogin()
    {
        return $this->data['daylilogin'];
    }

    public function GetSideStory()
    {
        return $this->data['sidestory'];
    }

    public function SetStory($value)
    {
        $this->data['story'] = $value;
    }

    public function SetSideStory($value)
    {
        $this->data['sidestory'] = $value;
    }

    public function GetEventInvite()
    {
        return $this->data['eventinvite'];
    }

    public function SetEventInvite($value)
    {
        $this->data['eventinvite'] = $value;
    }

    public function GetAction()
    {

        return $this->data['action'];
    }

    public function GetKolloWins()
    {
        return $this->data['kollowins'];
    }

    public function GetTravelAction()
    {
        return $this->data['travelaction'];
    }

    public function GetY()
    {
        return $this->data['y'];
    }

    public function SetY($value)
    {
        $this->data['y'] = $value;
    }

    public function GetX()
    {
        return $this->data['x'];
    }

    public function SetX($value)
    {
        $this->data['x'] = $value;
    }

    public function GetClanRank()
    {
        return $this->data['clanrank'];
    }

    public function GetTravelPlace()
    {
        return $this->data['travelplace'];
    }

    public function SetTravelPlace($value)
    {
        $this->data['travelplace'] = $value;
    }

    public function GetTravelPlanet()
    {
        return $this->data['travelplanet'];
    }

    public function SetTravelPlanet($value)
    {
        $this->data['travelplanet'] = $value;
    }

    public function GetLearningAttack()
    {
        return $this->data['learningattack'];
    }

    public function SetLearningAttack($value)
    {
        $this->data['learningattack'] = $value;
    }

    public function GetDeathPlace()
    {
        return $this->data['deathplace'];
    }

    public function SetDeathPlace($value)
    {
        $this->data['deathplace'] = $value;
    }

    public function GetDeathPlanet()
    {
        return $this->data['deathplanet'];
    }

    public function GetEloTrophaeen()
    {
        return $this->data['elotrophys'];
    }

    public function SetEloTrophäe($value)
    {
        $this->data['elotrophys'] = $value;
        if($this->data['elotrophys'] == "")
        {
            $this->database->Update('elotrophys="'.$value.'"', 'accounts', 'id="'.$this->GetID().'"');
        }
        else
        {
            $change = $this->data['elotrophys'].";".$value;
            $this->database->Update('elotrophys="'.$change.'"', 'accounts', 'id="'.$this->GetID().'"');
        }

    }

    public function SetDeathPlanet($value)
    {
        $this->data['deathplanet'] = $value;
    }

    public function GetStatsTraining()
    {
        if ($this->data['statstraining'] == '')
        {
            return '0;0;0;0;0;0';
        }
        return $this->data['statstraining'];
    }

    public function SetStatsTraining($value)
    {
        $this->data['statstraining'] = $value;
    }

    public function GetEquippedStats()
    {
        if ($this->data['equippedstats'] == '')
        {
            return '0;0;0;0;0;0';
        }
        return $this->data['equippedstats'];
    }

    public function GetTitelStats()
    {
        if ($this->data['titelstats'] == '')
        {
            return '0;0;0;0';
        }
        return $this->data['titelstats'];
    }

    public function SetEquippedStats($value)
    {
        $this->data['equippedstats'] = $value;
    }

    public function GetSkillPoints()
    {
        return $this->data['skillpoints'];
    }

    public function SetSkillPoints($value)
    {
        $this->data['skillpoints'] = $value;
    }

    public function GetSkillPointLearn()
    {
        return $this->data['skillpointlearn'];
    }

    public function SetSkillPointLearn($value)
    {
        $this->data['skillpointlearn'] = $value;
    }

    public function GetChallengeFight()
    {
        return $this->data['challengefight'];
    }

    public function SetChallengeFight($value)
    {
        $this->data['challengefight'] = $value;
    }

    public function GetChallengeTime()
    {
        return $this->data['challengedtime'];
    }

    public function SetChallengeTime($value)
    {
        $this->data['challengedtime'] = $value;
    }

    public function GetActionCountdown()
    {
        $actionStart = strtotime($this->GetActionStart());
        $timestamp = $actionStart + ($this->GetActionTime() * 60);
        if($this->HasActionBeforeReset($actionStart))
        {
            $timestamp += $this->GetBackupDifference();
        }
        $currentTime = strtotime("now");
        $difference = $timestamp - $currentTime;
        if ($difference < 0) $difference = 0;

        return $difference;
    }

    public function GetTravelActionCountdown()
    {
        $actionStart = strtotime($this->GetTravelActionStart());
        $timestamp = $actionStart + ($this->GetTravelActionTime() * 60);
        if($this->HasActionBeforeReset($actionStart))
        {
            $timestamp += $this->GetBackupDifference();
        }
        $currentTime = strtotime("now");
        $difference = $timestamp - $currentTime;
        if ($difference < 0) $difference = 0;
        return $difference;
    }

    public function SetAction($value)
    {
        $this->data['action'] = $value;
    }

    public function SetTravelAction($value)
    {
        $this->data['travelaction'] = $value;
    }

    public function GetActionStart()
    {
        return $this->data['actionstart'];
    }

    public function GetTravelActionStart()
    {
        return $this->data['travelactionstart'];
    }

    public function SetActionStart($value)
    {
        $this->data['actionstart'] = $value;
    }

    public function SetTravelActionStart($value)
    {
        $this->data['travelactionstart'] = $value;
    }

    public function GetActionTime()
    {
        return $this->data['actiontime'];
    }

    public function GetTravelActionTime()
    {
        return $this->data['travelactiontime'];
    }

    public function SetActionTime($value)
    {
        $this->data['actiontime'] = $value;
    }

    public function SetTravelActionTime($value)
    {
        $this->data['travelactiontime'] = $value;
    }

    public function GetTravelBonus()
    {
        return $this->data['travelbonus'];
    }

    public function GetTravelBonusInternal()
    {
        $travelBonus = $this->data['travelbonus'];
        return $travelBonus;
    }

    public function SetTravelBonus($value)
    {
        $this->data['travelbonus'] = $value;
    }

    public function GetTrainBonus()
    {
        return $this->data['trainingstats'];
    }

    public function SetTrainBonus($value)
    {
        $this->data['trainingstats'] = $value;
    }

    public function GetFightAttacks()
    {
        return $this->data['fightattacks'];
    }

    public function SetFightAttacks($value)
    {
        $this->data['fightattacks'] = $value;
    }

    public function GetAttacks()
    {
        return $this->data['attacks'];
    }

    public function GetSpielerKills()
    {
        return $this->data['spielertotungen'];
    }

    public function SetAttacks($value)
    {
        $this->data['attacks'] = $value;
    }

    public function AddFightAttack($techniques)
    {
        if ($this->GetFightAttacks() == '')
            $attacks = array();
        else
            $attacks = explode(';', $this->GetFightAttacks());

        foreach ($techniques as &$technique)
        {
            if (in_array($technique, $attacks))
                continue;

            array_push($attacks, $technique);
        }
        $attacks = implode(';', $attacks);
        $this->SetFightAttacks($attacks);
    }

    public function GetStatsPopup()
    {
        return $this->data['statspopup'];
    }

    public function SetStatsPopup($value)
    {
        $this->data['statspopup'] = $value;
    }

    public function GetLP()
    {
        return $this->data['lp'];
    }

    public function SetLP($value)
    {
        $this->data['lp'] = $value;
        $this->database->Update('lp="'.$value.'"', 'accounts', 'id="'.$this->GetID().'"');
    }

    public function GetMaxLP()
    {
        return $this->data['mlp'];
    }

    public function SetMaxLP($value)
    {
        $this->data['mlp'] = $value;
    }

    public function GetArenaKickCount()
    {
        return $this->data['arenakickcount'];
    }

    public function GetLPPercentage()
    {
        return round(($this->GetLP() / $this->GetMaxLP()) * 100);
    }

    public function GetKP()
    {
        return $this->data['kp'];
    }

    public function SetKP($value)
    {
        $this->data['kp'] = $value;
        $this->database->Update('kp="'.$value.'"', 'accounts', 'id="'.$this->GetID().'"');
    }

    public function GetMaxKP()
    {
        return $this->data['mkp'];
    }

    public function SetMaxKP($value)
    {
        $this->data['mkp'] = $value;
    }

    public function GetKPPercentage()
    {
        return round(($this->GetKP() / $this->GetmaxKP()) * 100);
    }

    public function GetAttack()
    {
        return $this->data['attack'];
    }

    public function SetAttack($value)
    {
        $this->data['attack'] = $value;
    }

    public function GetDefense()
    {
        return $this->data['defense'];
    }

    public function SetDefense($value)
    {
        $this->data['defense'] = $value;
    }

    public function GetCritchance()
    {
        return $this->data['critchance'];
    }

    public function SetCritchance($value)
    {
        $this->data['critchance'] = $value;
    }

    public function GetCritdamage()
    {
        return $this->data['critdamage'];
    }

    public function SetCritdamage($value)
    {
        $this->data['critdamage'] = $value;
    }

    public function GetAccuracy()
    {
        return $this->data['accuracy'];
    }

    public function SetAccuracy($value)
    {
        $this->data['accuracy'] = $value;
    }

    public function GetReflex()
    {
        return $this->data['reflex'];
    }

    public function SetReflex($value)
    {
        $this->data['reflex'] = $value;
    }

    public function SetPvP($value)
    {
        $this->data['kopfgeld'] = $value;
    }

    public function SetGold($value)
    {
        $this->data['gold'] = $value;
        $this->database->Update('gold='.$value, 'accounts', 'id='.$this->GetID());
    }

    public function GetPvP()
    {
        return $this->data['kopfgeld'];
    }

    public function GetGold()
    {
        return $this->data['gold'];
    }

    public function GetKI()
    {
        $value = ($this->GetMaxLP() / 10) + ($this->GetMaxKP() / 10) + ($this->GetAttack() / 2) + $this->GetDefense();
        //$value = ($this->GetMaxLP() / 10) + ($this->GetMaxKP() / 10) + $this->GetAttack() + $this->GetDefense(); TODO: Reupload
        return round($value / 4);
    }

    public function GetRace()
    {
        return $this->data['race'];
    }

    public function SetRace($value)
    {
        $this->database->Update('race="'.$value.'"', 'accounts', 'id="'.$this->GetID().'"');
    }

    public function GetRaceImage()
    {
        return $this->data['raceimage'];
    }

    public function SetRaceImage($value)
    {
        $this->data['raceimage'] = $value;
        $this->database->Update('raceimage="'.$value.'"', 'accounts', 'id="'.$this->GetID().'"');
    }

    public function ChangeRaceImage($value)
    {
        $this->SetRaceImage($_POST['raceimage']);
        $newBerry = $this->GetBerry() - 25000;
        $this->SetBerry($newBerry);
        $this->database->Update('zeni=' . $newBerry . ',raceimage="' . $value . '"', 'accounts', 'id = ' . $this->GetID() . '', 1);
    }

    public function GetLevel()
    {
        return $this->data['level'];
    }

    public function SetLevel($value)
    {
        $this->data['level'] = $value;
    }

    public function GetBerry()
    {
        return $this->data['zeni'];
    }

    public function GetMünzen()
    {
        return $this->data['münzen'];
    }

    public function SetMünzen($value)
    {
        $this->data['münzen'] = $value;
    }

    public function SetBerry($value)
    {
        $this->data['zeni'] = $value;
        $this->database->Update('zeni="'.$value.'"', 'accounts', 'id='.$this->GetID());
    }

    public function GetArenaPoints()
    {
        return $this->data['arenapoints'];
    }

    public function GetDailyArenaPoints()
    {
        return $this->data['dailyarenapoints'];
    }

    public function SetArenaPoints($value)
    {
        $this->database->Update('arenapoints="'.$value.'"', 'accounts', 'id="'.$this->GetID().'"');
        $this->data['arenapoints'] = $value;
    }

    public function GetClanWarPopup()
    {
        return $this->data['clanwarpopup'];
    }

    public function SetClanWarPopup($value)
    {
        $this->database->Update('clanwarpopup="'.$value.'"', 'accounts', 'id="'.$this->GetID().'"');
    }

    public function GetStats()
    {
        return $this->data['stats'];
    }

    public function UpdateLastTimeClanVisit()
    {
        $this->data['lasttimeclanvisited'] = date('Y-m-d H:i:s', time());
        $this->database->Update('lasttimeclanvisited="'.date('Y-m-d H:i:s', time()).'"', 'accounts', 'id='.$this->GetID(), 1);
    }


    public function GetLastTimeClanVisited()
    {
        return $this->data['lasttimeclanvisited'];
    }

    public function GetLastTimePatchnotesVisited()
    {
        return $this->data['patchnotesvisited'];
    }

    public function UpdateLastTimePatchnotesVisit()
    {
        $this->data['patchnotesvisited'] = date('Y-m-d H:i:s', time());
        $this->database->Update('patchnotesvisited="'.date('Y-m-d H:i:s', time()).'"', 'accounts', 'id='.$this->GetID(), 1);
    }

    public function GetAssignedStats()
    {
        return $this->data['assignedstats'];
    }

    public function SetStats($value)
    {
        $this->data['stats'] = $value;
        $result = $this->database->Update('stats="'.$value.'"', 'accounts', 'id="'.$this->GetID().'"');
    }

    public function IsKoloClose()
    {
        return $this->data['koloclose'];
    }

    public function IsKGClose()
    {
        return $this->data['kgclose'];
    }

    public function IsEloClose()
    {
        return $this->data['eloclose'];
    }

    public function GetWon()
    {
        return $this->data['won'];
    }

    public function SetWon($value)
    {
        $this->data['won'] = $value;
    }

    public function GetDraws()
    {
        return $this->data['draws'];
    }

    public function SetDraws($value)
    {
        $this->data['draws'] = $value;
    }

    public function GetLost()
    {
        return $this->data['lost'];
    }

    public function GetPPicture()
    {
        return $this->data['pimage'];
    }

    public function SetPPicture($value)
    {
        $this->data['pimage'] = $value;
        $result = $this->database->Update('pimage="'.$value.'"', 'accounts', 'id="'.$this->GetID().'"');
    }

    public function SetLost($value)
    {
        $this->data['lost'] = $value;
    }

    public function GetTFights()
    {
        return $this->data['tfights'];
    }

    public function SetClanRunPoints($value)
    {
        if($this->GetClan() != 0)
        {
            $ClanManager = $this->database->Select('*', 'clans', 'id="'.$this->GetClan().'"');
            $clan = $ClanManager->fetch_assoc();
            $update = $clan['runnpoints'] + $value;
            $result = $this->database->Update('runnpoints="'.$update.'"', 'clans', 'id="'.$clan['id'].'"');
        }
    }

    public function SetTFights($value)
    {
        $this->data['tfights'] = $value;
    }

    public function GetAttackLevel($id)
    {
        $attacks = explode(";", $this->data['attacklevel']);
        $i = 0;
        while(!is_null($attacks[$i]))
        {
            $attacks[$i] = explode("@", $attacks[$i]);
            if($attacks[$i][0] == $id)
            {
                return $attacks[$i][1];
            }
            ++$i;
        }
        return null;
    }

    public function GetAttackLevels()
    {
        return $this->data['attacklevel'];
    }

    public function SetAttackLevel($id, $value)
    {
        $attacks = explode(";", $this->data['attacklevel']);
        $i = 0;
        $found = false;
        while(!is_null($attacks[$i]))
        {
            $attacks[$i] = explode("@", $attacks[$i]);
            if($attacks[$i][0] == $id)
            {
                if($attacks[$i][1] < 20)
                {
                    $attacks[$i][1] = $value;
                    $attacks[$i] = implode("@", $attacks[$i]);
                    $found = true;
                    break;
                }
            }
            $attacks[$i] = implode("@", $attacks[$i]);
            ++$i;
        }

        if(!$found)
        {
            $attacks[] = $id."@1";
        }
        $attacks = implode(";", $attacks);
        $this->database->Update('attacklevel="'.$attacks.'"', 'accounts', 'id='.$this->GetID());
    }

    public function GetSFights()
    {
        return $this->data['sfights'];
    }

    public function SetSFights($value)
    {
        $this->data['sfights'] = $value;
    }

    public function GetWFights()
    {
        return $this->data['wfights'];
    }

    public function SetWFights($value)
    {
        $this->data['wfights'] = $value;
    }

    public function GetDailyfights()
    {
        return $this->data['dailyfights'];
    }

    public function SetDailyfights($value)
    {
        $this->data['dailyfights'] = $value;
    }

    public function GetDailyNPCFights()
    {
        return $this->data['dailynpcfights'];
    }

    public function SetDailyNPCFights($value)
    {
        $this->data['dailynpcfights'] = $value;
    }

    public function GetDailyNPCFightsMax()
    {
        return $this->data['dailynpcfightsmax'];
    }

    public function GetPvPMaxFights()
    {
        return $this->data['dailykopfgeldmax'];
    }

    public function SetPvPMaxFights($value)
    {
        $this->data['dailykopfgeldmax'] = $value;
        $this->database->Update('dailykopfgeldmax="'.$value.'"', 'accounts', 'id="'.$this->GetID().'"');
    }

    public function SetDailyNPCFightsMax($value)
    {
        $this->data['dailynpcfightsmax'] = $value;
        $this->database->Update('dailynpcfightsmax="'.$value.'"', 'accounts', 'id="'.$this->GetID().'"');
    }

    public function GetTotalStatsFights()
    {
        return $this->data['totalstatsfights'];
    }

    public function GetPromoStats($id)
    {
        $usepromocodes = $this->database->Select('*', 'usepromocodes', 'userid="'.$id.'"');
        if($usepromocodes)
        {
            $promostats = 0;
            while($use = $usepromocodes->fetch_assoc())
            {
                $promocodess = $this->database->Select('*', 'promocodes', 'promocode="'.$use['promocode'].'"', 1);
                if($promocodess)
                {
                    $promo = $promocodess->fetch_assoc();
                    if($promo) {
                        $promostats += $promo['stats'];
                    }
                }
            }
            return $promostats;
        }
    }

    public function GetMaxStatsFights()
    {
        return $this->GetLevel() * 10;
    }

    public function GetMaxStatsTrain()
    {
        return $this->GetLevel() * 100;
    }

    public function SetTotalDailyFights($value)
    {
        $this->data['totaldailyfights'] = $value;
    }
    public function GetTotalDailyfights()
    {
        return $this->data['totaldailyfights'];
    }

    public function GetPlanet()
    {
        return $this->data['planet'];
    }

    public function SetPlanet($value)
    {
        $this->data['planet'] = $value;
    }

    public function GetPreviousPlace()
    {
        return $this->data['previousplace'];
    }

    public function SetPreviousPlace($value)
    {
        $this->data['previousplace'] = $value;
    }

    public function GetPlace()
    {
        return $this->data['place'];
    }

    public function SetPlace($value)
    {
        $this->data['place'] = $value;
    }

    public function GetImage()
    {
        if ($this->data['charimage'] == '')
        {
            return 'img/imagefail.png';
        }
        return $this->data['charimage'];
    }

    public function SetImage($value)
    {
        if ($value == '') $value = 'img/imagefail.png';
        $this->data['charimage'] = $value;
    }

    public function GetDesign()
    {
        return $this->data['design'];
    }

    public function GetBackground()
    {
        return $this->data['background'];
    }

    public function GetProfileBG()
    {
        return $this->data['profilebg'];
    }

    public function GetHeader()
    {
        return $this->data['header'];
    }

    public function SetBackground($background)
    {
        $this->data['background'] = $background;
    }

    public function SetProfileBG($profilebg)
    {
        $this->data['profilebg'] = $profilebg;
    }

    public function SetHeader($header)
    {
        $this->data['header'] = $header;
    }

    public function GetRealFakeKI()
    {
        return $this->data['fakeki'];
    }

    public function CanFakeKI()
    {
        return $this->data['canfakeki'];
    }

    public function SetCanFakeKI($value)
    {
        $this->data['canfakeki'] = $value;
    }

    public function GetCountArticle()
    {
        $ArtikelCheck = $this->database->Select('*', 'verzeichnis', 'creator="'.$this->GetID().'"');
        $count = $ArtikelCheck->num_rows;
        return $count;
    }

    public function GetFakeKI()
    {
        if ($this->data['fakeki'] == 0)
        {
            return $this->GetKI();
        }
        return $this->data['fakeki'];
    }

    public function GetRankOne()
    {
        return $this->data['rankone'];
    }

    public function SetRankOne($value)
    {
        $this->data['rankone'] = $value;
    }

    public function GetRankTwo()
    {
        return $this->data['ranktwo'];
    }

    public function SetRankTwo($value)
    {
        $this->data['ranktwo'] = $value;
    }

    public function GetRankThree()
    {
        return $this->data['rankthree'];
    }

    public function SetRankThree($value)
    {
        $this->data['rankthree'] = $value;
    }

    public function GetArank()
    {
        return $this->data['arank'];
    }

    public function GetCanJoin()
    {
        return $this->data['canjoin'];
    }

    public function InClanSince(): int
    {
        $today = new DateTime("now");
        $match_date = DateTime::createFromFormat('Y-m-d H:i:s', $this->data['clansince']);
        $match_date = $match_date->setTime(0, 0);
        $diff = $today->diff($match_date);
        return (integer)$diff->format("%R%a");
    }

    public function GetTitel()
    {
        return $this->data['titel'];
    }

    public function GetTitels()
    {
        if ($this->data['titels'] == '')
            return array();

        return explode(';', $this->data['titels']);
    }

    public function HasTitel($titel)
    {
        return in_array($titel, $this->GetTitels());
    }

    public function SetTitels($titels)
    {
        $this->data['titels'] = $titels;
    }

    public function GetRank()
    {
        return $this->data['rank'];
    }

    public function SetRank($value)
    {
        $this->data['rank'] = $value;
    }

    public function GetText()
    {
        return $this->data['text'];
    }

    public function SetText($value)
    {
        $this->data['text'] = $value;
    }

    public function CanTeleport()
    {
        return $this->data['canteleport'] == 1;
    }

    public function SetCanTeleport($value)
    {
        $this->data['canteleport'] = $value;
    }

    public function GetFight()
    {
        return $this->data['fight'];
    }

    public function GetChallengedPopUp()
    {
        return $this->data['challengedpopup'];
    }

    public function GetImpelDownPopUp()
    {
        return $this->data['impeldownpopup'];
    }

    public function SetImpelDownPopUp($value)
    {
        $this->data['impeldownpopup'] = $value;
        $this->database->Update('impeldownpopup="'.$value.'"', 'accounts', 'id="'.$this->GetID().'"');
    }

    public function SetFight($value)
    {
        $this->data['fight'] = $value;
        $this->database->Update('fight="'.$value.'"', 'accounts', 'id="'.$this->data['id'].'"');
    }

    public function SetDesign($design)
    {
        $this->data['design'] = $design;
    }

    public function GetReflexValue()
    {
        return $this->data['reflexvalue'];
    }

    public function SetTitel($titel)
    {
        $this->data['titel'] = $titel;
    }

    public function SetFakeKI($value)
    {
        $this->data['fakeki'] = $value;
        $result = $this->database->Update('fakeki="'.$value.'"', 'accounts', 'id="'.$this->GetID().'"');
    }

    public function GetFriends()
    {
        return $this->data['friends'];
    }

    public function ReturnOffer($amount, $gold)
    {
        if(!$gold)
            $this->SetBerry($this->GetBerry() + $amount);
        else
            $this->SetGold(($this->GetGold() + $amount));
    }

    public function GetLastPage()
    {
        return $this->data['lastpage'];
    }

    public function HasOrange1()
    {
        return $this->data['orange1'] != 0;
    }

    public function SetOrange1($value)
    {
        $this->data['orange1'] = $value;
    }

    public function HasOrange2()
    {
        return $this->data['orange2'] != 0;
    }

    public function SetOrange2($value)
    {
        $this->data['orange2'] = $value;
    }

    public function HasOrange3()
    {
        return $this->data['orange3'] != 0;
    }

    public function SetOrange3($value)
    {
        $this->data['orange3'] = $value;
    }

    public function SetWappen($value)
    {
        $this->data['wappen'] = $value;
        $this->database->Update('wappen='.$value,'accounts','id='.$this->GetID());
    }

    public function ShowWappen()
    {
        return $this->data['wappen'] == 1;
    }

    public function OpenMirror($value)
    {
        $this->data['mirrorpopup'] = $value;
    }

    public function GetMirror()
    {
        return $this->data['mirrorpopup'];
    }

    public function SetMirror($value)
    {
        $this->data['mirrorpopup'] = $value;
        $this->database->Update('mirrorpopup='.$value, 'accounts', 'id='.$this->GetID());
    }
}

if (!isset($isChat))
{
    $isChat = false;
}
$id = 0;
if ($account->IsLogged())
{
    $id = $account->Get('id');
}
$player = new Player($database, 0, $actionManager, $isChat, $id);

?>