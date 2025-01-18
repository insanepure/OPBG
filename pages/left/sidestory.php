<center>
    <div class="spacer"></div>
    <?php
        $group = $player->GetGroup();
        if (isset($group) && count($group) > 1)
        {
            foreach ($group as &$gID)
            {
                $gPlayer = new Player($database, $gID, $actionManager);
                if ($gPlayer->GetPlanet() == $player->GetPlanet())
                {
                    ShowPlayer($gPlayer);
                }
            }
        }
        else
        {
            ShowPlayer($player);
        }

        $npcs = $sidestory->GetSupportNPCs();
        if ($npcs != '')
        {
            foreach ($npcs as &$NPC)
            {
                $supportNPC = new NPC($database, $NPC);
                ShowPlayer($supportNPC);
            }
        }
    ?>
    <a href="?p=profil">
        <input type="submit" value="Zurück" />
    </a>
</center>