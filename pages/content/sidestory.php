<?php
if ($sidestory == null)
{
    'Sidestory not valid.<br/>';
}
    $playernametitel = '<span style="color:red;">' . $player->GetName() . '</span>';
    $playername = '[color=red]' . $player->GetName() . '[/color]';
?>
<div class="newsspan"></div>
<div class="newscontainer smallBG borderR borderL borderT borderB" style="min-height: 500px;">
    <div class="SideMenuKat catGradient borderB">
        <div class="schatten">
            <?php
                if($player->GetArank() >= 2)
                    echo '<a href="?p=admin&a=see&table=story&id='.$sidestory->GetID().'">';
                $storytitel = str_replace('!player', $playernametitel, $sidestory->GetTitel());
                echo $storytitel;
                if($player->GetArank() >= 2)
                    echo '</a>';
            ?>
        </div>
    </div>
    <div class="newscontent smallBG">
        <?php
        $text = str_replace('!player', $playername, $sidestory->GetText());
        echo $bbcode->parse($text);
            if($sidestory->GetType() == 2)
            {
                if($sidestory->GetMaxGroupMembers() < 1)
                    $members = 1;
                else
                    $members = $sidestory->GetMaxGroupMembers();
                echo '<div class="spacer"></div>';
                echo $bbcode->parse('Dieser Kampf ist im [b]' . number_format($members, 0 , '', '.') . ' vs ' . number_format($sidestory->GetNPCsCount(), 0 , '', '.') . '[/b] möglich.');
            }
        ?>
    </div>
    <div class="spacer"></div>
    <?php
    if ($sidestory->GetPlace() == $player->GetPlace() && $sidestory->GetPlanet() == $player->GetPlanet())
    {
        if ($sidestory->GetType() == 1)
        {
    ?>
            <form method="post" action="?p=sidestory&a=continue">
                <input type="submit" value="Weiter">
            </form>
        <?php
        }
        else if ($sidestory->GetType() == 2)
        {
        ?>
            <form method="post" action="?p=sidestory&a=fight">
                <input type="submit" value="KAMPF">
            </form>
        <?php
        }
        else if ($sidestory->GetType() == 3)
        {
        ?>
            <form method="post" action="?p=sidestory&a=train">
                <input type="submit" value="Aktion starten">
            </form>
        <?php
        }
        else if ($sidestory->GetType() == 4)
        {
        ?>
            <form method="post" action="?p=sidestory&a=quizz">
                <input type="radio" name="answer" id="1" style="cursor: pointer;" value="<?php echo $sidestory->AnswerOne(); ?>" /> <label for="1" style="cursor: pointer;"><?php echo "<b>" . $sidestory->AnswerOne() . "</b>"; ?></label> <br />
                <br />
                <input type="radio" name="answer" id="2" style="cursor: pointer;" value="<?php echo $sidestory->AnswerTwo(); ?>" /> <label for="2" style="cursor: pointer;"><?php echo "<b>" . $sidestory->AnswerTwo() . "</b>"; ?></label> <br />
                <br />
                <input type="radio" name="answer" id="3" style="cursor: pointer;" value="<?php echo $sidestory->AnswerThree(); ?>" /> <label for="3" style="cursor: pointer;"><?php echo "<b>" . $sidestory->AnswerThree() . "</b>"; ?></label> <br />
                <div class="spacer"></div>
                <input type="submit" value="Antwort absenden">
            </form>
        <?php
        }
    }
    else if ($sidestory->GetPlanet() == 0)
    {
        ?>
        Derzeit ist keine weitere Neben-Story verfügbar.
    <?php
    }
    else if ($sidestory->GetPlanet() == $player->GetPlanet())
    {
        $place = new Place($database, $sidestory->GetPlace(), null);
        $placeCSS = 'mapplacesidestory';
    ?>
        Reise nach: <?php echo $sidestory->GetPlace();
                    echo "<br /><br />"; ?>
        <a style="cursor:pointer;" onclick="OpenPopupPage('<?php echo $place->GetName(); ?>','map/place.php?id=<?php echo $sidestory->GetPlace(); ?>')">
            <div class="<?php echo $placeCSS; ?>" style="left:59px; top:30px;"></div><input type="submit" value="Reise Starten" />
        </a>
    <?php
    }
    else if ($sidestory->GetPlanet() != $player->GetPlanet())
    {
        $planet = new Planet($database, $sidestory->GetPlanet());
    ?>
        Reise in das Meeresgebiet des/der "<?php echo $planet->GetName(); ?>".
    <?php
    }
    ?>
    <div class="spacer"></div>
</div>
<div class="spacer"></div>

<?php
if ($player->GetARank() >= 2)
{
    $select = "id, titel";
    $where = '';
    $order = 'id';
    $from = 'sidestory';
    $list = new Generallist($database, $from, $select, $where, $order, 1000, 'DESC');
?>
    <form method="post" action="?p=sidestory&a=jump">
        <select class="select" name="sidestoryid" id="sidestoryid">
            <?php
            $id = 0;
            $entry = $list->GetEntry($id);
            while ($entry != null)
            {
            ?>
                <option value="<?php echo $entry['id']; ?>"><?php echo '(' . $entry['id'] . ') ' . $entry['titel']; ?></option><?php
                                                                                                                                ++$id;
                                                                                                                                $entry = $list->GetEntry($id);
                                                                                                                            }
                                                                                                                                ?>
        </select>
        <input type="submit" value="Springen">
    </form>
    <br />
<?php
}
?>