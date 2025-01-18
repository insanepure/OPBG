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
            <?php if($player->HasTeleschnecke())
            {
                ?>
                    <a href="?p=pm&p2=send">Nachricht schreiben</a>
                <?php
            }
            ?>
        </td>
        <td width="25%" class="boxSchatten" style="text-align: center">
            <a href="?p=pm&p2=inbox">Empfangene Nachrichten</a>
        </td>
        <td width="25%" class="boxSchatten" style="text-align: center">
            <a href="?p=pm&p2=outbox">Gesendete Nachrichten</a>
        </td>
        <td width="15%" class="boxSchatten" style="text-align: center">
            <a href="?p=pmarchiv">Archiv</a>
        </td>

    </tr>
</table>
<div class="spacer"></div>
<?php
$start = 0;
$limit = 30;
$timeOut = 30;
if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0)
{
    $start = $limit * ($_GET['page'] - 1);
}
if (!isset($_GET['p2']) || isset($_GET['p2']) && $_GET['p2'] == 'inbox')
{
    ?>
    <script language="JavaScript">
        function toggle(source) {
            checkboxes = document.getElementsByName('deleteID[]');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                checkboxes[i].checked = source.checked;
            }
        }

        function toggle_sys(source) {
            //id = document.getElementById();
            checkboxes = document.getElementsByName('deleteID[]');
            //alert( checkboxes[i].getElementById('0'));
            //alert("Test");
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                if (document.getElementById('0' + i)) {
                    document.getElementById('0' + i).checked = source.checked;
                }
            }
        }
    </script>
    <form method="POST" action="?p=pm<?php if (isset($_GET['page'])) echo '&page=' . $_GET['page']; ?>&a=action">
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
            $PMManager->LoadInbox($start, $limit, false, $player->IsAdminLogged(), false);
            $i = 0;
            $pm = $PMManager->GetPM($i);
            while ($pm != null)
            {
                if ($pm->GetSenderID() == 0)
                {
                    ++$i;
                    $pm = $PMManager->GetPM($i);
                    continue;
                }

                if($pm->GetSenderID() != 649)
                    $sender = new Player($database, $pm->GetSenderID());
                if($pm->GetSenderID() != 649 && $sender->IsValid())
                {
                    if($sender->GetArank() < 2 && !$player->HasTeleschnecke())
                    {
                        ++$i;
                        $pm = $PMManager->GetPM($i);
                        continue;
                    }
                }
                ?>
                <tr>
                    <td width="25%" class="boxSchatten">
                        <center><?php if ($pm->GetRead() == 0) echo '<b>'; ?><?php if ($pm->GetSenderName() == "Chat" or $pm->GetSenderName() == "System")
                            {
                                echo "<span style='color: red'>" . $pm->GetTime() . "</span>";
                            }
                            else
                            {
                                echo $pm->GetTime();
                            } ?><?php if ($pm->GetRead() == 0) echo '</b>'; ?></center>
                    </td>
                    <td width="25%" class="boxSchatten">
                        <center><?php if ($pm->GetRead() == 0) echo '<b>'; ?><a href="?p=profil&id=<?php echo $pm->GetSenderID(); ?>"><?php if ($pm->GetSenderName() == "Chat" or $pm->GetSenderName() == "System")
                                {
                                    echo "<span style='color: red'>", $pm->GetSenderName(), "</span>";
                                }
                                else
                                {
                                    echo $pm->GetSenderName();
                                } ?></a><?php if ($pm->GetRead() == 0) echo '</b>'; ?></center>
                    </td>
                    <td width="30%" class="boxSchatten">
                        <center><?php if ($pm->GetRead() == 0) echo '<b>'; ?><?php if ($pm->GetSenderName() == "Chat" or $pm->GetSenderName() == "System")
                            {
                                echo "<span style='color: red'>" . $pm->GetTopic() . "</span>";
                            }
                            else
                            {
                                echo $pm->GetTopic();
                            } ?><?php if ($pm->GetRead() == 0) echo '</b>'; ?></center>
                    </td>
                    <td width="20%" class="boxSchatten">
                        <center>
                            <?php if ($pm->GetRead() == 0) echo '<b>'; ?>
                            <a href="?p=pm&p2=read&id=<?php echo $pm->GetID(); ?>"><?php if ($pm->GetSenderName() == "Chat" or $pm->GetSenderName() == "System")
                                {
                                    echo "<span style='color: red'>Lesen</span></a>";
                                }
                                else
                                {
                                    echo "Lesen</a>";
                                } ?> |
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
        <br />
        <?php
        if(!$player->HasTeleschnecke())
        {
            echo "<div style='color: red'><b>Wichtig:</b> Du besitzt keine Teleschnecke, du kannst daher nur Administrative Nachrichten lesen.</div>";
        }
        $total = $PMManager->LoadPMCount(true, false);
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
                <a href="?p=pm&page=<?php echo $i + 1;
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
                    <center><input type="checkbox" name="deleteAll" id="markall" onClick="toggle(this)" style="cursor: pointer;" /><label for="markall" style="cursor: pointer;">Alle Markieren</label><br /></center>
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
                        <button type="submit" name="action" value="archive">
                            Markierte Archivieren
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
    <br />
    <?php
    if($player->HasTeleschnecke())
    {
    ?>
        <button onclick="OpenPopupPage('Alle Nachrichten löschen','pm/manage.php?a=deleteall&p=1')">
            Alle Nachrichten löschen
        </button>
    <?php
    }
}
else if (isset($_GET['p2']) && $_GET['p2'] == 'outbox')
{
    ?>
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
                            <div class="schatten">Gesendete Nachrichten</div>
                        </span>
                    </span>
                </b>
            </td>
        </tr>
        <tr class="boxSchatten">
            <td width="25%"><b>
                    <center>Datum</center>
                </b></td>
            <td width="25%"><b>
                    <center>An</center>
                </b></td>
            <td width="30%"><b>
                    <center>Betreff</center>
                </b></td>
            <td width="20%"><b>
                    <center>Aktion</center>
                </b></td>
        </tr>
        <?php
        $PMManager->LoadOutbox($start, $limit, $player->IsAdminLogged());
        $i = 0;
        $pm = $PMManager->GetPM($i);
        while ($pm != null)
        {
            ?>
            <tr>
                <td width="25%" class="boxSchatten">
                    <center><?php if ($pm->GetRead() == 0) echo '<b>'; ?><?php echo $pm->GetTime(); ?><?php if ($pm->GetRead() == 0) echo '</b>'; ?></center>
                </td>
                <td width="25%" class="boxSchatten">
                    <center><?php if ($pm->GetRead() == 0) echo '<b>'; ?><a href="?p=profil&id=<?php echo $pm->GetReceiverID(); ?>"><?php echo $pm->GetReceiverName(); ?></a><?php if ($pm->GetRead() == 0) echo '</b>'; ?></center>
                </td>
                <td width="30%" class="boxSchatten">
                    <center><?php if ($pm->GetRead() == 0) echo '<b>'; ?><?php echo $pm->GetTopic(); ?><?php if ($pm->GetRead() == 0) echo '</b>'; ?></center>
                </td>
                <td width="20%" class="boxSchatten">
                    <center><?php if ($pm->GetRead() == 0) echo '<b>'; ?><a href="?p=pm&p2=read&id=<?php echo $pm->GetID(); ?>">Lesen</a><?php if ($pm->GetRead() == 0) echo '</b>'; ?></center>
                </td>
            </tr>
            <?php
            ++$i;
            $pm = $PMManager->GetPM($i);
        }
        ?>
    </table>

    <?php
    $total = $PMManager->LoadPMCount(false, false);
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
            <a href="?p=pm&page=<?php echo $i + 1;
            if (isset($_GET['p2'])) echo '&p2=' . $_GET['p2']; ?>">Seite <?php echo number_format($i + 1,'0', '', '.'); ?></a>
            <?php
            ++$i;
        }
    }
    ?>
    <?php
}
else if (isset($_GET['p2']) && $_GET['p2'] == 'send')
{
    ?>
    <form method="POST" action="?p=pm&a=send">
        <table width="100%" cellspacing="0" border="0">
            <tr>
                <td>
                    <center><input style="width:500px;" type="text" name="to" placeholder="An" <?php if (isset($_GET['name'])) echo 'value="' . $_GET['name'] . '"'; ?>></center>
                </td>
            </tr>
            <tr>
                <td>
                    <center><input style="width:500px;" type="text" name="topic" placeholder="Betreff" <?php if (isset($_GET['topic'])) echo 'value="RE: ' . $_GET['topic'] . '"'; ?>></center>
                </td>
            </tr>
            <tr>
                <td>
                    <center><textarea style="width:500px; height:250px; resize: vertical;" name="text"></textarea></center>
                </td>
            </tr>
            <tr>
                <td>
                    <center><input type="submit" value="senden" style="width:300px;"></center>
                </td>
            </tr>
        </table>
    </form>
    <?php
}
else if (isset($_GET['p2']) && $_GET['p2'] == 'read')
{
    $pm = null;
    if (isset($_GET['id']) && is_numeric($_GET['id']))
    {
        $id = $_GET['id'];
        $pm = $PMManager->LoadPM($id);
    }
    if ($pm != null)
    {
        ?>
        <table width="100%" class="boxSchatten borderT borderR borderL borderB" style="table-layout:fixed;" cellspacing="0">
            <tr>
                <td width="10%" height="50px" class="catGradient borderB">
                    <img src="img.php?url=<?php echo $pm->GetSenderImage(); ?>" width="50px" height="50px">
                </td>
                <td width="20%" class="catGradient borderB authortime">
                    <a href="?p=profil&id=<?php echo $pm->GetSenderID(); ?>" class="textColor"><?php echo $pm->GetSenderName(); ?><br /></a>
                    <?php echo $pm->GetTime(); ?>
                </td>
                <td width="70%" class="catGradient borderB headtext" align="center">
                    <?php echo $pm->GetTopic(); ?>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="width:610px; min-height:200px; word-wrap: break-word; overflow:hidden;">
                    <?php
                    if ($pm->IsHTML())
                    {
                        echo $pm->GetText();
                    }
                    else
                    {
                        echo $bbcode->parse(htmlspecialchars_decode($pm->GetText()));
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="footer borderT" align="center">
                    <a href="?p=pm&p2=send&name=<?php echo $pm->GetSenderName(); ?>&topic=<?php echo $pm->GetTopic(); ?>">Antworten</a>
                </td>
            </tr>
        </table>
        <?php
    }
}
?>