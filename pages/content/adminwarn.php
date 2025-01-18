<div class="spacer"></div>
<div class="catGradient borderT borderB" style="width:90%;">
    <h2>Verwarnung</h2>
</div>
<div class="spacer"></div>
<div class="spacer"></div>


<?php
if (isset($_GET['user']) && is_numeric($_GET['user']) || isset($_POST['user']) && is_numeric($_POST['user']))
{
    $userid = $_GET['user'] ?? $_POST['user'];

    $otherPlayer = new Player($database, $userid);
    if ($otherPlayer->IsValid())
    {
        echo "<h2>" . $otherPlayer->GetName() . "</h2>";
        if ($otherPlayer->GetWarnsCount(0) >= 1)
        {
            ?>
            <a href="?p=profil&id=<?= $otherPlayer->GetID() ?>"><?= $otherPlayer->GetName() ?></a> hat bereits <?= $otherPlayer->GetWarnsCount(0) ?> Verwarnung(en).
            <br/><br/>
            <?php
            $i = 0;
            while($i < $otherPlayer->GetWarnsCount(0))
            {
                echo "<h3>Verwarnung " . ($i+1) . ((!$otherPlayer->GetWarns()[$i]['active']) ? ' (Abgelaufen)' : '') . "</h3>";
                echo "Grund: " . $otherPlayer->GetWarns()[$i]['reason'] . "<br/>";
                echo "Charakter: <a href='?p=profil&id=".$otherPlayer->GetWarns()[$i]['charid']."'>".$otherPlayer->GetWarns()[$i]['name']."</a><br/>";
                echo "Erhalten am: ".$otherPlayer->GetWarns()[$i]['received']."<br/>";
                echo "Läuft ab am: ".$otherPlayer->GetWarns()[$i]['expires']."<br/><br/>";
                if($player->GetArank() >= 2 || $player->GetTeamUser() >= 2) {
                    ?>
                    <form method="POST" action="?p=adminwarn">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="user" value="<?= $userid ?>">
                        <input type="hidden" name="warning" value="<?= $i ?>">
                        <input type="submit" value="Löschen"/>
                    </form>
                    <?php
                    if ($i +1 < $otherPlayer->GetWarnsCount(0))
                        echo "<br/>--------------------------------------<br/>";
                }
                $i++;
            }
        }
        if ($otherPlayer->GetWarnsCount() < 4)
        {
            ?>
            <br />
            <br />
            <form method="POST" action="?p=adminwarn">
                <input type="hidden" name="main" value="<?php echo $userid; ?>">
                <input type="hidden" name="user" value="<?= $userid ?>">
                <input type="hidden" name="action" value="edit">
                <h3>Grund der <?php echo $otherPlayer->GetWarnsCount() + 1; ?>. Verwarnung:</h3>
                <label>
                    <input type="text" name="warnreason" maxlength="255">
                </label>
                <br />
                <br />
                <input type="submit" value="Verwarnen">
            </form>
            <?php
        }
        else
        {
            echo "<br/><br/><br/>Der Spieler wurde bereits 4x verwarnt, eine 5. Verwarnung kann nicht ausgesprochen werden.";
        }
    }
    else
    {
        echo "Der Spieler existiert nicht.";
    }
}
else
{
    ?>
    <form method="POST" action="?p=adminwarn">
        <label>
            <select class="select" name="user">
                <?php
                $users = new Generallist($database, 'accounts', 'name, id', '', '', 99999999);
                $id = 0;
                $entry = $users->GetEntry($id);
                while ($entry != null)
                {

                    ?>
                    <option value="<?= $entry['id']; ?>">
                        <?php
                        echo $entry['name'] . ' [' . number_format($entry['id'], '0', '', '.'). ']';
                        ?>
                    </option>
                    <?php
                    ++$id;
                    $entry = $users->GetEntry($id);
                }
                ?>
            </select>
        </label>
        <div class="spacer"></div>
        <input type="submit" value="Anzeigen">
    </form>
    <?php
}
?>
<br/>
<br/>
<form method="POST" action="?p=adminwarn&list=1">
    <input type="submit" value="Verwarnungen <?php echo ((isset($_GET['list']) && $_GET['list'] == 1 || !isset($_GET['list'])) ? 'Anzeigen' : 'Verbergen'); ?>"/>
</form>
<?php
if(isset($_GET['list']) && $_GET['list'] == 1)
{
    $warnings = $player->GetAllWarnings();
    if(!empty($warnings))
    {
        $i = 0;
        $uid = 1;
        while($i < sizeof($warnings))
        {
            ($warnings[$i]['userid'] == $warnings[$i-1]['userid'] && $i > 0) ? $uid++ : $uid = 1;
            $warn = $uid;
            $id = $warnings[$i]['id'];
            $reason = $warnings[$i]['reason'];
            $received = $warnings[$i]['received'];
            $expires = $warnings[$i]['expire'] == 1 ? $warnings[$i]['expires'] : 'Niemals';
            $charid = $warnings[$i]['charid'];
            $active = $warnings[$i]['active'];
            $user = new Player($database, $charid);
            $charname = $user->GetName();

            ?>
            <br />
            <h3>
                <a href="?p=profil&id=<?= $charid; ?>"><?= $charname; ?></a>
            </h3>
            Verwarnung <?= $warn . ((!$active) ? ' (Abgelaufen)' : ''); ?><br />
            Grund: <?= $reason; ?><br />
            Erhalten am: <?= $received; ?><br />
            Läuft ab am: <?= $expires; ?><br />
            <br />
            <form method="POST" action="?p=adminwarn">
                <input type="hidden" name="user" value="<?= $id; ?>">
                <input type="submit" value="Bearbeiten"/>
            </form>
            <br />
            --------------------------------------------------------------------
            <br />
            <?php
            $i++;
        }
    }
}
?>