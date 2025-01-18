<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<div class="spacer"></div>
<div class="catGradient borderT borderB" style="width:90%;">
    <h2>Moderation</h2>
</div>
<div class="spacer"></div>
<div class="spacer"></div>


<?php
if (isset($_GET['user']) && is_numeric($_GET['user']))
{
    $userid = 0;
    $user = $_GET['user'];
    $username = '';
    $result = $database->Select('id, name, userid', 'accounts', 'id = ' . $user . '', 1);
    if ($result)
    {
        if ($result->num_rows > 0)
        {
            $row = $result->fetch_assoc();
            $userid = $row['userid'];
            $username = $row['name'];
        }
        $result->close();
    }

    $banned = false;
    $banreason = '';
    $gameName = 'opwx';
    $result = $accountDB->Select('id, bannedgames, banreason', 'users', 'id = ' . $userid . '', 1);
    if ($result)
    {
        if ($result->num_rows > 0)
        {
            $row = $result->fetch_assoc();
            $bannedGames = explode(';', $row['bannedgames']);
            $banned = in_array($gameName, $bannedGames);
            $banreason = $row['banreason'];
        }
        $result->close();
    }
    $koloclosecheck = $database->select('*', 'accounts', 'id="'.$user.'"');
    $koloclose = $koloclosecheck->fetch_assoc();
    ?>
    <h2><?php echo $username; ?></h2>
    <form method="POST" action="?p=adminmod&user=<?php echo $user; ?>&a=edit">
        <input type="hidden" name="main" value="<?php echo $userid; ?>">
        <input type="hidden" name="userid" value="<?php echo $user; ?>">
        <?php if (isset($_GET['report']) && $_GET['report'] > 0) echo '<input type="hidden" name="report" value="' . $_GET['report'] . '">'; ?>
        <b>Bannen:</b> <input type="checkbox" name="banned" <?php if ($banned) echo 'checked'; ?>><br /><br />
        <b>Kolosseum Sperre:</b> <input type="checkbox" name="koloclose" <?php if ($koloclose['koloclose'] == 1) echo 'checked'; ?>><br /><br />
        <b>Elokämpfe Sperre:</b> <input type="checkbox" name="eloclose" <?php if ($koloclose['eloclose'] == 1) echo 'checked'; ?>><br /><br />
        <b>PvP-Kämpfe Sperre:</b> <input type="checkbox" name="kgclose" <?php if ($koloclose['kgclose'] == 1) echo 'checked'; ?>><br /><br />
        Grund: <textarea name="banreason" style="width:400px; height:100px;"><?php echo $banreason; ?></textarea><br />
        <hr>
        <h1>Sitting</h1>
        Von: <input type="date" name="sittingstart" value="<?= $koloclose['sitterstart']; ?>"> Bis: <input type="date" name="sittingend" value="<?= $koloclose['sitterend']; ?>">
        <br />
        <br />
        Sitter ID
        <br />
        <table>
            <tr>
                <td>
                    <select class="select" name="sitter" style="width:200px;">
                        <?php
                        if($koloclose['sitter'] == 0)
                        {
                        ?>
                        <option value="0">Spieler auswählen</option>
                        <?php
                        }
                        else
                            {
                                $SitterPlayer = new Player($database, $koloclose['sitter']);
                                ?>
                                <option value="<?= $SitterPlayer->GetUserID(); ?>"><?= $SitterPlayer->GetName(); ?></option>
                                    <?php
                            }
                        $accounts = new Generallist($database, 'accounts', '*', '', '', 99999999999, 'ASC');
                        $id = 0;
                        $entry = $accounts->GetEntry($id);
                        while ($entry != null)
                        {
                            ?>
                            <option value="<?php echo $entry['userid']; ?>" <?php if ($_GET['player'] == $entry['id']) echo 'selected'; ?>> <?php echo $entry['name']; ?></option>
                            <?php
                            ++$id;
                            $entry = $accounts->GetEntry($id);
                        }
                        ?>
                    </select>
                </td>
            </tr>
        </table>
        <hr>
        <h1>Werte gut schreiben</h1>
        <br/>
        Elokämpfe abziehen oder hinzufügen<br />
        <input type="text" name="dailyelo" value="<?= $koloclose['dailyelofights']; ?>"><br />
        PvP Kämpfe abziehen oder hinzufügen<br />
        <input type="text" name="dailykg" value="<?= $koloclose['dailyfights']; ?>"><br />
        Elopunkte abziehen oder hinzufügen<br />
        <input type="text" name="dailyelop" value="<?= $koloclose['elopoints']; ?>"><br />
        Kolosseumpunkte abziehen oder hinzufügen<br />
        <input type="text" name="dailyarena" value="<?= $koloclose['arenapoints']; ?>">
        <hr>
        <input type="submit" value="Moderieren">
    </form>
    <?php
}
else
{
    ?>
    <form method="GET" action="?p=adminmod">
        <input type="hidden" name="p" value="adminmod">
        <select class="select" name="user">
            <?php
            $users = new Generallist($database, 'accounts', 'name, id', '', '', 99999999);
            $id = 0;
            print_r($users);
            $entry = $users->GetEntry($id);
            while ($entry != null)
            {

                ?>
                <option value="<?php echo $entry['userid']; ?>"><?php echo $entry['name'] . ' [' . number_format($entry['id'], '0', '', '.') . ']'; ?></option>
                <?php
                ++$id;
                $entry = $users->GetEntry($id);
            }
            ?>
        </select>
        <div class="spacer"></div>
        <input type="submit" value="Moderieren">
    </form>
    <?php
}
?>
<script>
    $('.select').select2();
</script>
<style>
    .select2-results {
        color: #000000;
    }
</style>
