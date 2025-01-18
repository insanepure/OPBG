<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'classes/serverurl.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '../../classes/header.php';


$db = 'DATENBANK';
$user = 'USER';
$pw = 'PASSWORT';
    $database = new Database($db, $user, $pw);

    include_once '../../classes/generallist.php';
?>
<div style="width: 200px; height: 670px; overflow-y: auto; float:left;">
    <button style="width: 150px;" onclick="openlog(0)">
        Heute
    </button>
    <?php
        $logs = new GeneralList($database, 'logs', '*', 'playerid='.$_GET['id'], 'time', 9999);
        $id = 0;
        $entry = $logs->GetEntry($id);
        while($entry != null)
        {

            ?>
            <button style="width: 150px;" onclick="openlog(<?php echo $entry['id']; ?>)">
                <?php echo date('d.m.Y', strtotime($entry['time'])); ?>
            </button>
            <?php
            $id++;
            $entry = $logs->GetEntry($id);
        }
    ?>
</div>
<div id="log0" class="log" style="width:400px; height: 670px; overflow-x: auto;" hidden>
    <?php
        $result = $database->Select('debuglog', 'accounts', 'id='.$_GET['id'], '1');
        if($result)
        {
            $row = $result->fetch_assoc();
            echo $row['debuglog'];
        }
    ?>
</div>
<?php
    $logs = new GeneralList($database, 'logs', '*', 'playerid='.$_GET['id'], 'time');

    $id = 0;
    $entry = $logs->GetEntry($id);
    while($entry != null)
    {
        ?>
        <div id="log<?php echo $entry['id']; ?>" class="log" style="width:400px; height: 670px; overflow-x: auto;" hidden>
            <?php
                echo $entry['log'];
            ?>
        </div>
        <?php
        $id++;
        $entry = $logs->GetEntry($id);
    }
    ?>
<script>
    function openlog(id)
    {
        var logs = document.getElementsByClassName('log');
        var log = document.getElementById('log'+id);
        for (let i = 0; i < logs.length; i++) {
            logs[i].hidden = true;
        }
        log.hidden = false;
    }
</script>
