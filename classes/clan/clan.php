<?php
if ($database == NULL)
{
    print 'This File (' . __FILE__ . ') should be after Database!';
}

include_once 'clanlogmessage.php';
include_once 'clanshoutboxmsg.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/items/itemmanager.php';

function CreateClan($database, $name, $tag, $player): bool
{
    $name = $database->EscapeString($name);
    $result = $database->Select('*', 'clans', 'name="' . $name . '" OR tag="' . $tag . '"', 1);
    if ($result)
    {
        if ($result->num_rows > 0)
        {
            $result->close();
            return false;
        }
        $result->close();
    }
    $database->Insert('name, tag, leader, members, ranks', '"' . $name . '","' . $tag . '",' . $player->GetID() . ',1, "Kapitän;1;1;1;1;1;1;1@Vize-Kapitän;1;1;1;1;1;1;0@Mitglied;1;0;0;0;1;0;0"', 'clans');
    $clanID = $database->GetLastID();

    $player->SetClan($clanID);
    $player->SetClanName($name);

    $result = $database->Select('COUNT(id) as total', 'clans', '');
    $rank = 0;
    if ($result)
    {
        $row = $result->fetch_assoc();
        $rank = $row['total'];
        $result->close();
    }
    $database->Update('rang="' . $rank . '"', 'clans', 'id = ' . $clanID, 1);

    $database->Update('clanrank=0, clan="' . $clanID . '",clanname="' . $name . '"', 'accounts', 'id = ' . $player->GetID(), 1);
    return true;
}

class Clan
{
    private $database;
    private $data;
    private $valid;
    private $messages;
    private $shoutbox;

    function __construct($db, $id, $select = '*')
    {
        $this->database = $db;
        $this->valid = false;
        $this->messages = array();
        $this->shoutbox = array();
        $this->LoadData($id, $select);
    }

    public function LoadShoutbox($data)
    {
        if ($data == '')
        {
            return;
        }
        $users = explode('@', $data);
        $i = 0;
        while (isset($users[$i]))
        {
            $msg = explode(';', $users[$i]);
            $clanmsg = new ClanShoutboxMSG($msg);
            $this->shoutbox[] = $clanmsg;
            ++$i;
        }
    }

    public function GetAllianceInvites()
    {
        if($this->data['allianceinvite'] == "") return array();
        return explode(";", $this->data['allianceinvite']);
    }

    public function GetAlliances()
    {
        if($this->data['alliances'] == "") return array();
        return explode(";", $this->data['alliances']);
    }

    public function GetGoldTournament()
    {
        return $this->data['goldtour'];
    }

    public function SetGoldTournmanet($value)
    {
        $this->data['goldtour'] = $value;
        $this->database->Update('goldtour="'.$value.'"', 'clans', 'id="'.$this->GetID().'"');
    }

    public function GetMember()
{
    $chku = $this->database->Select('clan', 'accounts', 'clan="'.$this->GetID().'"');
    $num = $chku->num_rows;
    return $num;
}

    public function GetBerryTournament()
    {
        return $this->data['berrytour'];
    }

    public function SetBerryTournmanet($value)
    {
        $this->data['berrytour'] = $value;
        $this->database->Update('berrytour="'.$value.'"', 'clans', 'id="'.$this->GetID().'"');
    }

    public function GetOrangeFruitTournament()
    {
        return $this->data['orangefruittour'];
    }

    public function SetOrangeTournmanet($value)
    {
        $this->data['orangefruittour'] = $value;
        $this->database->Update('orangefruittour="'.$value.'"', 'clans', 'id="'.$this->GetID().'"');
    }

    public function GetRedFruitTournament()
    {
        return $this->data['redfruittour'];
    }

    public function SetRedTournmanet($value)
    {
        $this->data['redfruittour'] = $value;
        $this->database->Update('redfruittour="'.$value.'"', 'clans', 'id="'.$this->GetID().'"');
    }

    public function GetVitaTournament()
    {
        return $this->data['vitatour'];
    }

    public function SetVitaTournmanet($value)
    {
        $this->data['vitatour'] = $value;
        $this->database->Update('vitatour="'.$value.'"', 'clans', 'id="'.$this->GetID().'"');
    }

    public function GetTestoTournament()
    {
        return $this->data['testotour'];
    }

    public function SetTestoTournmanet($value)
    {
        $this->data['testotour'] = $value;
        $this->database->Update('testotour="'.$value.'"', 'clans', 'id="'.$this->GetID().'"');
    }

    public function GetTourFinish()
    {
        return $this->data['istourfinish'];
    }

    public function SetTourFinishTournmanet($value)
    {
        $this->data['istourfinish'] = $value;
        $this->database->Update('istourfinish="'.$value.'"', 'clans', 'id="'.$this->GetID().'"');
    }

    public function GetTourFinishTournament()
    {
        return $this->data['istourfinish'];
    }

    public function SetAllTournamentDrops()
    {
        $CheckAllUser = $this->database->Select('*', 'accounts', 'clan="'.$this->GetID().'" and bookingdays = 0');
        if($CheckAllUser && $CheckAllUser->num_rows > 3)
        {
            $setTourFinished = 0;
            $setGoldFinished = 0;
            $setOrangeFinish = 0;
            $setRedFinish = 0;
            $setVitaFinish = 0;
            $setTestoFinish = 0;
            $setBerryFinished = 0;
            while($user = $CheckAllUser->fetch_assoc())
            {
                $player = new Player($this->database, $user['id']);
                if($this->GetRunningPoints() >= 350 && $this->GetRunningPoints() < 500)
                {
                    if($this->GetGoldTournament() == 0)
                    {
                        $setGoldFinished = 1;
                        $goldwin = $player->GetGold() + 100;
                        $player->SetGold($goldwin);
                        $PMManager = new PMManager($this->database, $player->GetID());
                        $text = "Glückwunsch, ihr schreitet schnell voran, bleibt dran! Für das erreichen von 350 Punkte erhält jeder Spieler in deiner Bande 100 Gold";
                        $PMManager->SendPM(649, 'img/system.png', $this->GetName(), 'Banden Tournament', $text, $player->GetName(), 1);
                    }
                }
                else if($this->GetRunningPoints() >= 500 && $this->GetRunningPoints() < 650)
                {
                    if($this->GetBerryTournament() == 0)
                    {
                        $setBerryFinished = 1;
                        $berrywin = $player->GetBerry() + 50000;
                        $player->SetBerry($berrywin);
                        $PMManager = new PMManager($this->database, $player->GetID());
                        $text = "Glückwunsch, ihr schreitet schnell voran, bleibt dran! Für das erreichen von 500 Punkte erhält jeder Spieler in deiner Bande 50000 Berry";
                        $PMManager->SendPM(649, 'img/system.png', $this->GetName(), 'Banden Tournament', $text, $player->GetName(), 1);
                    }
                }
                else if($this->GetRunningPoints() >= 650 && $this->GetRunningPoints() < 850)
                {
                    if($this->GetOrangeFruitTournament() == 0)
                    {
                        $setOrangeFinish = 1;
                        $player->AddItems(87, 87, 1);
                        $PMManager = new PMManager($this->database, $player->GetID());
                        $text = "Glückwunsch, ihr schreitet schnell voran, bleibt dran! Für das erreichen von 650 Punkte erhält jeder Spieler in deiner Bande 1x das Item Seltene Orangene Frucht";
                        $PMManager->SendPM(649, 'img/system.png', $this->GetName(), 'Banden Tournament', $text, $player->GetName(), 1);
                    }
                }
                else if($this->GetRunningPoints() >= 850 && $this->GetRunningPoints() < 950)
                {
                    if($this->GetRedFruitTournament() == 0)
                    {
                        $setRedFinish = 1;
                        $player->AddItems(86, 86, 1);
                        $PMManager = new PMManager($this->database, $player->GetID());
                        $text = "Glückwunsch, ihr schreitet schnell voran, bleibt dran! Für das erreichen von 850 Punkte erhält jeder Spieler in deiner Bande 1x das Item Seltene Rote Frucht";
                        $PMManager->SendPM(649, 'img/system.png', $this->GetName(), 'Banden Tournament', $text, $player->GetName(), 1);
                    }
                }
                else if($this->GetRunningPoints() >= 950 && $this->GetRunningPoints() < 1150)
                {
                    if($this->GetVitaTournament() == 0)
                    {
                        $setVitaFinish = 1;
                        $player->AddItems(82, 82, 1);
                        $PMManager = new PMManager($this->database, $player->GetID());
                        $text = "Glückwunsch, ihr schreitet schnell voran, bleibt dran! Für das erreichen von 950 Punkte erhält jeder Spieler in deiner Bande 1x das Item Vitamine";
                        $PMManager->SendPM(649, 'img/system.png', $this->GetName(), 'Banden Tournament', $text, $player->GetName(), 1);
                    }
                }
                else if($this->GetRunningPoints() >= 1150 && $this->GetRunningPoints() < 1350)
                {
                    if($this->GetTestoTournament() == 0)
                    {
                        $setTestoFinish = 1;
                        $player->AddItems(81, 81, 1);
                        $PMManager = new PMManager($this->database, $player->GetID());
                        $text = "Glückwunsch, ihr schreitet schnell voran, bleibt dran! Für das erreichen von 1150 Punkte erhält jeder Spieler in deiner Bande 1x das Item Testo Booster";
                        $PMManager->SendPM(649, 'img/system.png', $this->GetName(), 'Banden Tournament', $text, $player->GetName(), 1);
                    }
                }
                else if($this->GetRunningPoints() >= 1350)
                {
                    if($this->GetTourFinishTournament() == 0)
                    {
                        $month = date('F');
                        $wolke = 0;
                        if($month == 'January' || $month == 'March' || $month == 'May' || $month == 'July' || $month == 'September' || $month == 'November')
                        {
                            $wolke = 406;
                        }
                        else
                        {
                            $wolke = 407;
                        }
                        $setTourFinished = 1;
                       $player->AddItems($wolke, $wolke, 1);
                        $PMManager = new PMManager($this->database, $player->GetID());
                        $text = "Glückwunsch, ihr schreitet schnell voran, bleibt dran! Für das erreichen von 1350 Punkte erhält jeder Spieler in deiner Bande 100 Elopunkte";
                        $PMManager->SendPM(649, 'img/system.png', $this->GetName(), 'Banden Tournament', $text, $player->GetName(), 1);
                    }
                }
            }
            if($setGoldFinished)
                $this->SetGoldTournmanet(1);
            if($setBerryFinished)
                $this->SetBerryTournmanet(1);
            if($setOrangeFinish)
                $this->SetOrangeTournmanet(1);
            if($setRedFinish)
                $this->SetRedTournmanet(1);
            if($setVitaFinish)
                $this->SetVitaTournmanet(1);
            if($setTestoFinish)
                $this->SetTestoTournmanet(1);
            if($setTourFinished)
                $this->SetTourFinishTournmanet(1);
        }
    }



    public function AddAllianceMember($value)
    {
        $otherClan = new Clan($this->database, $value);
        if($otherClan->IsValid())
        {
            $allianceClans = $this->GetAlliances();
            $allianceClans[] = $otherClan->GetID();
            $allianceClans = implode(";", $allianceClans);
            $this->data['alliances'] = $allianceClans;
            $this->database->Update('alliances="'.$allianceClans.'"', 'clans', 'id='.$this->GetID());

            $otherAllianceClans = $otherClan->GetAlliances();
            $otherAllianceClans[] = $this->GetID();
            $otherAllianceClans = implode(";", $otherAllianceClans);
            $this->database->Update('alliances="'.$otherAllianceClans.'"', 'clans', 'id='.$otherClan->GetID());
        }
    }

    public function AddAllianceInvite($value)
    {
        $allianceClans = $this->GetAllianceInvites();
        $allianceClans[] = $value;
        $allianceClans = implode(";", $allianceClans);
        $this->data['allianceinvite'] = $allianceClans;
        $this->database->Update('allianceinvite="'.$allianceClans.'"', 'clans', 'id='.$this->GetID());
    }

    public function AllTaskISNPCs() : int
    {
        $allnpcuser = 0;
        $HolMember = $this->database->Select('*', 'accounts', 'clan="'.$this->GetID().'" and bookingdays = 0');
        if($HolMember)
        {
            $rowuser = $HolMember->num_rows; // Die Anzahl der Member!
            $allnpcuser = $rowuser * 50; // Die Anzahl an möglichen
        }
        return $allnpcuser;
    }

    public function AllTaskNPCs() : int
    {
        $IsAnswer = 0;
        $HolMember = $this->database->Select('*', 'accounts', 'clan="'.$this->GetID().'" and bookingdays = 0');
        if($HolMember)
        {
            while($taskmember = $HolMember->fetch_assoc())
            {
                $IsAnswer += min($taskmember['dailynpcfights'], 50);
            }
        }
        return $IsAnswer;
    }

    public function AllTaskNPCPerecentage()
    {
        $perecentage = 0;
        $HolMember = $this->database->Select('*', 'accounts', 'clan="'.$this->GetID().'" and bookingdays = 0');
        if($HolMember)
        {
            $IsAnswer = 0;
            while($taskmember = $HolMember->fetch_assoc())
            {
                $rowuser = $HolMember->num_rows; // Die Anzahl der Member!
                $memberrow = $rowuser * 50;
                $IsAnswer += min($taskmember['dailynpcfights'], 50);
                $perecentage = round((($IsAnswer * 100) / $memberrow));
            }
        }
        return $perecentage;
    }


    public function GetNpcQuest()
    {
        return $this->data['npcquest'];
    }

    public function SetNpcQuest($row)
    {
        $this->data['npcquest'] = $row;
        $this->database->Update('npcquest="'.$row.'"', 'clans', 'id="'.$this->GetID().'"');
    }

    public function SetAllTaskNPCDrop($yesterday = 0): void
    {
        $result = $this->database->Select('*', 'accounts', 'clan="'.$this->GetID().'" AND bookingdays=0');
        if($result && $result->num_rows >= 3)
        {
            $MaxNPCClanFights = $result->num_rows * 50;
            $NPCClanFights = 0;
            $received = false;
            while($row = $result->fetch_assoc())
            {
                $NPCClanFights += min($row['dailynpcfights'], 50);
            }
            $result->data_seek(0);
            while($members = $result->fetch_assoc())
            {
                $player = new Player($this->database, $members['id']);
                $PMManager = new PMManager($this->database, $members['id']);
                if($NPCClanFights >= $MaxNPCClanFights && $this->GetNpcQuest() == 0 || $yesterday == 1)
                {
                   $player->AddItems(8, 8, 1);
                    $yesterdaytext = '';
                    if($yesterday != 0)
                        $yesterdaytext = ' [Für den Vortag]';
                    $pms = "Glückwunsch! Deine Bande hat erfolgreich die Quest der NPC kämpfe absolviert! Dafür erhältst du 1x Sehr starker Trank";
                    $PMManager->SendPM(1, 'img/system.png', 'Support', 'NPC Kampf Quest' . $yesterdaytext, $pms, $player->GetName(), 1);
                    $received = true;
                }
            }
            if($received && $yesterday == 0)
            {
                $this->SetNPCQuest(1);
            }
        }
        echo "<br/>---------------------------------------------------------------------------<br/>";
    }

    public function GetEloQuest()
    {
        return $this->data['eloquest'];
    }

    public function SetEloQuest($row)
    {
        $this->data['eloquest'] = $row;
        $this->database->Update('eloquest="'.$row.'"', 'clans', 'id="'.$this->GetID().'"');
    }

    public function SetAllTaskEloDrop($yesterday = 0)
    {
        $result = $this->database->Select('*', 'accounts', 'clan="'.$this->GetID().'" AND bookingdays=0');
        if($result && $result->num_rows >= 3)
        {
            $itemManager = new ItemManager($this->database);
            $MaxEloClanFights = $result->num_rows * 5;
            $EloClanFights = 0;
            $received = false;
            while($row = $result->fetch_assoc())
            {
                $EloClanFights += min($row['dailyelofights'], 5);
            }
            $result->data_seek(0);
            while($members = $result->fetch_assoc())
            {
                $DropEloFights = floor(90 * ($MaxEloClanFights / 100));
                $player = new Player($this->database, $members['id']);
                $PMManager = new PMManager($this->database, $members['id']);
                if($EloClanFights >= $DropEloFights && $this->GetEloQuest() == 0 || $yesterday == 1)
                {
                   $player->SetGold($player->GetGold() + 5);
                   $player->AddItems(60, 60, 1);
                   $player->AddItems(8, 8, 1);
                   $player->SetArenaPoints($player->GetArenaPoints() + 100);
                    $yesterdaytext = '';
                    if($yesterday != 0)
                        $yesterdaytext = ' [Für den Vortag]';
                    $pms = "Glückwunsch! Deine Bande hat erfolgreich die Quest der Elokämpfe absolviert! Dafür erhältst du 1x Schatztruhe und 5 Gold + 100 Kolosseumpunkte!";
                    $PMManager->SendPM(1, 'img/system.png', 'Support', 'Elokampf Quest' . $yesterdaytext, $pms, $player->GetName(), 1);
                    $received = true;
                }
            }
            if($received && $yesterday == 0)
            {
                $this->SetEloQuest(1);
            }
        }
        echo "<br/>---------------------------------------------------------------------------<br/>";
    }

    public function AllTaskISElo() : int
    {
        $allelouser = 0;
        $HolMember = $this->database->Select('*', 'accounts', 'clan="'.$this->GetID().'" and bookingdays = 0');
        if($HolMember)
        {
            $rowuser = $HolMember->num_rows; // Die Anzahl der Member!
            $allelouser = $rowuser * 5; // Die Anzahl an möglichen
        }
        return $allelouser;
    }

    public function AllTaskElos() : int
    {
        $IsAnswer = 0;
        $HolMember = $this->database->Select('*', 'accounts', 'clan="'.$this->GetID().'" and bookingdays = 0');
        if($HolMember)
        {
            while($taskmember = $HolMember->fetch_assoc())
            {
                $IsAnswer += min($taskmember['dailyelofights'], 5);
            }
        }
        return $IsAnswer;
    }

    public function AllTaskEloPerecentage() : int
    {
        $IsAnswer = 0;
        $memberrow = 0;
        $HolMember = $this->database->Select('*', 'accounts', 'clan="'.$this->GetID().'" and bookingdays = 0');
        if($HolMember)
        {
            $rowuser = $HolMember->num_rows;
            $memberrow = $rowuser * 5;
            while($taskmember = $HolMember->fetch_assoc())
            {
                $IsAnswer += min($taskmember['dailyelofights'], 5);
            }
        }
        return round((($IsAnswer * 100) / $memberrow));
    }

    public function GetPvPQuest()
    {
        return $this->data['kgquest'];
    }

    public function SetPvPQuest($row)
    {
        $this->data['kgquest'] = $row;
        $this->database->Update('kgquest="'.$row.'"', 'clans', 'id="'.$this->GetID().'"');
    }

    public function SetAllTaskPvPDrop($yesterday = 0)
    {
        $result = $this->database->Select('*', 'accounts', 'clan="'.$this->GetID().'" AND bookingdays=0');
        if($result && $result->num_rows >= 3)
        {
            $itemManager = new ItemManager($this->database);
            $MaxPvPClanFights = $result->num_rows * 5;
            $PvPClanFights = 0;
            $received = false;
            while($row = $result->fetch_assoc())
            {
                $PvPClanFights += min($row['dailyfights'], 5);
            }
            $result->data_seek(0);
            while($members = $result->fetch_assoc())
            {
                $HalfPvPFights = floor(90 * ($MaxPvPClanFights / 100));
                $player = new Player($this->database, $members['id']);
                $PMManager = new PMManager($this->database, $members['id']);
                if($PvPClanFights >= $HalfPvPFights && $this->GetPvPQuest() == 0 || $yesterday == 1)
                {
                    $player->SetGold($player->GetGold() + 5);
                    $player->SetBerry($player->GetBerry() + 5000);
                    $player->AddItems(409, 409, 1);
                    $yesterdaytext = '';
                    if($yesterday != 0)
                        $yesterdaytext = ' [Für den Vortag]';
                    $pms = "Glückwunsch! Deine Bande hat erfolgreich die Quest der PvP-Kämpfe absolviert! Dafür erhältst du 1x 5 Gold, 1x 5.000 Bery und 1x die Pfadtruhe";
                    $PMManager->SendPM(1, 'img/system.png', 'Support', 'PvP Quest' . $yesterdaytext, $pms, $player->GetName(), 1);
                    $received = true;
                }
            }
            if($received && $yesterday == 0)
            {
                $this->SetPvPQuest(1);
            }
        }
        echo "<br/>---------------------------------------------------------------------------<br/>";
    }

    public function AllTaskISPvP() : int
    {
        $allnpcuser = 0;
        $HolMember = $this->database->Select('*', 'accounts', 'clan="'.$this->GetID().'" and bookingdays = 0');
        if($HolMember)
        {
            $rowuser = $HolMember->num_rows; // Die Anzahl der Member!
            $allnpcuser = $rowuser * 5; // Die Anzahl an möglichen
        }
        return $allnpcuser;
    }

    public function AllTaskPvP() : int
    {
        $IsAnswer = 0;
        $HolMember = $this->database->Select('*', 'accounts', 'clan="'.$this->GetID().'" and bookingdays = 0');
        if($HolMember)
        {
            while($taskmember = $HolMember->fetch_assoc())
            {
                $IsAnswer += min($taskmember['dailyfights'], 5);
            }
        }
        return $IsAnswer;
    }

    public function AllTaskPvPPerecentage()
    {
        $IsAnswer = 0;
        $memberrow = 0;
        $HolMember = $this->database->Select('*', 'accounts', 'clan="'.$this->GetID().'" and bookingdays = 0');
        if($HolMember)
        {
            $rowuser = $HolMember->num_rows; // Die Anzahl der Member!
            $memberrow = $rowuser * 5;
            while($taskmember = $HolMember->fetch_assoc())
            {
                $IsAnswer += min($taskmember['dailyfights'], 5);
            }
        }
        return round((($IsAnswer * 100) / $memberrow));
    }

    public function GetDungeonQuest()
    {
        return $this->data['dungeonquest'];
    }

    public function SetDungeonQuest($row)
    {
        $this->data['dungeonquest'] = $row;
        $this->database->Update('dungeonquest="'.$row.'"', 'clans', 'id="'.$this->GetID().'"');
    }

    public function SetAllTaskDungeonDrop($yesterday = 0)
    {
        $result = $this->database->Select('*', 'accounts', 'clan="'.$this->GetID().'" AND bookingdays=0');
        if($result && $result->num_rows >= 3)
        {
            $itemManager = new ItemManager($this->database);
            $MaxDungeonClanFights = $result->num_rows * 10;
            $HalfDungeonFights = floor(90 * ($MaxDungeonClanFights / 100));
            $DungeonClanFights = 0;
            $received = false;
            while($row = $result->fetch_assoc())
            {
                $DungeonClanFights += $row['dungeon'];
            }
            $result->data_seek(0);
            while($members = $result->fetch_assoc())
            {
                $player = new Player($this->database, $members['id']);
                $PMManager = new PMManager($this->database, $members['id']);
                if($DungeonClanFights >= $HalfDungeonFights && $this->GetDungeonQuest() == 0 || $yesterday == 1)
                {
                    $player->SetBerry($player->GetBerry() + 10000);
                    $player->AddItems(8, 8, 1);
                    $player->AddItems(60, 60, 1);
                    $yesterdaytext = '';
                    if($yesterday != 0)
                        $yesterdaytext = ' [Für den Vortag]';
                    $pms = "Glückwunsch! Deine Bande hat erfolgreich die Quest der Dungeons absolviert! Dafür erhältst du 10.000 Berry, 1x Schatztruhe und 1x Sehr starke Medizin!";
                    $PMManager->SendPM(1, 'img/system.png', 'Support', 'Dungeon Quest' . $yesterdaytext, $pms, $player->GetName(), 1);
                    $received = true;
                }
            }
            if($received && $yesterday == 0)
            {
                $this->SetDungeonQuest(1);
            }
        }
        echo "<br/>---------------------------------------------------------------------------<br/>";
    }

    public function AllTaskISDungeon() : int
    {
        $alldungeonuser = 0;
        $HolMember = $this->database->Select('*', 'accounts', 'clan="'.$this->GetID().'" and bookingdays = 0');
        if($HolMember)
        {
            $rowuser = $HolMember->num_rows; // Die Anzahl der Member!
            $alldungeonuser = $rowuser * 10; // Die Anzahl an möglichen
        }
        return $alldungeonuser;
    }

    public function AllTaskDungeon() : int
    {
        $IsAnswer = 0;
        $HolMember = $this->database->Select('*', 'accounts', 'clan="'.$this->GetID().'" and bookingdays = 0');
        if($HolMember)
        {
            while($taskmember = $HolMember->fetch_assoc())
            {
                $IsAnswer += min($taskmember['dungeon'], 10);
            }
        }
        return $IsAnswer;
    }

    public function AllTaskDungeonPercentage() : int
    {
        $IsAnswer = 0;
        $memberrow = 0;
        $HolMember = $this->database->Select('*', 'accounts', 'clan="'.$this->GetID().'" and bookingdays = 0');
        if($HolMember)
        {
            $rowuser = $HolMember->num_rows; // Die Anzahl der Member!
            $memberrow = $rowuser * 10;
            while($taskmember = $HolMember->fetch_assoc())
            {
                $IsAnswer += min($taskmember['dungeon'], 10);
            }
        }
        return round((($IsAnswer * 100) / $memberrow));
    }

    public function SetAllTaskQuestDrops($yesterday = 0)
    {
        if($this->GetNpcQuest() == 1 && $this->GetDungeonQuest() == 1 && $this->GetEloQuest() == 1 && $this->GetPvPQuest() == 1 && $this->GetAllQuestFinish() == 0 || $yesterday == 1)
        {
            $HolUserCheck = $this->database->Select('*', 'accounts', 'clan="'.$this->GetID().'" and bookingdays = 0');
            if($HolUserCheck)
            {
                while($user = $HolUserCheck->fetch_assoc())
                {
                    $QuestPlayer = new Player($this->database, $user['id']);
                    $PMManager = new PMManager($this->database, $user['id']);
                    $QuestPlayer->SetGold($QuestPlayer->GetGold() + 5);
                    $QuestPlayer->AddItems(60, 60, 1);
                    $QuestPlayer->AddItems(8, 8, 1);


                    $yesterdaytext = '';
                    if($yesterday != 0)
                        $yesterdaytext = ' [Für den Vortag]';

                    $pms = "Herzlichen Glückwunsch! Ihr habt alle täglichen Quest der Banden erfolgreich geschafft, dafür erhaltet ihr nochmal eine extra Belohnung, jeder Spieler in der Bande erhält 5 Gold, 1x Schatztruhe und 1x Sehr starke Medizin!";
                    $PMManager->SendPM(1, 'img/system.png', 'Support', 'Alle Quests geschafft!' . $yesterdaytext, $pms, $QuestPlayer->GetName(), 1);
                }
            }
            if($yesterday == 0)
                $this->SetAllQuestFinish(1);
        }
    }

    public function GetAllQuestFinish()
    {
        return $this->data['isallquestfinish'];
    }

    public function SetAllQuestFinish($value)
    {
        $this->data['isallquestfinish'] = $value;
        $result = $this->database->Update('isallquestfinish="'.$value.'"', 'clans', 'id="'.$this->GetID().'"');
    }

    public function RemoveAllianceInvite($value)
    {
        $allianceClans = $this->GetAllianceInvites();
        if(($key = array_search($value, $allianceClans)) !== false)
        {
            unset($allianceClans[$key]);
        }
        $allianceClans = implode(";", $allianceClans);
        $this->data['allianceinvite'] = $allianceClans;
        $this->database->Update('allianceinvite="'.$allianceClans.'"', 'clans', 'id='.$this->GetID());
    }

    public function RemoveAlliance($value)
    {
        $otherClan = new Clan($this->database, $value);
        if($otherClan->IsValid())
        {
            $allianceClans = $this->GetAlliances();
            if(($key = array_search($otherClan->GetID(), $allianceClans)) !== false)
            {
                unset($allianceClans[$key]);
            }
            $allianceClans = implode(";", $allianceClans);
            $this->data['alliances'] = $allianceClans;
            $this->database->Update('alliances="'.$allianceClans.'"', 'clans', 'id='.$this->GetID());

            $otherAllianceClans = $otherClan->GetAlliances();
            if(($key = array_search($this->GetID(), $otherAllianceClans)) !== false)
            {
                unset($otherAllianceClans[$key]);
            }
            $otherAllianceClans = implode(";", $otherAllianceClans);
            $this->database->Update('alliances="'.$otherAllianceClans.'"', 'clans', 'id='.$otherClan->GetID());
        }
    }

    public function LoadMessages($data)
    {
        if ($data == '')
        {
            return;
        }
        $users = explode('@', $data);
        $i = 0;
        while (isset($users[$i]))
        {
            $msg = explode(';', $users[$i]);
            $clanmsg = new ClanLogMessage($msg);
            $this->messages[] = $clanmsg;
            ++$i;
        }
    }

    public static function FindByName($database, $name)
    {
        $result = $database->Select('id', 'clans', 'name="' . $name . '"', 1);
        if ($result)
        {
            if ($result->num_rows > 0)
            {
                $row = $result->fetch_assoc();
                return $row['id'];
            }
            $result->close();
        }
        return 0;
    }

    public function ChangeName($name, $oldName, $leaderName)
    {
        $this->SetName($name);
        $this->RemoveGold(200, $this->GetLeader(), $leaderName, 0, 'System (Änderung Bandenname)');
        $this->database->Update('name="' . $name . '"', 'clans', 'id=' . $this->GetID(), 1);
        $this->database->Update('clanname="' . $name . '"', 'accounts', 'clanname="' . $oldName . '"', 99999);
    }

    public function ChangeTag($tag)
    {
        $this->SetTag($tag);
        $this->database->Update('tag="' . $tag . '"', 'clans', 'id=' . $this->GetID(), 1);
    }

    public function PlayerJoins()
    {
        $members = $this->GetMembers();
        $members = $members + 1;
        $this->SetMembers($members);
        $this->database->Update('members="' . $members . '"', 'clans', 'id = ' . $this->GetID(), 1);
    }

    public function GetDiscord()
    {
        return $this->data['discord'];
    }

    public function SetDiscord($value)
    {
        $this->data['discord'] = $value;
    }

    public function PlayerLeaves()
    {
        $members = $this->GetMembers();
        $members = $members - 1;
        $this->SetMembers($members);
        $this->database->Update('members="' . $members . '"', 'clans', 'id = ' . $this->GetID(), 1);
    }

    public function PostShoutbox($id, $name, $text)
    {
        $date = date('d.m.Y H:i', time());
        $text = html_entity_decode($text);
        $text = str_replace(";", "", $text);
        $text = str_replace("@", "", $text);

        $msg = $date . ';' . $id . ';' . $name . ';' . $text;
        $clanmsg = new ClanShoutboxMSG(explode(';', $msg));
        array_unshift($this->shoutbox, $clanmsg);

        if ($this->data['shoutbox'] == '')
        {
            $this->data['shoutbox'] = $msg;
        }
        else
        {
            $this->data['shoutbox'] = $msg . '@' . $this->data['shoutbox'];
        }

        $num = 1000;
        $log = '';
        $msgs = array();
        if (count($this->shoutbox) > $num)
        {
            for ($i = 0; $i < $num; ++$i)
            {
                $smsg = $this->shoutbox[$i];
                if ($log == '')
                {
                    $log = $smsg->GetData();
                }
                else
                {
                    $log = $log . '@' . $smsg->GetData();
                }
                $msgs[] = $smsg;
            }
            $this->data['shoutbox'] = $log;
            $this->shoutbox = $msgs;
        }

        $this->database->Update('shoutbox="' . $this->GetShoutbox() . '", lastupdate="'.date('Y-m-d H:i:s' ,time()).'"', 'clans', 'id = ' . $this->GetID(), 1);
    }

    public function GetShoutboxMSG()
    {
        return $this->shoutbox;
    }

    public function GetShoutbox()
    {
        return $this->data['shoutbox'];
    }

    public function GetRanks()
    {
        return $this->data['ranks'];
    }

    public function GetRunningPoints() : int
    {
        return $this->data['runnpoints'];
    }

    public function SetRunningPoints($value)
    {
        $this->data['runnpoints'] = $value;
        $this->database->Update('runnpoints="'.$value.'"', 'clans', 'id="'.$this->GetID().'"');
    }

    public function GetRankPermission($rank, $perm)
    {
        $r = explode("@", $this->GetRanks());
        if($r[$rank])
        {
            $rp = explode(";", $r[$rank]);
            if($perm == "treasuresee")
                return $rp[1];
            else if($perm == "treasureedit")
                return $rp[2];
            else if($perm == "massmail")
                return $rp[3];
            else if($perm == "profiledit")
                return $rp[4];
            else if($perm == "intern")
                return $rp[5];
            else if($perm == "challenge")
                return $rp[6];
            else if($perm == "management")
                return $rp[7];
        }
        return 0;
    }

    public function SetRanks($value)
    {
        $this->data['ranks'] = $value;
        $this->database->Update('ranks="' . $value . '"', 'clans', 'id = ' . $this->GetID(), 1);
    }

    public function AddToLog($msg)
    {
        $clanmsg = new ClanLogMessage(explode(';', $msg));
        array_unshift($this->messages, $clanmsg);

        if ($this->data['log'] == '')
        {
            $this->data['log'] = $msg;
        }
        else
        {
            $this->data['log'] = $msg . '@' . $this->data['log'];
        }

        $num = 200;
        $log = '';
        $msgs = array();
        if (count($this->messages) > $num)
        {
            for ($i = 0; $i < $num; ++$i)
            {
                $smsg = $this->messages[$i];
                if ($log == '')
                {
                    $log = $smsg->GetData();
                }
                else
                {
                    $log = $log . '@' . $smsg->GetData();
                }
                $msgs[] = $smsg;
            }
            $this->data['log'] = $log;
            $this->messages = $msgs;
        }
    }

    public function GetLogMSG()
    {
        return $this->messages;
    }

    public function GetLog()
    {
        return $this->data['log'];
    }

    public function GetLevel()
    {
        return $this->data['level'];
    }

    public function SetLevel($value)
    {
        $this->data['level'] = $value;
    }

    public function GetExp()
    {
        return $this->data['exp'];
    }

    public function SetExp($value)
    {
        $this->data['exp'] = $value;
    }

    public function GetEXPPercentage()
    {
        if(round(($this->GetExp() / $this->GetRequiredExp()) * 100) > 100)
            return 100;
        else
            return round(($this->GetExp() / $this->GetRequiredExp()) * 100);
    }

    public function GetRequiredExp(): int
    {
        switch ($this->GetLevel())
        {
            case 1:
                return 250;
            case 2:
                return 500;
            case 3:
                return 1000;
            case 4:
                return 2000;
            case 5:
                return 3500;
            case 6:
                return 5500;
            case 7:
                return 7500;
            default:
                return 10000;
        }
    }

    public function GetLevelUpCostBerry($level, $isStat = true) : int
    {
        if($level == 1)
            $cost = 10000;
        else
            $cost = 0;
        for ($i = 1; $i < $level; $i++)
        {
            if($i < 9)
                $cost += 25000;
            else if($i < 19)
                $cost += 50000;
            else if($i < 29)
                $cost += 75000;
            else if($i < 39)
                $cost += 100000;
            else if($i < 49)
                $cost += 150000;
            else
                $cost += 200000;
        }
        if($isStat)
            return $cost;
        else
            return $cost * 2;
    }

    public function GetLevelUpCostGold($level, $isStat = true) : int
    {
        $cost = 0;
        if($level == 1)
            $cost = 10;

        for ($i = 1; $i < $level; $i++)
        {
            if($i < 9)
                $cost += 25;
            else if($i < 19)
                $cost += 50;
            else if($i < 29)
                $cost += 75;
            else if($i < 39)
                $cost += 100;
            else if($i < 49)
                $cost += 150;
            else
                $cost += 200;
        }
        if($isStat)
            return $cost;
        else
            return $cost * 2;
    }

    public function GetFlag()
    {
        return $this->data['flag'];
    }

    public function SetFlag($value)
    {
        $this->data['flag'] = $value;
    }

    public function GetAttack()
    {
        return $this->data['atk'];
    }

    public function SetAttack($value)
    {
        $this->data['atk'] = $value;
    }

    public function GetDefense()
    {
        return $this->data['def'];
    }

    public function SetDefense($value)
    {
        $this->data['def'] = $value;
    }

    public function GetLP()
    {
        return $this->data['lp'];
    }

    public function SetLP($value)
    {
        $this->data['lp'] = $value;
    }

    public function GetAD()
    {
        return $this->data['ad'];
    }

    public function SetAD($value)
    {
        $this->data['ad'] = $value;
    }

    public function HasApplicants()
    {
        $has = false;
        $result = $this->database->Select('*', 'accounts', 'clanapplication=' . $this->GetID(), 1);
        if ($result)
        {
            $has = $result->num_rows > 0;
            $result->close();
        }

        return $has;
    }

    public function AddBerry($berry, $byacc, $byname, $toacc, $toname)
    {
        $date = date('d.m.Y H:i', time());
        $msg = $date . ';' . $byacc . ';' . $byname . ';' . $toacc . ';' . $toname . ';' . $berry;
        $this->AddToLog($msg);


        $berry = $berry + $this->GetBerry();
        $this->database->Update('zeni=' . $berry . ', log="' . $this->GetLog() . '"', 'clans', 'id = ' . $this->GetID(), 1);
        $this->SetBerry($berry);
    }
    public function AddGold($gold, $byacc, $byname, $toacc, $toname)
    {
        $date = date('d.m.Y H:i', time());
        $msg = $date . ';' . $byacc . ';' . $byname . ';' . $toacc . ';' . $toname . ';' . $gold;
        $this->AddToLog($msg);


        $gold = $gold + $this->GetGold();
        $this->database->Update('gold=' . $gold . ', log="' . $this->GetLog() . '"', 'clans', 'id = ' . $this->GetID(), 1);
        $this->SetGold($gold);
    }
    public function RemoveGold($gold, $byacc, $byname, $toacc, $toname)
    {
        $date = date('d.m.Y H:i', time());
        $msg = $date . ';' . $byacc . ';' . $byname . ';' . $toacc . ';' . $toname . ';' . $gold;
        $this->AddToLog($msg);

        $gold = $this->GetGold() - $gold;
        $this->database->Update('gold=' . $gold . ', log="' . $this->GetLog() . '"', 'clans', 'id = ' . $this->GetID(), 1);
        $this->SetGold($gold);
    }
    public function WinGold($gold)
    {
        $byacc = $this->GetLeader();
        $byname = 'SYSTEM';
        $toacc = "";
        $toname = "Bandenkasse";
        $date = date('d.m.Y H:i', time());
        $msg = '@'.$date . ';' . $byacc . ';' . $byname . ';' . $toacc . ';' . $toname . ';' . $gold;
        $this->AddToLog($msg);

        $gold = $gold + $this->GetGold();
        $this->database->Update('gold=' . $gold . ', log="' . $this->GetLog() . '"', 'clans', 'id = ' . $this->GetID(), 1);
        $this->SetGold($gold);
    }

    public function RemoveBerry($berry, $byacc, $byname, $toacc, $toname)
    {
        $date = date('d.m.Y H:i', time());
        $msg = $date . ';' . $byacc . ';' . $byname . ';' . $toacc . ';' . $toname . ';' . $berry;
        $this->AddToLog($msg);

        $berry = $this->GetBerry() - $berry;
        $this->database->Update('zeni=' . $berry . ', log="' . $this->GetLog() . '"', 'clans', 'id = ' . $this->GetID(), 1);
        $this->SetBerry($berry);
    }

    public function Change($image, $flag, $banner, $interntext, $text, $rules, $requirements, $discord, $paysbounty)
    {
        $image = $this->database->EscapeString($image);
        $flag = $this->database->EscapeString($flag);
        $banner = $this->database->EscapeString($banner);
        $text = $this->database->EscapeString($text);
        $interntext = $this->database->EscapeString($interntext);
        $rules = $this->database->EscapeString($rules);
        $requirements = $this->database->EscapeString($requirements);
        $discord = $this->database->EscapeString($discord);
        $this->SetImage($image);
        $this->SetFlag($flag);
        $this->SetBanner($banner);
        $this->SetText($text);
        $this->SetInternText($interntext);
        $this->SetRules($rules);
        $this->SetRequirements($requirements);
        $this->SetDiscord($discord);
        if ($paysbounty == "on")
            $this->SetPaysBounty(1);
        else
            $this->SetPaysBounty(0);
        $this->database->Update('image="' . $image . '", flag="'.$flag.'", banner="' . $banner . '",interntext="'.$interntext.'",text="' . $text . '",rules="' . $rules . '",requirements="' . $requirements . '",discord="' . $discord . '",paysbounty="' . $this->PaysBounty() . '"', 'clans', 'id = ' . $this->GetID(), 1);
    }

    public function MakeLeader($player)
    {
        $this->database->Update('clanrank=2', 'accounts', 'id='.$this->GetLeader());
        $this->database->Update('leader=' . $player->GetID(), 'clans', 'id = ' . $this->GetID(), 1);
        $this->database->Update('clanrank=0', 'accounts', 'id = ' . $player->GetID(), 1);
        $this->SetLeader($player->GetID());
    }

    public function Delete($player)
    {
        $result = $this->database->Select('*', 'accounts', 'clan=' . $this->GetID() . ' AND id != ' . $player->GetID());
        if($result && $result->num_rows > 0)
        {
            $CoLeader = array();
            $Member = array();
            $AllMembers = array();
            while($row = $result->fetch_assoc())
            {
                $AllMembers[] = $row['id'];
                if($row['clanrank'] == 1) {
                    $CoLeader[] = $row['id'];
                }
                else
                {
                    $Member[] = $row['id'];
                }
            }

            if(sizeof($CoLeader) > 0)
                $NewLeader = $CoLeader[rand(0, sizeof($CoLeader) - 1)];
            else
                $NewLeader = $Member[rand(0,sizeof($Member) - 1)];



            $this->database->Update('clanrank=0', 'accounts', 'id='.$NewLeader);
            $this->database->Update('members=members-1, leader='.$NewLeader, 'clans', 'id='.$this->GetID());
            $this->database->Update('clanrank=0,clan=0,clanname=""', 'accounts', 'id = ' . $player->GetID());
            $newPlayer = new Player($this->database, $NewLeader);
            $PMManager = new PMManager($this->database, $newPlayer->GetID());
            $text = "<div style='text-align:center;'>Der bisherige Bandenleiter <a href='?p=profil&id=" . $player->GetID() ."'>" . $player->GetName() . "</a> hat die Bande verlassen,<br/>Du wurdest durch das System als neuer Leiter der Bande bestimmt.</div>";
            $PMManager->SendPM(649, 'img/system.png', $this->GetName(), 'Du bist nun der Bandenleiter', $text, $newPlayer->GetName(), 1);

            $i = 0;
            while(isset($AllMembers[$i]))
            {
                if($AllMembers[$i] != $newPlayer->GetID())
                {
                    $bandenMember = new Player($this->database, $AllMembers[$i]);
                    $text = "<div style='text-align:center;'>Der bisherige Bandenleiter <a href='?p=profil&id=" . $player->GetID() . "'>" . $player->GetName() . "</a> hat die Bande verlassen,<br/>" . $newPlayer->GetName() . " durch das System als neuer Leiter der Bande bestimmt.</div>";
                    $PMManager->SendPM(649, 'img/system.png', $this->GetName(), 'Es wurde ein neuer Bandenleiter bestimmt', $text, $bandenMember->GetName(), 1);
                }
                $i++;
            }
        }
        else
        {
            $this->database->Delete('clans', 'id = ' . $this->GetID(), 1);
            $this->database->Update('clanrank=0,clan="0",clanname=""', 'accounts', 'id = ' . $player->GetID(), 1);
            $this->database->Update('territorium=0, gewinn=0, sieger="", blocked=0', 'places', 'territorium = ' . $this->GetID());
            $player->SetClan(0);
            $player->SetClanName('');
        }
    }

    public function IsValid()
    {
        return $this->valid;
    }

    public function GetID()
    {
        return $this->data['id'];
    }

    public function GetName()
    {
        return $this->data['name'];
    }

    public function SetName($value)
    {
        $this->data['name'] = $value;
    }

    public function GetMembers()
    {
        return $this->data['members'];
    }

    public function GetMaxMembers()
    {
        if(floor(15 + $this->GetLevel() / 2) < 20)
            return floor(15 + $this->GetLevel() / 2);
        else
            return 20;
    }

    public function GetActivityPoints()
    {
        return $this->data['activitypoints'];
    }

    public function SetActivityPoints($value)
    {
        $this->data['activitypoints'] = $value;
    }

    public function SetMembers($value)
    {
        $this->data['members'] = $value;
    }

    public function PaysBounty()
    {
        return $this->data['paysbounty'];
    }

    public function SetPaysBounty($value)
    {
        $this->data['paysbounty'] = $value;
    }

    public function GetTag()
    {
        return $this->data['tag'];
    }

    public function SetTag($value)
    {
        $this->data['tag'] = $value;
    }

    public function GetText()
    {
        return $this->data['text'];
    }

    public function SetText($value)
    {
        $this->data['text'] = $value;
    }

    public function GetInternText()
    {
        return $this->data['interntext'];
    }

    public function SetInternText($value)
    {
        $this->data['interntext'] = $value;
    }

    public function GetRules()
    {
        return $this->data['rules'];
    }

    public function SetRules($value)
    {
        $this->data['rules'] = $value;
    }

    public function GetRequirements()
    {
        return $this->data['requirements'];
    }

    public function SetRequirements($value)
    {
        $this->data['requirements'] = $value;
    }

    public function GetLeader()
    {
        return $this->data['leader'];
    }


    public function SetLeader($value)
    {
        $this->data['leader'] = $value;
    }

    public function GetCoLeader(): array
    {
        $result = $this->database->Select('*', 'accounts', 'clan='.$this->GetID().', clanrank=1');
        $coLeader = array();
        if($result && $result->num_rows)
        {
            while($row = $result->fetch_assoc())
            {
                $coLeader[] = $row['id'];
            }
        }
        return $coLeader;
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

    public function GetChallengeCountdown()
    {
        $timestamp = strtotime($this->GetChallengeTime());
        $currentTime = strtotime("now");
        $difference = $timestamp - $currentTime;
        if ($difference < 0)
            $difference = 0;
        return $difference;
    }

    public function SetChallengeTime($value)
    {
        $this->data['challengedtime'] = $value;
    }

    public function GetBerry()
    {
        return $this->data['zeni'];
    }

    public function GetGold()
    {
        return $this->data['gold'];
    }

    public function SetBerry($value)
    {
        $this->data['zeni'] = $value;
    }

    public function SetGold($value)
    {
        $this->data['gold'] = $value;
    }

    public function GetPoints()
    {
        return $this->data['memberki'];
    }

    public function SetPoints($value)
    {
        $this->data['memberki'] = $value;
    }

    public function GetImage()
    {
        return $this->data['image'];
    }

    public function SetImage($value)
    {
        $this->data['image'] = $value;
    }

    public function GetBanner()
    {
        return $this->data['banner'];
    }

    public function SetBanner($value)
    {
        $this->data['banner'] = $value;
    }

    public function GetLastUpdate()
    {
        return $this->data['lastupdate'];
    }

    private function LoadData($id, $select = '*')
    {
        $result = $this->database->Select($select, 'clans', 'id=' . $id, 1);
        if ($result)
        {
            if ($result->num_rows > 0)
            {
                $row = $result->fetch_assoc();
                $this->data = $row;
                $this->valid = true;
                $this->LoadMessages($row['log']);
                $this->LoadShoutbox($row['shoutbox']);
            }
            $result->close();
        }
    }
}
