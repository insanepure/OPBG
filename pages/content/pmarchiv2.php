
<?php

$start = 0;
$limit = 30;
$timeOut = 30;
if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0)
{
    $start = $limit * ($_GET['page'] - 1);
}
?>
<script language="JavaScript">
    function toggle(source) {
        checkboxes = document.getElementsByName('deleteID[]');
        for (var i = 0, n = checkboxes.length; i < n; i++) {
            checkboxes[i].checked = source.checked;
        }
    }

    function toggle_sys(source) {
        checkboxes = document.getElementsByName('deleteID[]');
        for (var i = 0, n = checkboxes.length; i < n; i++) {
            if (document.getElementById('0' + i)) {
                document.getElementById('0' + i).checked = source.checked;
            }
        }
    }
</script>
<table width="98%" cellspacing="0" border="0">
    <tr>
        <td colspan=6 class="catGradient borderT borderB">
            <b>
				<span style="text-align: center">
					<span style="color: white">
						<div class="schatten">Nachrichten Option</div>
					</span>
				</span>
            </b>
        </td>
    </tr>
    <tr>
        <td width="25%" class="boxSchatten" style="text-align: center">
            <a href="?p=pm2&p2=inbox">Posteingang</a>
        </td>
    </tr>
</table>
<div class="spacer"></div>
<form method="POST" action="?p=pm2<?php if (isset($_GET['page'])) echo '&page=' . $_GET['page']; ?>&a=action">
    <table width="98%" cellspacing="0" border="0">
        <tr>
            <td colspan=6 class="catGradient borderT borderB">
                <b>
                        <span style="text-align: center">
                          <span style="color: white">
                            <div class="schatten">Empfangene Nachrichten</div>
                          </span>
                        </span>
                </b>
            </td>
        </tr>
        <tr class="boxSchatten">
            <td width="25%" style="text-align: center">
                <b>Datum</b>
            </td>
            <td width="25%" style="text-align: center">
                <b>Von</b>
            </td>
            <td width="30%" style="text-align: center">
                <b>Betreff</b>
            </td>
            <td width="20%" style="text-align: center">
                <b>Aktion</b>
            </td>
        </tr>
        <?php
        $PMManager->LoadInbox($start, $limit, true, $player->IsAdminLogged(), true);
        $i = 0;
        $pm = $PMManager->GetPM($i);
        while ($pm != null)
        {
            ?>
            <tr>
                <td width="25%" class="boxSchatten">
                    <center>
                        <?php
                        if ($pm->GetRead() == 0) echo '<b>';
                        if ($pm->GetSenderName() == "Chat" or $pm->GetSenderName() == "System")
                        {
                            echo "<span style='color: red'>" . $pm->GetTime() . "</span>";
                        }
                        else
                        {
                            echo $pm->GetTime();
                        }
                        if ($pm->GetRead() == 0) echo '</b>';
                        ?>
                    </center>
                </td>
                <td width="25%" class="boxSchatten">
                    <center>
                        <?php
                        if ($pm->GetRead() == 0) echo '<b>';
                        ?>
                        <a href="?p=profil&id=<?php echo $pm->GetSenderID(); ?>">
                            <?php
                            if ($pm->GetSenderName() == "Chat" or $pm->GetSenderName() == "System")
                            {
                                echo "<span style='color: red'>", $pm->GetSenderName(), "</span>";
                            }
                            else
                            {
                                echo $pm->GetSenderName();
                            } ?></a><?php if ($pm->GetRead() == 0) echo '</b>'; ?>
                    </center>
                </td>
                <td width="30%" class="boxSchatten">
                    <center>
                        <?php
                        if ($pm->GetRead() == 0) echo '<b>';
                        if ($pm->GetSenderName() == "Chat" or $pm->GetSenderName() == "System")
                        {
                            echo "<span style='color: red'>" . $pm->GetTopic() . "</span>";
                        }
                        else
                        {
                            echo $pm->GetTopic();
                        }
                        if ($pm->GetRead() == 0) echo '</b>';
                        ?>
                    </center>
                </td>
                <td width="20%" class="boxSchatten">
                    <center>
                        <?php if ($pm->GetRead() == 0) echo '<b>'; ?>
                        <a href="?p=pm2&p2=read&id=<?php echo $pm->GetID(); ?>">
                            <?php
                            if ($pm->GetSenderName() == "Chat" or $pm->GetSenderName() == "System")
                            {
                                echo "<span style='color: red'>Lesen</span></a>";
                            }
                            else
                            {
                                echo "Lesen</a>";
                            }
                            ?>|
                            <input type="checkbox" id="<?php echo $pm->GetSenderID() . $i; ?>" name="deleteID[]" value="<?php echo $pm->GetID(); ?>">
                            <?php if ($pm->GetRead() == 0) echo '</b>'; ?>
                    </center>
                </td>
            </tr>
            <?php
            ++$i;
            $pm = $PMManager->GetPM($i);
        }
        ?>
    </table>
    <?php
    $total = $PMManager->LoadPMCount(true, true, true);
    $pages = ceil($total / $limit);
    if ($pages != 1)
    {
        ?>
        <div class="spacer"></div>
        <?php
        $i = 0;
        while ($i != $pages)
        {
            ?>
            <a href="?p=pmarchiv2&page=<?php echo $i + 1;
            if (isset($_GET['p2'])) echo '&p2=' . $_GET['p2']; ?>">Seite <?php echo number_format($i + 1,'0', '', '.'); ?></a>
            <?php
            ++$i;
        }
    }
    ?>
    <div class="spacer"></div>
    <table width="98%" cellspacing="0" border="0">
        <tr>
            <td colspan=6 height="20px">
            </td>
        </tr>
        <tr>
            <td colspan=6 class="catGradient borderT borderB">
                <b>
            <span style="text-align: center">
              <span style="color: white">
                <div class="schatten">Aktionen</div>
              </span>
            </span>
                </b>
            </td>
        </tr>
        <tr>
            <td width="25%" class="boxSchatten">
                <center><input type="checkbox" name="deleteAll" id="markall" onClick="toggle_sys(this)" style="cursor: pointer;" /><label for="markall" style="cursor: pointer;">Alle Markieren</label><br /></center>
            </td>
            <td width="30%" class="boxSchatten">
                <center>
                    <div class="spacer"></div>
                    <button type="submit" name="action" value="delete">
                        Markierte Löschen
                    </button>
                    <div class="spacer"></div>
                    <button type="submit" name="action" value="read">
                        Markierte Lesen/Unlesen
                    </button>
                    <div class="spacer"></div>
                    <button type="submit" name="action" value="unarchive">
                        Markierte aus dem Archiv entfernen
                    </button>
                    <div class="spacer"></div>
                    <button type="submit" name="action" value="markread">
                        Ungelesene als gelesen markieren
                    </button>
                    <div class="spacer"></div>
                </center>
            </td>
        </tr>
    </table>
</form>