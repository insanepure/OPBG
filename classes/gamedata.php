<?php
if ($database == NULL)
{
    print 'This File (' . __FILE__ . ') should be after Database!';
}

class GameData
{
    private $database;
    private $playerOnline;
    private $playerTotal;
    private $playerUniqueOnline;
    private $playerUniqueTotal;
    private $clans;

    function __construct($db)
    {
        $this->database = $db;
        $this->LoadOnline();
        $this->LoadTotal();
        $this->LoadClans();
        $this->LoadUniqueTotal();
        $this->LoadUniqueOnline();
    }

    private function LoadOnline()
    {
        $timeOut = 15;
        $where = 'deleted = 0 AND TIMESTAMPDIFF(MINUTE, lastaction, NOW()) < ' . $timeOut;
        $result = $this->database->Select('COUNT(onlinestatus) as total', 'accounts', $where);
        if ($result)
        {
            $row = $result->fetch_assoc();
            $this->playerOnline = $row['total'];
            $result->close();
        }
    }

    private function LoadTotal()
    {
        $result = $this->database->Select('COUNT(id) as total', 'accounts', '');
        if ($result)
        {
            $row = $result->fetch_assoc();
            $this->playerTotal = $row['total'];
            $result->close();
        }
    }

    private function LoadUniqueOnline()
    {
        $timeOut = 15;
        $where = 'onlinestatus = 1 AND TIMESTAMPDIFF(MINUTE, lastaction, NOW()) < ' . $timeOut;
        $result = $this->database->Select('COUNT(DISTINCT userid) as total', 'accounts', $where);
        if ($result)
        {
            $row = $result->fetch_assoc();
            $this->playerUniqueOnline = $row['total'];
            $result->close();
        }
    }

    private function LoadUniqueTotal()
    {
        $result = $this->database->Select('COUNT(DISTINCT userid) as total', 'accounts', '');
        if ($result)
        {
            $row = $result->fetch_assoc();
            $this->playerUniqueTotal = $row['total'];
            $result->close();
        }
    }

    private function LoadClans()
    {
        $result = $this->database->Select('COUNT(id) as total', 'clans', '');
        if ($result)
        {
            $row = $result->fetch_assoc();
            $this->clans = $row['total'];
            $result->close();
        }
    }

    public function GetOnline()
    {
        return $this->playerOnline;
    }

    public function GetTotal()
    {
        return $this->playerTotal;
    }

    public function GetClans()
    {
        return $this->clans;
    }

    public function GetUniqueOnline()
    {
        return $this->playerUniqueOnline;
    }

    public function GetUniqueTotal()
    {
        return $this->playerUniqueTotal;
    }

    public function GetActiveGuests()
    {
        $result = $this->database->Select('*', 'counter', 'MINUTE(TIMEDIFF(NOW(), visit)) < 5');
        if($result && $result->num_rows > 0)
        {
            return $result->num_rows;
        }
        return 0;
    }

    public function GetAllGuests()
    {
        $result = $this->database->Select('*', 'counter');
        if($result && $result->num_rows > 0)
        {
            return $result->num_rows;
        }
        return 0;
    }

    public function GetGuestRecord()
    {
        $result = $this->database->Select('DATE(visit) AS date, COUNT(DISTINCT ip) AS visitors', 'counter', 'visit >= DATE_SUB(NOW(), INTERVAL 24 HOUR)', 1, 'visitors', 'DESC', '', 'date');
        if($result && $result->num_rows > 0)
        {
            $row = $result->fetch_assoc();
            return array($row['date'], $row['visitors']);
        }
        return false;
    }

    public function CheckGuest($ip, $session_id): bool
    {
        $result = $this->database->Select('*', 'counter', 'ip="'.hash("sha256", ($session_id.$ip)).'" AND HOUR(TIMEDIFF(NOW(), visit)) < 6', 1, 'id', 'DESC');
        if($result && $result->num_rows > 0)
        {
            $row = $result->fetch_assoc();
            $this->database->Update('visit=now()', 'counter', 'id='.$row['id']);
            return true;
        }
        $this->database->Insert('ip, visit', '"'.hash("sha256", ($session_id.$ip)).'", NOW()', 'counter');
        return true;
    }

    public function GetBdayUsersCount(): int
    {
        $bday = 0;
        $CheckBDayUser = $this->database->Select('*', 'accounts', '');
        while($user = $CheckBDayUser->fetch_assoc())
        {
            $today = date("m-d");
            $day = explode("-", $user['bday']);
            $isbday = $day[1]."-".$day[2];
            if($isbday == $today) {
                $bday++;
            }
        }
        return $bday;
    }

    public function GetAllBDayUsers()
    {
        $CheckBDayUser = $this->database->Select('*', 'accounts', '');
        while($user = $CheckBDayUser->fetch_assoc())
        {
            $today = date("m-d");
            $day = explode("-", $user['bday']);
            $isbday = $day[1]."-".$day[2];
            if($isbday == $today) {
                echo "<a target='_blank' href='?p=profil&id=" . $user['id'] . "'>" . $user['name'] . "</a>, ";
            }

        }
    }
}
$gameData = new GameData($database);
