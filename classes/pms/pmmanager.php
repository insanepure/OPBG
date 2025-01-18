<?php
ini_set('memory_limit', '-1');
if ($database == NULL)
{
	print 'This File (' . __FILE__ . ') should be after Database!';
}

include_once 'pm.php';

class PMManager
{

	private $database;
	private $pms;
	private $playerID;
	private $unreadPMs = 0;
	private $unreadPMs2 = 0;
	function __construct($db, $playerID)
	{
		$this->database = $db;
		$this->pms = array();
		$this->playerID = $playerID;
		$this->LoadUnreadPMs();
		$this->LoadSystemPMs();
	}

	public function GetUnreadPMs()
    {
		return $this->unreadPMs;
	}

	public function GetSystemPMs()
	{
		return $this->unreadPMs2;
	}
	private function LoadUnreadPMs()
	{
		$result = $this->database->Select('COUNT(id) as total', 'pms', '`receiverid` = ' . $this->playerID . ' AND `read` = 0 AND `senderid` != 0 AND deleted=0');
		if ($result)
		{
			$row = $result->fetch_assoc();
			$this->unreadPMs = $row['total'];

			$result->close();
		}
	}
	private function LoadSystemPMs()
	{
		$result = $this->database->Select('COUNT(id) as total', 'pms', '`receiverid` = ' . $this->playerID . ' AND `read` = 0 AND `senderid` = 0 AND deleted=0');
		if ($result)
		{
			$row = $result->fetch_assoc();
			$this->unreadPMs2 = $row['total'];

			$result->close();
		}
	}

	public function Delete($ids, $pid)
	{
		$where = '';
		$i = 0;
		while (isset($ids[$i]))
		{
			$id = $ids[$i];
			if (!is_numeric($id))
			{
				++$i;
				continue;
			}
			$pm = $this->LoadPM($id);
			if ($pm == null)
			{
				++$i;
				continue;
			}
			if ($pm->GetRead() == false)
			{
				if ($pm->GetSenderID() != 0)
					$this->unreadPMs = $this->unreadPMs - 1;
				else
					$this->unreadPMs2 = $this->unreadPMs2 - 1;
			}

			$whereText = '`id`=' . $id . '';
			if ($where == '')
			{
				$where = $whereText;
			}
			else
			{
				$where = $where . ' OR ' . $whereText;
			}

			++$i;
		}

		if ($where == '')
		{
			return;
		}

        if ($pm->GetSenderID() != 0)
            $this->database->Update('deleted=1', 'pms', '(' . $where . ') AND `receiverid`=' . $pid . '', 99999);
        else
            $this->database->Delete('pms', '(' . $where . ') AND `receiverid`=' . $pid . '', 99999);
	}

	public function ReadAllOnly($ids, $pid)
	{
		$where = '';
		$i = 0;
		$count = 0;
		while (isset($ids[$i]))
		{
			$id = $ids[$i];
			if (!is_numeric($id))
			{
				++$i;
				continue;
			}
			$pm = $this->LoadPM($id);
			if ($pm == null)
			{
				++$i;
				continue;
			}
			if ($pm->GetRead())
			{
				++$i;
				continue;
			}

			if ($pm->GetSenderID() != 0)
				$this->unreadPMs = $this->unreadPMs - 1;
			else
				$this->unreadPMs2 = $this->unreadPMs2 - 1;

			$whereText = '`id`=' . $id . '';
			if ($where == '')
			{
				$where = $whereText;
			}
			else
			{
				$where = $where . ' OR ' . $whereText;
			}
			++$count;

			++$i;
		}

		if ($where == '')
		{
			return;
		}

		$this->database->Update('`read`= !`read`', 'pms', '(' . $where . ') AND `receiverid`=' . $pid . '', $count);
	}

	public function ReadAll($ids, $pid)
	{
		$where = '';
		$i = 0;
		$count = 0;
		while (isset($ids[$i]))
		{
			$id = $ids[$i];
			if (!is_numeric($id))
			{
				++$i;
				continue;
			}
			$pm = $this->LoadPM($id);
			if ($pm == null)
			{
				++$i;
				continue;
			}
			if ($pm->GetRead() == false)
			{
				if ($pm->GetSenderID() != 0)
					$this->unreadPMs = $this->unreadPMs - 1;
				else
					$this->unreadPMs2 = $this->unreadPMs2 - 1;
			}
			else
			{
				if ($pm->GetSenderID() != 0)
					$this->unreadPMs = $this->unreadPMs + 1;
				else
					$this->unreadPMs2 = $this->unreadPMs2 + 1;
			}

			$whereText = '`id`=' . $id . '';
			if ($where == '')
			{
				$where = $whereText;
			}
			else
			{
				$where = $where . ' OR ' . $whereText;
			}
			++$count;

			++$i;
		}

		if ($where == '')
		{
			return;
		}

		$this->database->Update('`read`= !`read`', 'pms', '(' . $where . ') AND `receiverid`=' . $pid . '', $count);
	}

	public function Read($id, $pid)
	{
		$pm = $this->LoadPM($id);
		if ($pm == null || $pm->GetRead() || $pm->GetReceiverID() != $pid)
		{
			return;
		}
		if ($pm->GetSenderID() != 0)
			$this->unreadPMs = $this->unreadPMs - 1;
		else
			$this->unreadPMs2 = $this->unreadPMs2 - 1;
		$this->database->Update('`read`=1', 'pms', '`id`=' . $id . ' AND `receiverid`=' . $pid . '', 1);
	}

    public function ArchivePM($ids, $pid, $value)
    {
        if(!empty($ids))
        {
            foreach($ids as $id)
            {
                $pm = $this->LoadPM($id);
                if ($pm == null || $pm->GetReceiverID() != $pid)
                    return;

                $pm->ArchivePM($value);
                $this->database->Update('archived='.$value, 'pms', 'id = ' . $id . ' AND receiverid = ' . $pid);
            }
        }
    }

	public function SendPMToAll($id, $image, $name, $title, $text, $isHTML = 0)
	{
		$result = $this->database->Select('*', 'accounts', '', 9999999);
		if ($result)
		{
			$timestamp = date('Y-m-d H:i:s');
			while ($row = $result->fetch_assoc())
			{
				$tid = $row['id'];
				$tname = $row['name'];
				$this->database->Insert(
					'`sendername`, `senderimage`, `senderid`, `receiverid`, `receivername`, `text`, `time`, `topic`, `read`, `ishtml`',
					'"' . $name . '","' . $image . '","' . $id . '","' . $tid . '","' . $tname . '","' . $text . '","' . $timestamp . '","' . $title . '", "0", "' . $isHTML . '"',
					'pms'
				);
			}
			$result->close();
		}
		else
		{
			return false;
		}

		return true;
	}

	public function SendPM($id, $image, $name, $title, $text, $tname, $isHTML = 0, $arank = 0): bool
    {
		$result = $this->database->Select('*', 'accounts', 'name="' . $tname . '"', 1);
		if ($result)
		{
            if($id == 0)
                $text = $this->database->EscapeString($text);
			$row = $result->fetch_assoc();
			$tid = $row['id'];

            $blocked = explode(';', $row['blocked']);
            if($id > 0 && in_array($id, $blocked) && $arank == 0)
            {
                return false;
            }

			$timestamp = date('Y-m-d H:i:s');
			if($isHTML == 0)
			{
				$title = strip_tags($title);
			}
			$result = $this->database->Insert(
				'`sendername`, `senderimage`, `senderid`, `receiverid`, `receivername`, `text`, `time`, `topic`, `read`, `ishtml`',
				'"' . $name . '","' . $image . '","' . $id . '","' . $tid . '","' . $tname . '","' . $text . '","' . $timestamp . '","' . $title . '", "0", "' . $isHTML . '"',
				'pms'
			);
			if (!$result)
			{
				return false;
			}
		}
		else
		{
			return false;
		}
        if ($id == $tid && $id != 0)
        {
            $this->unreadPMs = $this->unreadPMs + 1;
        }
		return true;
	}

	public function LoadOutbox($start, $limit, $admin)
	{
		$this->LoadPMs(false, $start, $limit, false, $admin, false);
	}

	public function LoadInbox($start, $limit, $system, $admin, $archived)
	{
		$this->LoadPMs(true, $start, $limit, $system, $admin, $archived);
	}

	public function LoadPM($id)
	{
		$pm = null;
		$result = $this->database->Select('*', 'pms', '`id` = ' . $id . ' AND (`receiverid` = ' . $this->playerID . ' OR `senderid` = ' . $this->playerID . ')', 1);
		if ($result)
		{
			if ($result->num_rows > 0)
			{
				$row = $result->fetch_assoc();
				$pm = new PM($row);
			}
			$result->close();
		}
		return $pm;
	}

	public function GetPM($id)
	{
		if (count($this->pms) > $id && $id >= 0)
		{
			return $this->pms[$id];
		}
		return null;
	}

	public function DeleteAll($systemPMs, $pid)
	{
		if (!$systemPMs)
		{
            $this->database->Update('deleted=1', 'pms', 'archived = 0 AND senderid != 0 AND receiverid=' . $pid . '', 999999999);
			$this->unreadPMs = 0;
		}
		else
		{
			$this->database->Delete('pms', 'archived = 0 AND senderid=0 AND receiverid=' . $pid . '', 999999999);
			$this->unreadPMs2 = 0;
		}
	}

	private function LoadPMs($inbox, $start, $limit, $system, $admin, $archived)
	{
        if($admin)
		    $where = '';
        else
            $where = 'deleted=0 AND ';
		if ($inbox)
		{
			$where .= 'receiverid=' . $this->playerID . '';
			if ($system)
			{
				$where .= ' AND senderid=0';
			}
			else
			{
				$where .= ' AND senderid != 0';
			}
            if($archived)
            {
                $where .= ' AND archived = 1';
            }
            else
            {
                $where .= ' AND archived = 0';
            }
		}
		else
		{
			$where .= 'senderid=' . $this->playerID . '';
		}
		$result = $this->database->Select('*', 'pms', $where, $start . ',' . $limit, 'time', 'DESC');
		if ($result)
		{
			if ($result->num_rows > 0)
			{
				while ($row = $result->fetch_assoc())
				{
					$pm = new PM($row);
					$this->pms[] = $pm;
				}
			}
			$result->close();
		}
	}

    public function LoadAllPMs()
    {
        $result = $this->database->Select('*', 'pms', 'receiverid='.$this->playerID, 9999, 'time', 'DESC');
        if ($result)
        {
            if ($result->num_rows > 0)
            {
                while ($row = $result->fetch_assoc())
                {
                    $pm = new PM($row);
                    $this->pms[] = $pm;
                }
            }
            $result->close();
        }
    }

	public function LoadPMCount($inbox, $system, $archived = 0)
	{
		$where = 'deleted=0 AND ';
		if ($inbox)
		{
			$where .= 'receiverid=' . $this->playerID . '';
			if ($system)
			{
				$where .= ' AND senderid=0';
			}
			else
			{
				$where .= ' AND senderid != 0';
			}
		}
		else
		{
			$where = 'senderid=' . $this->playerID . '';
		}
        if($archived)
        {
            $where .= 'archived = 1';
        }
		$total = 0;
		//SELECT COUNT(id) as total FROM `pms` WHERE `receiverid` = 1 AND `read` = 0
		$result = $this->database->Select('COUNT(id) as total', 'pms', $where);
		if ($result)
		{
			$row = $result->fetch_assoc();
			$total = $row['total'];

			$result->close();
		}
		return $total;
	}
}
