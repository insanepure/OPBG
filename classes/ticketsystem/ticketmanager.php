<?php
include_once $_SERVER['DOCUMENT_ROOT'].'classes/ticketsystem/ticket.php';
if ($database == NULL)
{
    print 'This File (' . __FILE__ . ') should be after Database!';
}

class TicketManager
{
    private $database;
    private array $tickets;

    function __construct($db)
    {
        $this->database = $db;
        $this->tickets = array();
        $this->LoadData();
    }

    private function LoadData() : void
    {
        $result = $this->database->Select('*', 'ticket');
        if($result && $result->num_rows > 0)
        {
            while($row = $result->fetch_assoc())
            {
                $ticket = new Ticket($this->database, $row);
                $this->tickets[] = $ticket;
            }
        }
        $result->close();
    }

    public function HasOpenTickets($id): bool
    {
        $id = $this->database->EscapeString($id);
        $result = $this->database->Select('*', 'ticket', 'ersteller='.$id.', active=0');
        if($result && $result->num_rows > 0) return true;
        return false;
    }

    public function GetOpenTickets(): array
    {
        $result = $this->database->Select('*', 'ticket', 'active=0');
        if($result && $result->num_rows > 0)
        {
            $ticketData =  array();
            while($row = $result->fetch_assoc())
            {
                $ticketData[] = $row;
            }
            return $ticketData;
        }
        return array();
    }

    public function NumOpenTickets(): int
    {
        $i = 0;
        $open = 0;
        while(isset($this->tickets[$i]))
        {
            if($this->tickets[$i]->GetActive() == 0)
                $open++;
            $i++;
        }
        return $open;
    }

    public function GetTicket($id) : ?Ticket
    {
        $i = 0;
        while(isset($this->tickets[$i]))
        {
            if($this->tickets[$i]->GetID() == $id)
            {
                return $this->tickets[$i];
            }
            $i++;
        }
        return null;
    }

    public function CreateTicket($id, $betreff, $text)
    {
        $this->database->Insert('ersteller, erstellt, betreff, openmessage, verlauf, active, gelesen', $id.', "'.date("d.m.Y - H:i").'" , "'.$betreff.'", "'.$text.'", "'.$text.'", 0, 1', 'ticket');
    }
}