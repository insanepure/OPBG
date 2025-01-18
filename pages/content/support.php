<?php
function displaySupport($database, $arank, $titel, $limit = 50)
{
    $players = new Generallist($database, 'accounts', 'id, arank, name, team, charimage, beschreibung',  'arank="'.$arank.'" AND team="'.$titel.'"', 'id', $limit);
    $id = 0;
    $entry = $players->GetEntry($id);
    if($entry != null && $arank == 3 && $titel == 3)
    {
        ?>
        <h3>
            Administratoren
        </h3>
        <?php
    }
    else if($entry != null && $arank == 3 && $titel == 4)
    {
        ?>
        <br />
        <br />
        <hr>
        <h3>Director</h3>
        <?php
    }
    else if($entry != null && $arank == 2 && $titel == 2)
    {
        ?>
        <br />
        <br />
        <hr>
        <h3>
            Team
        </h3>
        <?php
    }
    while ($entry != null)
    {
        ?>
        <table border="1" cellpadding="1" cellspacing="1" summary="">
            <tr>
                <td bgcolor="#FF0000" align="center"><a href="?p=profil&id=<?php echo $entry['id']; ?>"><?php echo $entry['name']; ?></a></td>
                <td bgcolor="#FF0000" align="center"><b>Charakter</b></td>
            </tr>
            <tr>
                <td>
                    <img src="<?php echo $entry['charimage']; ?>" alt="<?php echo $entry['name']; ?>" title="<?php echo $entry['name']; ?>" width="200" height="200" /><br />
                </td>
                <td><?php echo $entry['beschreibung']; ?></td>
            </tr>
        </table>
        <br />
        <?php
        ++$id;
        $entry = $players->GetEntry($id);
    }
}
?>
    <h2>
        Team
    </h2>

    Falls du Hilfe im Spiel benötigst, so zögere nicht einem aus dem Team zu schreiben.<br />

    <hr>
<?php
displaySupport($database, 3, 3, 2);
displaySupport($database, 3, 4,2);
displaySupport($database, 2, 2);
?>