<center>
    <div class="spacer"></div>
    <?php
    if ($sidestory->GetPlanet() != "")
    {
        $npc = new NPC($database, $sidestory->GetTalkNPC());
        ShowPlayer($npc);
    }
    ?>
</center>