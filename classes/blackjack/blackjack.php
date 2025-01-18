<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
if ($database == NULL)
{
    print 'This File (' . __FILE__ . ') should be after Database!';
}

class BlackJack
{
    private $database;
    private $data;

    function __construct($db, $acc)
    {
        $acc = $db->EscapeString($acc);
        $this->database = $db;
        $this->LoadData($acc);
    }

    public function GetID()
    {
        return $this->data['id'];
    }

    public function GetAcc()
    {
        return $this->data['acc'];
    }

    public function GetHand()
    {
        return $this->data['hand'];
    }

    public function GetDealer()
    {
        return $this->data['dealer'];
    }

    public function GetEnd()
    {
        return $this->data['end'];
    }

    public function MyPoints()
    {
        $myPoints = 0;
        for($i = 1; $i <= 52; $i++)
        {
            $test = explode(";", $this->GetHand());
            if(in_array($i, $test))
            {
                $punkte = 0;
                if($i == 1  || $i == 14 || $i == 27 || $i == 40)
                {
                    $punkte = 2;
                }
                else if($i == 2 || $i == 15 || $i == 28 || $i == 41)
                {
                    $punkte = 3;
                }
                else if($i == 3 || $i == 16 || $i == 29 || $i == 42)
                {
                    $punkte = 4;
                }
                else if($i == 4 || $i == 17 || $i == 30 || $i == 43)
                {
                    $punkte = 5;
                }
                else if($i == 5 || $i == 18 || $i == 31 || $i == 44)
                {
                    $punkte = 6;
                }
                else if($i == 6 || $i == 19 || $i == 32 || $i == 45)
                {
                    $punkte = 7;
                }
                else if($i == 7 || $i == 20 || $i == 33 || $i == 46)
                {
                    $punkte = 8;
                }
                else if($i == 8 || $i == 21 || $i == 34 || $i == 47)
                {
                    $punkte = 9;
                }
                else if($i == 9 || $i == 22 || $i == 35 || $i == 47 || $i == 10 || $i == 23 || $i == 36 || $i == 48 || $i == 11 || $i == 24 || $i == 38 || $i == 49 || $i == 12 || $i == 37 || $i == 50 || $i == 25 || $i == 38 || $i == 51)
                {
                    $punkte = 10;
                }
                else
                {
                    $punkte = 11;
                }
                $myPoints += $punkte;
            }
        }
        return $myPoints;
    }

    public function SetTheEnd($value)
    {
        $this->database->Update('end="'.$value.'"', 'blackjack', 'acc="'.$this->GetAcc().'"');
    }

    public function GetWin()
    {
        $this->data['win'];
    }

    public function Draw()
    {
        $card = rand(1, 52);
        return $card;
    }

    public function SetDraw()
    {
        $update = $this->GetHand().";".$this->Draw();
        $this->database->Update('hand="'.$update.'"', 'blackjack', 'acc="'.$this->GetAcc().'"');
    }

    public function OpenTheGame($id)
    {
        $handcardone = $this->Draw();
        $handcardtwo = $this->Draw();
        $hand = $handcardone.";".$handcardtwo;
            $dealerhandone = $this->Draw();
        $dealerhandtwo = $this->Draw();
        $dealerhand = $dealerhandone.";".$dealerhandtwo;
        $win = 0;
        $this->database->Insert('acc, hand, dealer, win', '"'.$id.'", "'.$hand.'", "'.$dealerhand.'", "'.$win.'"', 'blackjack');


    }

    private function LoadData($acc)
    {
        $result = $this->database->Select('*', 'blackjack', 'acc="'.$acc.'" AND end=0');
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
    }

}
