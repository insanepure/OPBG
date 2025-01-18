<?php
if ($database == NULL)
{
    print 'This File (' . __FILE__ . ') should be after Database!';
}

class Ticket
{
    private array $data;
    private $database;

    function __construct($db, $data)
    {
        $this->data = $data;
        $this->database = $db;
    }

    public function GetID()
    {
        return $this->data['id'];
    }

    public function GetCreator()
    {
        return $this->data['ersteller'];
    }

    public function GetVerlauf()
    {
        return $this->data['verlauf'];
    }

    public function SetVerlauf($value)
    {
        $value = $this->database->EscapeString($value . $this->GetVerlauf());
        $this->data['verlauf'] = $value;
        $this->database->Update('verlauf="'.$value.'"', 'ticket', 'id='.$this->GetID());
    }

    public function GetActive()
    {
        return $this->data['active'];
    }

    public function UpdateRead($value)
    {
        $this->data['gelesen'] = $value;
        $this->database->Update('gelesen="'.$value.'"', 'ticket', 'id="'.$this->GetID().'"');
    }

    public function DeleteTicket()
    {
        $this->database->Delete('ticket', 'id='.$this->GetID());
    }

    public function TicketClose($id)
    {
        $this->data['active'] = 1;
        $this->data['gelesen'] = 0;
        $this->database->Update('active=1, gelesen=0, closedby='.$id, 'ticket', 'id='.$this->GetID());
    }

    public function TicketOpen()
    {
        $this->data['active'] = 0;
        $this->database->Update('active=0', 'ticket', 'id='.$this->GetID());
    }
}