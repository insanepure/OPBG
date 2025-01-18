<?php
if($player->GetArank() >= 2)
{
    if(isset($_GET['fight']) && is_numeric($_GET['fight']))
    {
        $result = $database->Select('debuglog', 'lastfights', 'id='.$_GET['fight'], 1);
        if($result)
        {
            if($result->num_rows > 0)
            {
                $row = $result->fetch_assoc();
                echo $row['debuglog'];
            }
        }
    }
}