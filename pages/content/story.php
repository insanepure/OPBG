<?php
if ($story == null)
{
    'Story not valid.<br/>';
}
    $playernametitel = '<span style="color:red;">' . $player->GetName() . '</span>';
    $playername = '[color=red]' . $player->GetName() . '[/color]';
?>
    <div class="newsspan"></div>
    <div class="newscontainer smallBG borderR borderL borderT borderB">
        <div class="SideMenuKat catGradient borderB">
            <div class="schatten">
                <?php
                if($player->GetArank() >= 2)
                    echo '<a href="?p=admin&a=see&table=story&id='.$story->GetID().'">';
                $storytitel = str_replace('!player', $playernametitel, $story->GetTitel());
                echo $storytitel;
                if($player->GetArank() >= 2)
                    echo '</a>';
                ?>
            </div>
        </div>
        <div class="newscontent smallBG">
            <?php
            $text = str_replace('!player', $playername, $story->GetText());
            echo $bbcode->parse($text);
            if($story->GetType() == 2)
            {
                if($story->GetMaxGroupMembers() < 1)
                    $members = 1;
                else
                    $members = $story->GetMaxGroupMembers();

                if(sizeof($story->GetSupportNPCs()) > 0)
                {
                    $members += sizeof($story->GetSupportNPCs());
                }
                echo '<div class="spacer"></div>';
                echo $bbcode->parse('Dieser Kampf ist im [b]' . number_format($members, 0 , '', '.') . ' vs ' . number_format($story->GetNPCsCount(), 0 , '', '.') . '[/b] möglich.');
            }
            ?>
        </div>
        <div class="spacer"></div>
        <?php
        if ($story->GetPlace() == $player->GetPlace() && $story->GetPlanet() == $player->GetPlanet())
        {
            if ($story->GetID() > 235 && $player->GetArank() < 2)
            {
                ?>
                Die Story kann aktuell nicht fortgeführt werden.
                <?php
            }
            else if($story->GetType() == 1)
            {
                ?>
                <form method="post" action="?p=story&a=continue">
                    <input type="submit" value="Weiter">
                </form>
                <?php
            }
            else if($story->GetType() == 2)
            {
                ?>
                <form method="post" action="?p=story&a=fight">
                    <input type="submit" value="KAMPF">
                </form>
                <?php
            }
            else if($story->GetType() == 3)
            {
                $action = $actionManager->GetAction($story->GetAction());
                if($action != null)
                    echo 'Dauer: ' . $action->GetDauer() . ' Stunden';
                echo '';
                ?>
                <div class="spacer"></div>
                <form method="post" action="?p=story&a=train">
                    <input type="submit" value="Aktion starten">
                </form>
                <?php
            }
            else if($story->GetType() == 4)
            {
                ?>
                <form method="post" action="?p=story&a=quiz">
                    <?php
                    $aid = 0;
                    foreach($story->GetQuizAnswers() as $answer)
                    {
                        ?>
                        <input type="<?php echo (sizeof($story->CorrectAnswers()) > 1) ? 'checkbox' : 'radio'; ?>" name="answer[]" id="<?= $aid ?>" style="cursor: pointer;" value="<?= $aid ?>" />
                        <label for="<?= $aid ?>" style="cursor: pointer;">
                            <b>
                                <?= $answer ?>
                            </b>
                        </label>
                        <br />
                        <br />
                        <?php
                        $aid++;
                    }
                    ?>
                    <div class="spacer"></div>
                    <input type="submit" value="Antwort absenden">
                </form>
                <?php
            }
        }
        else if($story->GetPlanet() == 0 && $player->GetArank() < 2)
        {
            echo 'Derzeit ist keine weitere Story verfügbar.';
        }
        else if($story->GetPlanet() == $player->GetPlanet())
        {
            $storyplace = new Place($database, $story->GetPlace(), null);
            $placeCSS = 'mapplacestory';
            echo 'Reise nach: ' . $storyplace->GetName();
            echo "<br /><br />";
            ?>
            <a style="cursor:pointer;" onclick="OpenPopupPage('<?php echo $storyplace->GetName(); ?>','map/place.php?id=<?php echo $story->GetPlace(); ?>')">
                <div class="<?php echo $placeCSS; ?>" style="left:59px; top:30px;"></div><input type="submit" value="Reise Starten" />
            </a>
            <?php
        }
        else if ($story->GetPlanet() != $player->GetPlanet())
        {
            $storyplanet = new Planet($database, $story->GetPlanet());
            ?>
            Reise in das Meeresgebiet des/der "<?php echo $storyplanet->GetName(); ?>".
            <?php
        }
        ?>
        <div class="spacer"></div>
    </div>
    <div class="spacer"></div>

<?php
if ($player->GetARank() >= 2)
{
    $select = "id, titel, npcs";
    $where = '';
    $order = 'id';
    $from = 'story';
    $list = new Generallist($database, $from, $select, $where, $order, 1000, 'DESC');
    if(count($story->GetNPCs()) == 1)
    {
        ?>
        <div style="float: left; margin-left: 5%;">
            <a href="?p=admin&a=see&table=npcs&id=<?php echo $story->GetNPCs()[0]; ?>">
                <button>Balancen</button>
            </a>
        </div>
        <?php
    }
    ?>
    <div style="float: right; margin-right: 5%;">
        <form method="post" action="?p=story&a=jump">
            <select class="select" name="storyid" id="storyid">
                <?php
                $id = 0;
                $entry = $list->GetEntry($id);
                while ($entry != null)
                {
                    ?>
                    <option value="<?php echo $entry['id']; ?>" <?php if ($player->GetStory() == $entry['id']) echo 'selected'; ?>>
                        <?php echo '(' . $entry['id'] . ') ' . $entry['titel']; ?>
                    </option>
                    <?php
                    ++$id;
                    $entry = $list->GetEntry($id);
                }
                ?>
            </select>
            <input type="submit" value="Springen">
        </form>
    </div>
    <div class="spacer"></div>
    <?php
}
?>