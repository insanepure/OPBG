<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
if ($database == NULL)
{
    print 'This File (' . __FILE__ . ') should be after Database!';
}

Class fussballwetten
{
    private $database;
    private $data;

    function __construct($db, $id)
    {
        $this->database = $db;
        $this->LoadData($id);
    }

    public function GetAusgezahlt()
    {
        return $this->data['ausgezahlt'];
    }

    public function SetAusgezahlt($value)
    {
        $this->database->Update('ausgezahlt="'.$value.'"', 'fussballwetten', 'id="'.$this->GetID().'"');
    }

    public function CreateGame($mannschaftz, $logoz, $mannschaftg, $logog, $starttime, $startdate, $extradate)
    {
       $mannschaftz = $this->database->Escape_String($mannschaftz);
        $logoz = $this->database->Escape_String($logoz);
        $mannschaftg = $this->database->Escape_String($mannschaftg);
        $logog = $this->database->Escape_String($logog);
        $starttime = $this->database->Escape_String($starttime);
        $startdate = $this->database->Escape_String($startdate);
        $extradate = $this->database->Escape_String($extradate);

        $this->database->Insert('mannschaftz, flaggez, mannschaftg, flaggeg, ergebnis, einsatz, teilnehmer, start, datum, beginn', '"'.$mannschaftz.'", "'.$logoz.'", "'.$mannschaftg.'", "'.$logog.'", "0:0", 0, "", "'.$starttime.'", "'.$startdate.'", "'.$extradate.'"', 'fussballwetten');
    }

    public function GetTipper($tipper)
    {
        $spieler = explode(';', $this->GetTeilnehmer());
          if(!in_array($tipper, $spieler[0]))
          {
              return true;
          }
          else
          {
              return false;
          }

    }

    public function GetTime()
    {
        return $this->data['start'];
    }

    public function SetTime($value)
    {

        $this->database->Update('start="'.$value.'"', 'fussballwetten', 'id="'.$this->GetID().'"');
    }

    public function GetID()
    {
        return $this->data['id'];
    }

    public function GetDatum()
    {
        return $this->data['datum'];
    }

    public function SetDatum($value)
    {
        $this->database->Update('datum="'.$value.'"', 'fussballwetten', 'id="'.$this->GetID().'"');
    }

    public function GetFlaggeZ()
    {
        return $this->data['flaggez'];
    }

    public function SetFlaggeZ($value)
    {
        $this->database->Update('flaggez="'.$value.'"', 'fussballwetten', 'id="'.$this->GetID().'"');
    }

    public function GetFlaggeG()
    {
        return $this->data['flaggeg'];
    }

    public function SetFlaggeG($value)
    {
        $this->database->Update('flaggeg="'.$value.'"', 'fussballwetten', 'id="'.$this->GetID().'"');
    }

    public function GetBeginn()
    {
        return $this->data['beginn'];
    }

    public function SetBeginn($value)
    {
        $this->database->Update('beginn="'.$value .'"', 'fussballwetten', 'id="'.$this->GetID().'"');
    }

    public function GetMannschaftZ()
    {
        return $this->data['mannschaftz'];
    }

    public function SetMannschaftZ($value)
    {
      $this->database->Update('mannschaftz="'.$value.'"', 'fussballwetten', 'id="'.$this->GetID().'"');
    }

    public function GetMannschaftG()
    {
        return $this->data['mannschaftg'];
    }

    public function SetMannschaftG($value)
    {
        $this->database->Update('mannschaftg="'.$value.'"', 'fussballwetten', 'id="'.$this->GetID().'"');
    }

    public function GetErgebnis()
    {
        return $this->data['ergebnis'];
    }

    public function SetErgebnis($value)
    {
        $this->database->Update('ergebnis="'.$value.'"', 'fussballwetten', 'id="'.$this->GetID().'"');
    }

    public function GetEinsatz()
    {
        return $this->data['einsatz'];

    }

    public function SetEinsatz($value)
    {
        $this->database->Update('einsatz="'.$value.'"', 'fussballwetten', 'id="'.$this->GetID().'"');
    }

    public function GetTeilnehmer()
    {
        return $this->data['teilnehmer'];
    }

    public function SetTeilnehmer($value, $ergebnis)
    {
        $spieler = '';
        if($this->data['teilnehmer'] == '')
        {
            $spieler = $value.'@'.''.$ergebnis.';';
        }
        else
        {
            $spieler = $this->data['teilnehmer'].''.$value.'@'.$ergebnis.';';
        }
        $this->database->Update('teilnehmer="'.$spieler.'"', 'fussballwetten', 'id="'.$this->GetID().'"');
    }

    public function LoadData($id)
    {
    $result = $this->database->Select('*', 'fussballwetten', 'id="'.$id.'"', 1);
    if($result)
    {
        if ($result->num_rows > 0)
        {
            $row = $result->fetch_assoc();
            $this->data = $row;
        }
        $result->close();
    }
    }
}






?>