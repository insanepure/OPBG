<?php
if($database == NULL)
{
    print 'This File ('.__FILE__.') should be after Database!';
}

class VerzeichnisEntry
{
    private $data;
    private $database;

    function __construct($db, $initialData)
    {
        $this->data = $initialData;
        $this->database = $db;
    }

    public function GetLikes()
    {
        return $this->data['likes'];
    }

    public function SetLikes($value)
    {
        $value = '';
        if($this->data['likes'] != '')
        {
            $value = $this->data['likes'].';'.$value;
        }
        else
        {
            $value = $value;
        }

        $this->database->Update('likes="'.$value.'"', 'verzeichnis', 'id="'.$this->GetID().'"');
    }

    public function GetDislikes()
    {
        return $this->data['dislikes'];
    }

    public function SetDislikes($value)
    {
        if($this->data['likes'] != '')
        {
            $value = $this->data['dislikes'].';'.$value;
        }
        else
        {
            $value = $value;
        }

        $this->database->Update('dislikes="'.$value.'"', 'verzeichnis', 'id="'.$this->GetID().'"');
    }

    function GetID()
    {
        return $this->data['id'];
    }

    public function GetUID()
    {
        return $this->data['uid'];
    }

    function GetName()
    {
        return $this->data['name'];
    }

    function GetOriginalName()
    {
        return $this->data['romaji'];
    }

    public function Activate(): void
    {
        $this->data['status'] = 0;
        $this->database->Update('status=0', 'verzeichnis', 'id="'.$this->GetID().'"');
    }

    public function DeleteEdit($uid): void
    {
        $this->data['status'] = 0;
        $this->database->Delete('verzeichnis_edit', 'uid="'.$uid);
    }

    public function IsActivated(): bool
    {
        return $this->data['status'] == 0 || $this->data['status'] == 2;
    }

    function GetImage()
    {
        return $this->data['image'];
    }

    function GetAllIncludes()
    {
        $VerzeichnisCheck = $this->database->Select('*', 'verzeichnis', 'status=0');
        $sum = $VerzeichnisCheck->num_rows;
        return $sum;
    }

    function GetVoiceActorGer()
    {
        return $this->data['voiceactorger'];
    }

    function GetVoiceActorJap()
    {
        return $this->data['voiceactorjap'];
    }

    function GetRasse()
    {
        return $this->data['rasse'];
    }

    function GetBirthDay()
    {
        return $this->data['birthday'];
    }

    function GetPvP()
    {
        return $this->data['kopfgeld'];
    }

    function GetAlter()
    {
        return $this->data['alter'];
    }

    function GetPiratenbande()
    {
        return $this->data['piratenbande'];
    }

    function GetPosition()
    {
        return $this->data['position'];
    }

    function GetGeschlecht()
    {
        return $this->data['geschlecht'];
    }

    function GetBirthPlace()
    {
        return $this->data['herkunft'];
    }

    function GetHeight()
    {
        return $this->data['groesse'];
    }

    function GetWeight()
    {
        return $this->data['weight'];
    }

    public function GetTF()
    {
        return $this->data['teufelsfrucht'];
    }

    function GetFamily()
    {
        return $this->data['family'];
    }

    function GetAnime()
    {
        return $this->data['anime'];
    }

    function GetManga()
    {
        return $this->data['manga'];
    }

    function GetDescription()
    {
        return $this->data['description'];
    }
}

class Verzeichnis
{
    private $database;

    function __construct($db)
    {
        $this->database = $db;
    }

    function LoadEntryName($name) : ?VerzeichnisEntry
    {
        $name = $this->database->EscapeString($name);
        $entry = null;
        $result = $this->database->Select('*','verzeichnis','name="'.$name.'"', 1);
        if ($result)
        {
            $row = $result->fetch_assoc();
            if(isset($row))
            {
                $entry = new VerzeichnisEntry($this->database, $row);
            }
            $result->close();
        }

        return $entry;
    }

    function LoadEntry($id) : ?VerzeichnisEntry
    {
        $entry = null;
        $result = $this->database->Select('*','verzeichnis','id='.$id);
        if ($result)
        {
            $row = $result->fetch_assoc();
            if(isset($row))
            {
                $entry = new VerzeichnisEntry($this->database, $row);
            }
            $result->close();
        }
        return $entry;
    }

    function LoadEditEntry($id) : ?VerzeichnisEntry
    {
        $entry = null;
        $result = $this->database->Select('*','verzeichnis_edit','uid="'.$id.'"');
        if ($result)
        {
            $row = $result->fetch_assoc();
            if(isset($row))
            {
                $entry = new VerzeichnisEntry($this->database, $row);
            }
            $result->close();
        }
        return $entry;
    }

    public function VerifyEdit($id): void
    {
        $id = $this->database->EscapeString($id);
        $result = $this->database->Select('*', 'verzeichnis_edit', 'uid="'.$id.'"');
        if($result && $result->num_rows > 0)
        {
            $verzeichnis = $result->fetch_assoc();
            $update = "
                    `image` = '" . $this->database->EscapeString($verzeichnis['image']) . "',
                    `name` = '" . $this->database->EscapeString($verzeichnis['name']) . "',
                    `romaji` = '" . $this->database->EscapeString($verzeichnis['romaji']) . "',
                    `teufelsfrucht` = '" . $this->database->EscapeString($verzeichnis['teufelsfrucht']) . "',
                    `rasse` = '" . $this->database->EscapeString($verzeichnis['rasse']) . "',
                    `geschlecht` = '" . $this->database->EscapeString($verzeichnis['geschlecht']) . "',
                    `alter` = '" . $this->database->EscapeString($verzeichnis['alter']) . "',
                    `birthday` = '" . $this->database->EscapeString($verzeichnis['birthday']) . "',
                    `groesse` = '" . $this->database->EscapeString($verzeichnis['groesse']) . "',
                    `herkunft` = '" . $this->database->EscapeString($verzeichnis['herkunft']) . "',
                    `family` = '" . $this->database->EscapeString($verzeichnis['family']) . "',
                    `piratenbande` = '" . $this->database->EscapeString($verzeichnis['piratenbande']) . "',
                    `position` = '" . $this->database->EscapeString($verzeichnis['position']) . "',
                    `voiceactorger` = '" . $this->database->EscapeString($verzeichnis['voiceactorger']) . "',
                    `voiceactorjap` = '" . $this->database->EscapeString($verzeichnis['voiceactorjap']) . "',
                    `description` = '" . $this->database->EscapeString($verzeichnis['description']) . "',
                    `anime` = '" . $this->database->EscapeString($verzeichnis['anime']) . "',
                    `manga` = '" . $this->database->EscapeString($verzeichnis['manga']) . "',
                    `kopfgeld` = '" . $this->database->EscapeString($verzeichnis['kopfgeld']) . "',
                    `last_editor` = '" . $this->database->EscapeString($verzeichnis['last_editor']) . "'
                 ";
            $this->database->Update($update, 'verzeichnis', 'uid="' . $id . '"');
            $this->database->Delete('verzeichnis_edit', "uid='".$id."'");
        }
    }

    public function Activate($id): void
    {
        $id = $this->database->EscapeString($id);
        $result = $this->database->Select('*', 'verzeichnis_new', 'uid="'.$id.'"');
        if($result && $result->num_rows > 0)
        {
            $verzeichnis = $result->fetch_assoc();
            $variables = "
                    `image`,
                    `name`,
                    `romaji`,
                    `teufelsfrucht`,
                    `rasse`,
                    `geschlecht`,
                    `alter`,
                    `birthday`,
                    `groesse`,
                    `herkunft`,
                    `family`,
                    `piratenbande`,
                    `position`,
                    `voiceactorger`,
                    `voiceactorjap`,
                    `description`,
                    `anime`,
                    `manga`,
                    `kopfgeld`,
                    `last_editor`,
                    `uid`,
                    `mainpage`,
                    `creator`
                 ";
            $values = "
                    '".$this->database->EscapeString($verzeichnis['image'])."',
                    '".$this->database->EscapeString($verzeichnis['name'])."',
                    '".$this->database->EscapeString($verzeichnis['romaji'])."',
                    '".$this->database->EscapeString($verzeichnis['teufelsfrucht'])."',
                    '".$this->database->EscapeString($verzeichnis['rasse'])."',
                    '".$this->database->EscapeString($verzeichnis['geschlecht'])."',
                    '".$this->database->EscapeString($verzeichnis['alter'])."',
                    '".$this->database->EscapeString($verzeichnis['birthday'])."',
                    '".$this->database->EscapeString($verzeichnis['groesse'])."',
                    '".$this->database->EscapeString($verzeichnis['herkunft'])."',
                    '".$this->database->EscapeString($verzeichnis['family'])."',
                    '".$this->database->EscapeString($verzeichnis['piratenbande'])."',
                    '".$this->database->EscapeString($verzeichnis['position'])."',
                    '".$this->database->EscapeString($verzeichnis['voiceactorger'])."',
                    '".$this->database->EscapeString($verzeichnis['voiceactorjap'])."',
                    '".$this->database->EscapeString($verzeichnis['description'])."',
                    '".$this->database->EscapeString($verzeichnis['anime'])."',
                    '".$this->database->EscapeString($verzeichnis['manga'])."',
                    '".$this->database->EscapeString($verzeichnis['kopfgeld'])."',
                    '".$this->database->EscapeString($verzeichnis['last_editor'])."',
                    '".$this->database->EscapeString($verzeichnis['uid'])."',
                    '".$this->database->EscapeString($verzeichnis['mainpage'])."',
                    '".$this->database->EscapeString($verzeichnis['creator'])."'
                ";
            $this->database->Insert($variables, $values, 'verzeichnis');
            $this->database->Delete('verzeichnis_new', 'uid="'.$id.'"');
        }
    }

    public function DeleteEdit($uid): void
    {
        $uid = $this->database->EscapeString($uid);
        $this->database->Delete('verzeichnis_edit', 'uid="'.$uid.'"');
    }

    public function DeleteNewEntry($id): void
    {
        $id = $this->database->EscapeString($id);
        $this->database->Delete('verzeichnis_new', 'uid="'.$id.'"');
    }
}
