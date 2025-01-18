<?php
if($player->GetArank() < 3)
    return;


if(isset($_GET['s']) && $_GET['s'] == "edit" && isset($_GET['a']) && $_GET['a'] == "show")
{
    $id = $_GET['id'];
    $result = $database->Select('*', 'verzeichnis_edit', 'uid="'.$id.'"');
    if($result && $result->num_rows > 0)
    {
        $verzeichnis = $result->fetch_assoc();
        ?>
        <table width="100%" cellspacing="0">
            <tr>
                <td width="300px" class="catGradient borderT borderR borderL"><?php echo $verzeichnis['name']; ?></td>
                <td class="catGradient borderT borderL">Beschreibung</td>
            </tr>
            <tr>
                <td class="borderT borderR borderL borderB">
                    <table>
                        <tr>
                            <td colspan="2"><img src="img/verzeichnis/<?php echo $verzeichnis['image']; ?>.png?2" width="250px" height="250px"></td>
                        </tr>
                    </table>
                    <table width="100%">
                        <tr>
                            <td width="300px" class="catGradient borderT borderR borderL">
                                <center><b>Allgemein</b></center>
                            </td>
                        </tr>
                    </table>
                    <table width="100%">
                        <tr>
                            <td width="100px">Name:</td>
                            <td><?php echo $verzeichnis['name']; ?></td>
                        </tr>
                        <tr>
                            <td width="100px">Original:</td>
                            <td><?php echo $verzeichnis['romaji']; ?></td>
                        </tr>
                        <tr>
                            <td width="100px">Rasse:</td>
                            <td><?php echo $verzeichnis['rasse']; ?></td>
                        </tr>
                        <tr>
                            <td width="100px">Geschlecht:</td>
                            <td><?php echo $verzeichnis['geschlecht']; ?></td>
                        </tr>
                        <tr>
                            <td width="100px">Alter:</td>
                            <td><?php echo $verzeichnis['alter']; ?></td>
                        </tr>
                        <tr>
                            <td width="100px">Geburtstag:</td>
                            <td><?php echo $verzeichnis['birthday']; ?></td>
                        </tr>
                        <tr>
                            <td width="100px">Größe:</td>
                            <td><?php echo $verzeichnis['groesse']; ?></td>
                        </tr>
                        <tr>
                            <td width="100px">Herkunft:</td>
                            <td><?php echo $verzeichnis['place']; ?></td>
                        </tr>
                        <tr>
                            <td width="100px">Familie:</td>
                            <td> <?php echo $bbcode->parse($verzeichnis['family']); ?></td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td width="300px" class="catGradient borderT borderR borderL">
                                <center><b>Piraten / Marine</b></center>
                            </td>
                        </tr>
                    </table>
                    <table width="100%">
                        <tr>
                            <td width="100px">Piratenbande/Organisation:</td>
                            <td><?php echo $verzeichnis['bande']; ?></td>
                        </tr>
                        <tr>
                            <td width="100px">Position/Rang:</td>
                            <td><?php echo $verzeichnis['position']; ?></td>
                        </tr>
                        <tr>
                            <td width="100px">Kopfgeld:</td>
                            <td><?php echo $verzeichnis['kopfgeld']; ?></td>
                        </tr>
                        <tr>
                            <td width="100px">Teufelsfrucht:</td>
                            <td><?php echo $verzeichnis['teufelsfrucht']; ?></td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td width="300px" class="catGradient borderT borderR borderL">
                                <center><b>Manga / Anime</b></center>
                            </td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td width="100px">Deutscher Voice Actor:</td>
                            <td><?php echo $verzeichnis['voiceactorger']; ?></td>
                        </tr>
                        <tr>
                            <td width="100px">Japanischer Voice Actor:</td>
                            <td><?php echo $verzeichnis['voiceactorjap']; ?></td>
                        </tr>
                        <tr>
                            <td width="100px">Debüt im Anime:</td>
                            <td><?php echo $verzeichnis['anime']; ?></td>
                        </tr>
                        <tr>
                            <td width="100px">Debüt im Manga:</td>
                            <td><?php echo $verzeichnis['manga']; ?></td>
                        </tr>
                    </table>
                </td>
                <td class="borderT borderR borderB" valign="top">
                    <?php echo $bbcode->parse($verzeichnis['description']); ?>
                </td>
            </tr>
        </table>
        <br />
        <a href="?p=verzeichnis_admin">
            <input type="submit" value="Zurück" />
        </a>
        <?php
    }
}
else if(isset($_GET['s']) && $_GET['s'] == "new" && isset($_GET['a']) && $_GET['a'] == "show")
{

    $id = $_GET['id'];
    $result = $database->Select('*', 'verzeichnis_new', 'uid="'.$id.'"');
    if($result && $result->num_rows > 0)
    {
        $verzeichnis = $result->fetch_assoc();
        ?>
        <table width="100%" cellspacing="0">
            <tr>
                <td width="300px" class="catGradient borderT borderR borderL"><?php echo $verzeichnis['name']; ?></td>
                <td class="catGradient borderT borderL">Beschreibung</td>
            </tr>
            <tr>
                <td class="borderT borderR borderL borderB">
                    <table>
                        <tr>
                            <td colspan="2"><img src="img/verzeichnis/<?php echo $verzeichnis['image']; ?>.png?2" width="250px" height="250px"></td>
                        </tr>
                    </table>
                    <table width="100%">
                        <tr>
                            <td width="300px" class="catGradient borderT borderR borderL">
                                <center><b>Allgemein</b></center>
                            </td>
                        </tr>
                    </table>
                    <table width="100%">
                        <tr>
                            <td width="100px">Name:</td>
                            <td><?php echo $verzeichnis['name']; ?></td>
                        </tr>
                        <tr>
                            <td width="100px">Original:</td>
                            <td><?php echo $verzeichnis['romaji']; ?></td>
                        </tr>
                        <tr>
                            <td width="100px">Rasse:</td>
                            <td><?php echo $verzeichnis['rasse']; ?></td>
                        </tr>
                        <tr>
                            <td width="100px">Geschlecht:</td>
                            <td><?php echo $verzeichnis['geschlecht']; ?></td>
                        </tr>
                        <tr>
                            <td width="100px">Alter:</td>
                            <td><?php echo $verzeichnis['alter']; ?></td>
                        </tr>
                        <tr>
                            <td width="100px">Geburtstag:</td>
                            <td><?php echo $verzeichnis['birthday']; ?></td>
                        </tr>
                        <tr>
                            <td width="100px">Größe:</td>
                            <td><?php echo $verzeichnis['groesse']; ?></td>
                        </tr>
                        <tr>
                            <td width="100px">Herkunft:</td>
                            <td><?php echo $verzeichnis['place']; ?></td>
                        </tr>
                        <tr>
                            <td width="100px">Familie:</td>
                            <td> <?php echo $bbcode->parse($verzeichnis['family']); ?></td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td width="300px" class="catGradient borderT borderR borderL">
                                <center><b>Piraten / Marine</b></center>
                            </td>
                        </tr>
                    </table>
                    <table width="100%">
                        <tr>
                            <td width="100px">Piratenbande/Organisation:</td>
                            <td><?php echo $verzeichnis['bande']; ?></td>
                        </tr>
                        <tr>
                            <td width="100px">Position/Rang:</td>
                            <td><?php echo $verzeichnis['position']; ?></td>
                        </tr>
                        <tr>
                            <td width="100px">Kopfgeld:</td>
                            <td><?php echo $verzeichnis['kopfgeld']; ?></td>
                        </tr>
                        <tr>
                            <td width="100px">Teufelsfrucht:</td>
                            <td><?php echo $verzeichnis['teufelsfrucht']; ?></td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td width="300px" class="catGradient borderT borderR borderL">
                                <center><b>Manga / Anime</b></center>
                            </td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td width="100px">Deutscher Voice Actor:</td>
                            <td><?php echo $verzeichnis['voiceactorger']; ?></td>
                        </tr>
                        <tr>
                            <td width="100px">Japanischer Voice Actor:</td>
                            <td><?php echo $verzeichnis['voiceactorjap']; ?></td>
                        </tr>
                        <tr>
                            <td width="100px">Debüt im Anime:</td>
                            <td><?php echo $verzeichnis['anime']; ?></td>
                        </tr>
                        <tr>
                            <td width="100px">Debüt im Manga:</td>
                            <td><?php echo $verzeichnis['manga']; ?></td>
                        </tr>
                    </table>
                </td>
                <td class="borderT borderR borderB" valign="top">
                    <?php echo $bbcode->parse($verzeichnis['description']); ?>
                </td>
            </tr>
        </table>
        <br />
        <a href="?p=verzeichnis_admin">
            <input type="submit" value="Zurück" />
        </a>
        <?php
    }
}
else
{
?>

    <div class="spacer"></div>
    <table width="98%" cellspacing="0" border="1" class="borderB borderR borderL">
        <tr>
            <td class="catGradient borderB borderT" colspan="4" align="center"><b>Verzeichnis Einträge</b></td>
        </tr>
        <tr>
            <td width="40%" style="text-align:center">
                <b>Eintrag</b>
            </td>
            <td width="15%" style="text-align:center">
                <b>Status</b>
            </td>
            <td width="20%" style="text-align:center">
                <b>Bearbeiter</b>
            </td>
            <td width="20%" style="text-align:center">
                <b>Aktion</b>
            </td>
        </tr>
        <?php
        $edit = new Generallist($database, 'verzeichnis_edit', '*', '', 'id');
        $id = 0;
        $entry = $edit->GetEntry($id);
        while ($entry != null)
        {
            $result = $database->Select('id', 'accounts', 'id = '.$entry['last_editor'], 1);
            $otherPlayer = false;
            if ($result)
            {
                if ($result->num_rows > 0)
                {
                    $row = $result->fetch_assoc();
                    $otherPlayer = new Player($database, $row['id']);
                    $result->close();
                }
            }
            ?>
            <tr>
                <td>
                    <a href="?p=verzeichnis&name=<?= $entry['name']; ?>"><?= $entry['name'] ?></a>
                </td>
                <td>
                    Editiert
                </td>
                <td>
                    <a href="?p=profil&id=<?= $otherPlayer->GetID() ?>"><?= $otherPlayer->GetName() ?></a>
                </td>
                <td>
                    <a href='?p=verzeichnis_admin&id=<?php echo $entry['uid']; ?>&a=verify_edit'>Verifizieren</a><br/>
                    <a href='?p=verzeichnis_admin&id=<?php echo $entry['uid']; ?>&s=edit&a=show'>Aufrufen</a><br/>
                    <a href='?p=verzeichnis_admin&id=<?php echo $entry['uid']; ?>&a=delete_edit'>Verwerfen</a>
                </td>
            </tr>
            <?php
            $id++;
            $entry = $edit->GetEntry($id);
        }


        $new = new Generallist($database, 'verzeichnis_new', '*', '', 'id');
        $id = 0;
        $entry = $new->GetEntry($id);
        while ($entry != null)
        {
            $result = $database->Select('id', 'accounts', 'id = '.$entry['last_editor'], 1);
            $otherPlayer = false;
            if ($result)
            {
                if ($result->num_rows > 0)
                {
                    $row = $result->fetch_assoc();
                    $otherPlayer = new Player($database, $row['id']);
                    $result->close();
                }
            }
            ?>
            <tr>
                <td>
                    <?= $entry['name'] ?>
                </td>
                <td>
                    Neu
                </td>
                <td>
                    <a href="?p=profil&id=<?= $otherPlayer->GetID() ?>"><?= $otherPlayer->GetName() ?></a>
                </td>
                <td>
                    <a href='?p=verzeichnis_admin&id=<?php echo $entry['uid']; ?>&a=verify_new'>Verifizieren</a><br/>
                    <a href='?p=verzeichnis_admin&id=<?php echo $entry['uid']; ?>&s=new&a=show'>Ansehen</a><br/>
                    <a href='?p=verzeichnis_admin&id=<?php echo $entry['uid']; ?>&a=delete_new'>Löschen</a>
                </td>
            </tr>
            <?php
            $id++;
            $entry = $new->GetEntry($id);
        }
        ?>
    </table>


    <br />
    <a href="?p=verzeichnis">
        <input type="submit" value="Zurück" />
    </a>

<?php
}
?>