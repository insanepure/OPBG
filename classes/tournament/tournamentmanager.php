<?php
if ($database == NULL)
{
	print 'This File (' . __FILE__ . ') should be after Database!';
}

include_once 'tournament.php';

class TournamentManager
{

	private $database;
	private $tournaments;

	function __construct($db, $planet)
	{
		$this->database = $db;
		$this->tournaments = array();
		$this->LoadData($planet);
	}

	private function LoadData($arank)
	{
		$result = $this->database->Select('*', 'tournaments', 'arank > "-1"', 99999);
		if ($result)
		{
			if ($result->num_rows > 0)
			{
				while ($row = $result->fetch_assoc())
				{
					$tournament = new Tournament($row, $this->database);
					$this->tournaments[] = $tournament;
				}
			}
			$result->close();
		}
	}

	public function GetTournamentByID($id) : ?Tournament
	{
		$i = 0;
		while (isset($this->tournaments[$i]))
		{
			if ($this->tournaments[$i]->GetID() == $id)
			{
				return $this->tournaments[$i];
			}
			++$i;
		}
		return null;
	}

	public function GetTournament($id) : ?Tournament
	{
		if (isset($this->tournaments[$id]))
		{
			return $this->tournaments[$id];
		}
		return null;
	}
}
