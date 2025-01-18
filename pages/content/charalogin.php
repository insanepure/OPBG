<div class="spacer"></div>
<?php
//$regi wird in der head/register.php gesetzt
if ($account->IsLogged() && $userLoginActive)
{
    $cID = 0;
    $charas = new Generallist($database, 'accounts', 'userid, id', 'userid="' . $account->Get('id') . '"', 'id', 999);
    $id = 0;
    $entry = $charas->GetEntry($id);
    ?>
    Du musst die <b><a href="?p=regeln">Regeln</a></b> zum Thema "<b>Mehrere Charaktere/Accounts</b>" beachten, wenn du mehrere Charaktere erstellst und spielst.<br />
    Bei missachten der Regeln kann es zu einer Sperrung deines kompletten Accounts kommen.<br/>
    <div class="spacer"></div>
    <hr>
    <b>Charaktere</b><br/><br/>
    <table>
        <tr>
            <?php
            $idx = 0;
            $isDonor = 0;
            while ($entry != null)
            {
                $displayedPlayer = new Player($database, $entry['id']);


                if ($idx == 3)
                {
                    $idx = 0;
                    echo "</tr><tr>";
                }
                ++$idx;

                if($isDonor == 0 && $displayedPlayer->IsDonator())
                    $isDonor = 1;

                ?>
                <td>
                    <table width="200px" cellspacing="0" border="0">
                        <tr>
                            <td class="catGradient borderT borderB" align="center">
                                <b>
                                    <div class="schatten"><?php echo $displayedPlayer->GetName(); ?></div>
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td class="SideMenuInfo borderL borderR borderB" align="center">
                                <img height="80" src="<?php echo $displayedPlayer->GetImage(); ?>">
                            </td>
                        </tr>
                        <?php
                        if ($displayedPlayer->GetAction() != 0)
                        {
                            $action = $actionManager->GetAction($displayedPlayer->GetAction());
                            ?>
                            <tr>
                                <td class="SideMenuInfo borderL borderR borderB" align="center">
                                    <?php
                                    $image = 'actions/'.$action->GetImage();
                                    if ($action->GetType() == 5)
                                    {
                                        $attackName = '';

                                        $result = $database->Select('id, name, image', 'attacks', 'id = ' . $displayedPlayer->GetLearningAttack() . '', 1);
                                        $attackID = 0;
                                        if ($result)
                                        {
                                            $row = $result->fetch_assoc();
                                            $attackName = $row['name'];
                                            $image = 'attacks/'.$row['image'];
                                            $result->close();
                                        }
                                        echo $attackName . ' ';
                                    }
                                    echo $action->GetName();
                                    ?>
                                    <div class="spacer"></div>
                                    <div class="tooltip" style="position: relative;">
                                        <img src="img/<?php echo $image; ?>.png" alt="<?php echo $action->GetName(); ?>" title="<?php echo $action->GetName(); ?>" style="width: 50px; height: 50px; border-radius:25%; overflow:hidden;">
                                        <span class="tooltiptext" style="width:180px; top:-80px; left:-65px;"><?php echo $action->GetDescription() . ' ' . $attackName; ?></span>
                                    </div>
                                    <br />
                                    <div id="cID<?php echo $cID; ?>">Init
                                        <script>
                                            countdown(<?php echo $displayedPlayer->GetActionCountdown(); ?>, 'cID<?php echo $cID; ?>');
                                        </script>
                                    </div>
                                    <div class="spacer"></div>
                                </td>
                            </tr>
                            <?php
                            ++$cID;
                        }
                        if ($displayedPlayer->GetTravelAction() != 0)
                        {
                            $travelAction = $actionManager->GetAction($displayedPlayer->GetTravelAction());
                            ?>
                            <tr>
                                <td class="SideMenuInfo borderL borderR borderB" align="center">
                                    <?php
                                    echo $travelAction->GetName();
                                    $travelplace = new Place($database, $displayedPlayer->GetTravelPlace(), null);
                                    $travelplanet = new Planet($database, $displayedPlayer->GetTravelPlanet());
                                    if ($travelAction->GetType() == 4)
                                    {

                                        if($travelAction->GetID() == 2)
                                            echo '<br/> zum Ort: ' . $travelplace->GetName();
                                        else if($travelAction->GetID() == 13)
                                            echo '<br/> zum Meer: ' . $travelplanet->GetName();
                                    }
                                    ?>
                                    <div class="spacer"></div>
                                    <div class="tooltip" style="position: relative;">
                                        <img src="img/actions/<?php echo $travelAction->GetImage(); ?>.png" alt="<?php echo $travelAction->GetName(); ?>" title="<?php echo $travelAction->GetName(); ?>" style="width: 50px; height: 50px; border-radius:25%; overflow:hidden;">
                                        <span class="tooltiptext" style="width:180px; top:-80px; left:-65px;"><?php echo $travelAction->GetDescription(); ?></span>
                                    </div>
                                    <br />
                                    <div id="cID<?php echo $cID; ?>">Init
                                        <script>
                                            countdown(<?php echo $displayedPlayer->GetTravelActionCountdown(); ?>, 'cID<?php echo $cID; ?>');
                                        </script>
                                    </div>
                                    <div class="spacer"></div>
                                </td>
                            </tr>
                            <?php
                            ++$cID;
                        }
                        ?>
                        <tr>
                            <td class="SideMenuInfo borderL borderR borderB" align="center">
                                <form method="POST" action="?p=charalogin&id=<?php echo $entry['id']; ?>">
                                    <input type="hidden" name="charnames" value="">
                                    <input type="checkbox" name="logged" id="logged<?php echo $entry['id']; ?>" value="Ja"> <label style="cursor: pointer;" for="logged<?php echo $entry['id']; ?>">Eingeloggt bleiben</label><br>
                                    <input type="submit" value="Einloggen">
                                </form>
                            </td>
                        </tr>
                    </table>
                </td>
                <?php
                ++$id;
                $entry = $charas->GetEntry($id);
            }
            ?>
        </tr>
    </table>
    <div class="spacer"></div>
    <?php
    $sittingcharas = new Generallist($database, 'accounts', 'id, sitter, sitterstart, sitterend', 'sitter="' . $account->Get('id') . '" AND sitterstart <= "'.date("Y-m-d").'" AND sitterend >= "'.date("Y-m-d").'"', 'id', 999);
    $sid = 0;
    $entry = $sittingcharas->GetEntry($sid);
    if($sittingcharas->GetCount() > 0)
    {
        ?>
        <hr>
        <b>Sitting Accounts</b>
        <br />
        <br />
        <table>
            <tr>
                <?php
                $idx = 0;
                while ($entry != null)
                {
                    $displayedPlayer = new Player($database, $entry['id']);

                    if ($idx == 3)
                    {
                        $idx = 0;
                        echo "</tr><tr>";
                    }
                    ++$idx;

                    $image = "img/imagefail.png";
                    if ($displayedPlayer->GetImage() != '')
                        $image = $displayedPlayer->GetImage();
                    ?>
                    <td>
                        <table width="200px" cellspacing="0" border="0">
                            <tr>
                                <td class="catGradient borderT borderB" align="center">
                                    <b>
                                        <div class="schatten"><?php echo $displayedPlayer->GetName(); ?></div>
                                    </b>
                                </td>
                            </tr>
                            <tr>
                                <td class="SideMenuInfo borderL borderR borderB" align="center">
                                    <img height="80" src="<?php echo $image; ?>">
                                </td>
                            </tr>
                            <?php
                            if ($displayedPlayer->GetAction() != 0)
                            {
                                $action = $actionManager->GetAction($displayedPlayer->GetAction());
                                ?>
                                <tr>
                                    <td class="SideMenuInfo borderL borderR borderB" align="center">
                                        <?php
                                        $image = 'actions/'.$action->GetImage();
                                        if ($action->GetType() == 5)
                                        {
                                            $attackName = '';

                                            $result = $database->Select('id, name, image', 'attacks', 'id = ' . $displayedPlayer->GetLearningAttack() . '', 1);
                                            $attackID = 0;
                                            if ($result)
                                            {
                                                $row = $result->fetch_assoc();
                                                $attackName = $row['name'];
                                                $image = 'attacks/'.$row['image'];
                                                $result->close();
                                            }
                                            echo $attackName . ' ';
                                        }
                                        echo $action->GetName();
                                        ?>
                                        <div class="spacer"></div>
                                        <div class="tooltip" style="position: relative;">
                                            <img src="img/<?php echo $image; ?>.png" alt="<?php echo $action->GetName(); ?>" title="<?php echo $action->GetName(); ?>" style="width: 50px; height: 50px; border-radius:25%; overflow:hidden;">
                                            <span class="tooltiptext" style="width:180px; top:-80px; left:-65px;"><?php echo $action->GetDescription() . ' ' . $attackName; ?></span>
                                        </div>
                                        <br />
                                        <div id="cID<?php echo $cID; ?>">Init
                                            <script>
                                                countdown(<?php echo $displayedPlayer->GetActionCountdown(); ?>, 'cID<?php echo $cID; ?>');
                                            </script>
                                        </div>
                                        <div class="spacer"></div>
                                    </td>
                                </tr>
                                <?php
                                ++$cID;
                            }
                            if ($displayedPlayer->GetTravelAction() != 0)
                            {
                                $travelAction = $actionManager->GetAction($displayedPlayer->GetTravelAction());
                                ?>
                                <tr>
                                    <td class="SideMenuInfo borderL borderR borderB" align="center">
                                        <?php
                                        echo $travelAction->GetName();
                                        if ($travelAction->GetType() == 4)
                                        {
                                            echo '<br/>Ort: ' . $displayedPlayer->GetTravelPlace();
                                        }
                                        ?>
                                        <br />
                                        <div id="cID<?php echo $cID; ?>">Init
                                            <script>
                                                countdown(<?php echo $displayedPlayer->GetTravelActionCountdown(); ?>, 'cID<?php echo $cID; ?>');
                                            </script>
                                        </div>
                                        <div class="spacer"></div>
                                    </td>
                                </tr>
                                <?php
                                ++$cID;
                            }
                            ?>
                            <tr>
                                <td class="SideMenuInfo borderL borderR borderB" align="center">
                                    <form method="POST" action="?p=charalogin&id=<?php echo $entry['id']; ?>">
                                        <input type="hidden" name="charnames" value="">
                                        <input type="checkbox" name="logged" id="logged<?php echo $entry['id']; ?>" value="Ja"> <label style="cursor: pointer;" for="logged<?php echo $entry['id']; ?>">Eingeloggt bleiben</label><br>
                                        <input type="submit" value="Einloggen">
                                    </form>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <?php
                    ++$sid;
                    $entry = $sittingcharas->GetEntry($sid);
                }
                ?>
            </tr>
        </table>
        <?php
    }
    if ($charas->GetCount() < 6)
    { ?>
        <hr>
        <b>Neuer Charakter</b>
        <div class="spacer"></div>
        <form method="POST" action="?p=characreate">
            <input type="submit" value="erstellen">
        </form>
        <?php
    }
    ?>
    <div class="spacer"></div>
    <hr>
    <b>Passwort ändern</b>
    <div class="spacer"></div>
    <form name="form1" action="?p=charalogin&a=changepw" method="post" enctype="multipart/form-data">
        <table>
            <tr>
                <td>Passwort:</td>
                <td><input type="password" name="pw" style="width:250px"></td>
            </tr>
            <tr>
                <td>Wiederholen:</td>
                <td><input type="password" name="pw2" style="width:250px"></td>
            </tr>
        </table>
        <input type="submit" value="Passwort ändern">
    </form>
    <div class="spacer"></div>
    <hr>
    <b>Account löschen</b><br /><br />
    <form method="POST" action="?p=charalogin&a=accdelete">
        <input type="checkbox" name="logged" value="Ja">Ja ich bin sicher<br>
        <input type="submit" value="löschen">
    </form>
    <?php
}
?>
<script>
    var charnames = document.getElementsByName("charnames");
    for (let i = 0; i < charnames.length; i++) {
        charnames[i].value = localStorage.getItem('chars');
    }
</script>
