<?php
if ($database == NULL)
{
    print 'This File (' . __FILE__ . ') should be after Database!';
}

class SideStory
{
    private $database;
    private $data;

    function __construct($db, $id)
    {
        $this->database = $db;
        $this->LoadData($id);
    }

    public function GetID()
    {
        return $this->data['id'];
    }

    public function GetTitel()
    {
        return $this->data['titel'];
    }

    public function GetLevelup()
    {
        return $this->data['levelup'];
    }
    public function GetBerry()
    {
        return $this->data['zeni'];
    }
    public function GetGold()
    {
        return $this->data['gold'];
    }
    public function GetSkillpoints()
    {
        return $this->data['skillpoints'];
    }
    public function GetPvP()
    {
        return $this->data['kopfgeld'];
    }
    public function GetItems()
    {
        if ($this->data['items'] == '')
            return null;
        return explode(';', $this->data['items']);
    }

    public function GetType()
    {
        return $this->data['type'];
    }

    public function GetAction()
    {
        return $this->data['action'];
    }

    public function GetPlace()
    {
        return $this->data['place'];
    }

    public function GetPlanet()
    {
        return $this->data['planet'];
    }

    public function GetStatsPlus()
    {
        return $this->data['stats'];
    }

    public function GetTalkNPC()
    {
        return $this->data['talknpc'];
    }

    public function GetNPCs()
    {
        if ($this->data['npcs'] == '')
            return array();
        return explode(';', $this->data['npcs']);
    }

    public function GetSupportNPCs()
    {
        if ($this->data['supportnpcs'] == '')
            return array();
        return explode(';', $this->data['supportnpcs']);
    }

    public function GetNPCsCount()
    {
        if ($this->data['npcs'] == '')
            return array();
        return count(explode(';', $this->data['npcs']));
    }

    public function GetNPC()
    {
        return 5;
    }

    public function SingleNPC()
    {
        return $this->data['singlenpc'];
    }

    public function GetMaxGroupMembers()
    {
        return $this->data['maxgroupmembers'];
    }

    public function GetMinGroupMembers()
    {
        return $this->data['mingroupmembers'];
    }

    public function GetSurvivalRounds()
    {
        return $this->data['survivalrounds'];
    }

    public function GetSurvivalTeam()
    {
        return $this->data['survivalteam'];
    }

    public function GetSurvivalWinner()
    {
        return $this->data['survivalwinner'];
    }

    public function GetHealthRatio()
    {
        return $this->data['healthratio'];
    }

    public function GetHealthRatioTeam()
    {
        return $this->data['healthratioteam'];
    }

    public function GetHealthRatioWinner()
    {
        return $this->data['healthratiowinner'];
    }

    public function GetStartingHealthRatioPlayer()
    {
        return $this->data['startinghealthratioplayer'];
    }

    public function GetStartingHealthRatioEnemy()
    {
        return $this->data['startinghealthratioenemy'];
    }
    public function CorrectAnswer()
    {
        return $this->data['canswer'];
    }
    public function AnswerOne()
    {
        return $this->data['answerone'];
    }
    public function AnswerTwo()
    {
        return $this->data['answertwo'];
    }
    public function AnswerThree()
    {
        return $this->data['answerthree'];
    }

    public function GetText()
    {
        return $this->data['text'];
    }

    private function LoadData($id)
    {
        $result = $this->database->Select('*', 'sidestory', 'id=' . $id . '', 1);
        if ($result)
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
