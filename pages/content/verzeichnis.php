<?php
include_once 'classes/bbcode/bbcode.php';
if($player->GetArank() == 3)
{
    ?>
    <br />
    <a href="?p=verzeichnis_admin">
        <input type="submit" value="Admin" />
    </a>
    <br />
    <?php
}
?>
    <div class="spacer"></div>
<?php
if ($verzeichnisentry != null && ($verzeichnisentry->IsActivated() || $player->GetArank() == 3))
{
    ?>
    <table width="100%" cellspacing="0">
        <tr>
            <td width="300px" class="catGradient borderT borderR borderL"><?php echo $verzeichnisentry->GetName(); ?></td>
            <td class="catGradient borderT borderL">Beschreibung</td>
        </tr>
        <tr>
            <td class="borderT borderR borderL borderB">
                <table>
                    <tr>
                        <td colspan="2"><img src="img/verzeichnis/<?php echo $verzeichnisentry->GetImage(); ?>.png?2" width="250px" height="250px"></td>
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
                        <td><?php echo $verzeichnisentry->GetName(); ?></td>
                    </tr>
                    <tr>
                        <td width="100px">Original:</td>
                        <td><?php echo $verzeichnisentry->GetOriginalName(); ?></td>
                    </tr>
                    <tr>
                        <td width="100px">Rasse:</td>
                        <td><?php echo $verzeichnisentry->GetRasse(); ?></td>
                    </tr>
                    <tr>
                        <td width="100px">Geschlecht:</td>
                        <td><?php echo $verzeichnisentry->GetGeschlecht(); ?></td>
                    </tr>
                    <tr>
                        <td width="100px">Alter:</td>
                        <td><?php echo $verzeichnisentry->GetAlter(); ?></td>
                    </tr>
                    <tr>
                        <td width="100px">Geburtstag:</td>
                        <td><?php echo $verzeichnisentry->GetBirthDay(); ?></td>
                    </tr>
                    <tr>
                        <td width="100px">Größe:</td>
                        <td><?php echo $verzeichnisentry->GetHeight(); ?></td>
                    </tr>
                    <tr>
                        <td width="100px">Herkunft:</td>
                        <td><?php echo $verzeichnisentry->GetBirthPlace(); ?></td>
                    </tr>
                    <tr>
                        <td width="100px">Familie:</td>
                        <td> <?php echo $bbcode->parse($verzeichnisentry->GetFamily()); ?></td>
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
                        <td><?php echo $verzeichnisentry->GetPiratenbande(); ?></td>
                    </tr>
                    <tr>
                        <td width="100px">Position/Rang:</td>
                        <td><?php echo $verzeichnisentry->GetPosition(); ?></td>
                    </tr>
                    <tr>
                        <td width="100px">Kopfgeld:</td>
                        <td><?php echo $verzeichnisentry->GetPvP(); ?></td>
                    </tr>
                    <tr>
                        <td width="100px">Teufelsfrucht:</td>
                        <td><?php echo $verzeichnisentry->GetTF(); ?></td>
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
                        <td><?php echo $verzeichnisentry->GetVoiceActorGer(); ?></td>
                    </tr>
                    <tr>
                        <td width="100px">Japanischer Voice Actor:</td>
                        <td><?php echo $verzeichnisentry->GetVoiceActorJap(); ?></td>
                    </tr>
                    <tr>
                        <td width="100px">Debüt im Anime:</td>
                        <td><?php echo $verzeichnisentry->GetAnime(); ?></td>
                    </tr>
                    <tr>
                        <td width="100px">Debüt im Manga:</td>
                        <td><?php echo $verzeichnisentry->GetManga(); ?></td>
                    </tr>
                </table>
            </td>
            <td class="borderT borderR borderB" valign="top">
                <?php echo $bbcode->parse($verzeichnisentry->GetDescription()); ?>
            </td>
        </tr>
    </table>
    <br />
    <a href="?p=verzeichnis">
        <input type="submit" value="Zurück" />
    </a>
    <?php
    $hide = false;
    $result = $database->Select('*', 'verzeichnis_edit', 'uid="'.$verzeichnisentry->GetUID().'"');
    if($result && $result->num_rows > 0)
    {
        $hide = true;
    }
    ?>
        <Button type="button" onclick="OpenPopupPage('Artikel bearbeiten','verzeichnis/editarticle.php?id=<?= $verzeichnisentry->GetID(); ?>')" <?php if($hide) echo "hidden" ?>>Bearbeiten</Button>
        <div class="spacer"></div>
    <?php
}
else
{
    ?>
    <div class="catGradient borderT borderB" style="width:95%;">
        <b>Verzeichnis</b>
    </div>
    <form method="get" action="?p=verzeichnis">
        <table width="95%" cellspacing="0" border="0" class="borderT borderR borderL borderB">
            <tr>
                <td> <input type="hidden" name="p" value="verzeichnis"></td>
                <td width="45%">
                    <center><input placeholder="Name" style="width:100%;" type="text" name="searchname" value="<?php if (isset($_GET['searchname'])) echo $_GET['searchname']; ?>"></center>
                </td>
                <td width="45%">
                    <center><input type="submit" style="width:50%" value="Suchen"> <Button type="button" onclick="OpenPopupPage('Artikel erstellen','verzeichnis/createarticle.php')">Artikel erstellen</Button></center>

                </td>
            </tr>
        </table>
    </form>
    <div class="spacer"></div>
    <table width="95%" cellspacing="0" border="0">
        <tr>
            <?php
            $where = 'mainpage=1';
            $limit = 10;
            $mainPageList = new Generallist($database, 'verzeichnis', 'name, image', $where, '',	$limit, 'ASC');
            $id = 0;
            $verzeichnisentry = $mainPageList->GetEntry($id);
            while ($verzeichnisentry != null)
            {
            if (($id % 5) == 0)
            {
            ?></tr>
        <tr><?php
            }
            ?>
            <td>
                <a href="?p=verzeichnis&name=<?php echo $verzeichnisentry['name']; ?>">
                    <div class="catGradient borderT borderB" style="width:120px">
                        <b><?php echo $verzeichnisentry['name']; ?></b>
                    </div>
                    <div class="borderR borderL borderB" style="width:120px">
                        <img width="119px" height="119px" src="img/verzeichnis/<?php echo $verzeichnisentry['image']; ?>.png">
                    </div>
                </a>
            </td>
            <?php
            ++$id;
            $verzeichnisentry = $mainPageList->GetEntry($id);
            }
            ?>
        </tr>
    </table>
    <div class="spacer"></div>
    <div class="catGradient borderT borderB" style="width:95%;">
        <b>Alle Einträge</b>
    </div>
    <div class="borderR borderL borderB" style="width:95%; text-align: left;">
        <table width="100%">
            <tr>
                <?php
                $letters = range('A', 'Z');
                foreach ($letters as &$letter)
                {
                    ?>
                    <td>
                        <?php if ($_GET['search'] == $letter)
                        { ?><b><?php } ?>
                            <a href="?p=verzeichnis&search=<?php echo $letter; ?>"><?php echo $letter; ?></a>
                            <?php if ($_GET['search'] == $letter)
                            { ?></b><?php } ?>
                    </td>
                    <?php
                }
                ?>
            </tr>
        </table>
        <?php
        if (isset($_GET['search']) && $_GET['search'] != '' || isset($_GET['searchname']) && $_GET['searchname'] != '')
        {
            if (isset($_GET['search']))
                $search = $database->EscapeString($_GET['search']);
            else if (isset($_GET['searchname']))
                $search = $database->EscapeString($_GET['searchname']);
            ?>
            <div class="spacer"></div>
            <h2><?php
                if (isset($_GET['search']))
                    echo $search;
                else if (isset($_GET['searchname']))
                    echo 'Suchwort: ' . $search;
                ?></h2>
            <ul style="text-align: left;">
                <?php
                if (isset($_GET['search']))
                    $where = 'name LIKE "' . $search . '%"';
                else if (isset($_GET['searchname']))
                    $where = 'name LIKE "%' . $search . '%" OR description LIKE "%'.$search.'%"';
                $limit = 100;
                $searchEntries = new Generallist($database, 'verzeichnis', 'id, name, image', $where, '',	$limit, 'ASC');
                $id = 0;
                $verzeichnisentry = $searchEntries->GetEntry($id);
                while ($verzeichnisentry != null)
                {
                    ?>
                    <li><img width="50px" height="50px" src="img/verzeichnis/<?php echo $verzeichnisentry['image']; ?>.png"> <a href="?p=verzeichnis&name=<?php echo $verzeichnisentry['name']; ?>"><?php echo $verzeichnisentry['name']; ?></a></li>
                    <?php
                    ++$id;
                    $verzeichnisentry = $searchEntries->GetEntry($id);
                }
                ?>
            </ul>
            <?php
        }
        ?>
    </div>
    <br />
    <hr>
    <br />
    <h2>One Piece News</h2>
    <?php
}

// PAGINATOR

$start = 0;
$limit = 5;

if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0)
{
    $start = $limit * ($_GET['page'] - 1);
}

////////////

$id = 0;
$news = new Generallist($database, 'writer', '*', '', 'id', $start . ',' . $limit, 'DESC');
$entry = $news->GetEntry($id);
if($entry != NULL)
{
    $max = 0;
    while($entry != NULL)
    {
        if($max < 1)
        {
            echo "<b>".$entry['titel']." - ".$entry['datum']."</b><br /><br />";
            if($entry['mode'] == "")
            {
                echo "<a><img height='350' width='600' src=".$entry['bild']." /><br /></a>";
            }
            else
            {
                echo "<a href='".$entry['mode']."' target='_blank''><img height='350' width='650' src=".$entry['bild']." /><br /></a>";
            }
            echo "<br />".$entry['text']."<br /><br />";
        }
        else
        {
            ?>
            <hr >
            <details>
                <summary style="table-layout: fixed;"><?php echo "<b>".$entry['titel']." - ".$entry['datum']."</b><br />"; ?></summary>
                <?php
                echo "<br />".$entry['text']."<br /><br />";
                if($entry['mode'] == "")
                {
                    echo "<a><img height='350' width='600' src=".$entry['bild']." /><br /></a>";
                }
                else
                {
                    echo "<a href='".$entry['mode']."' target='_blank''><img height='350' width='650' src=".$entry['bild']." /><br /></a>";
                }
                ?>
            </details>
            <hr>
            <?php
        }
        $id++;
        $entry = $news->GetEntry($id);
        $max += 1;
    }
}
else
{
    echo "Es wurden noch keine Einträge verfasst";
}

$result = $database->Select('COUNT(id) as total', 'writer', '');
$total = 0;
if ($result)
{
    $row = $result->fetch_assoc();
    $total = $row['total'];
    $result->close();
}
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
        <a href="?p=verzeichnis&page=<?php echo $i + 1;
        if (isset($_GET['race'])) echo '&race=' . $_GET['race'];
        if (isset($_GET['user'])) echo '&user=' . $_GET['user'];
        if (isset($_GET['place'])) echo '&place=' . $_GET['place'];
        if (isset($_GET['planet'])) echo '&planet=' . $_GET['planet'];

        ?> ">Seite <?php echo number_format($i + 1,'0', '', '.'); ?></a>
        <?php
        ++$i;
    }
}
?>