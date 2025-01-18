<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'classes/serverurl.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '../../classes/header.php';


$db = 'DATENBANK';
$user = 'USER';
$pw = 'PASSWORT';
    $database = new Database($db, $user, $pw);

    include_once '../../classes/generallist.php';
    include_once '../../classes/fight/fight.php';
?>
<script src="https://code.jquery.com/jquery.js"></script>
<div class="catGradient" style="margin-top: 40px;">
    <h2>
        Kämpfe (Gestern)
    </h2>
</div>
<div style="width: 600px; height: 632px; overflow-y: auto; float:left;">
    <table class="table" style="width: 100%;" id="table">
</div>
<div style="width: 600px; height: 130px; float:left;">
    <?php
        $fightslist = new GeneralList($database, 'lastfights', '*', 'fighters LIKE "%['.$_GET['id'].']%"');
        $bountylist = new GeneralList($database, 'lastfights', '*', 'type=1 AND fighters LIKE "%['.$_GET['id'].']%"');
        $npclist = new GeneralList($database, 'lastfights', '*', 'type=3 AND fighters LIKE "%['.$_GET['id'].']%"');
        $elolist = new GeneralList($database, 'lastfights', '*', 'type=13 AND fighters LIKE "%['.$_GET['id'].']%"');
        $arenalist = new GeneralList($database, 'lastfights', '*', 'type=8 AND fighters LIKE "%['.$_GET['id'].']%"');
        $bountywonlist = new GeneralList($database, 'lastfights', '*', 'type=1 AND winner LIKE "%'.$_GET['id'].'%" AND fighters LIKE "%['.$_GET['id'].']%"');
        $bountylostlist = new GeneralList($database, 'lastfights', '*', 'type=1 AND winner NOT LIKE "%'.$_GET['id'].'%" AND fighters LIKE "%['.$_GET['id'].']%"');
    ?>
    <div class="spacer"></div>
    Kämpfe gesamt: <?php echo number_format($fightslist->GetCount(), 0, '', '.'); ?> <br/>
    NPC gesamt: <?php echo number_format($npclist->GetCount(), 0, '', '.'); ?> <br/>
    Kolosseum gesamt: <?php echo number_format($arenalist->GetCount(), 0, '', '.'); ?> <br/>
    Elokämpfe gesamt: <?php echo number_format($elolist->GetCount(), 0, '', '.'); ?> <br/>
    Kopfgelder gesamt: <?php echo number_format($bountylist->GetCount(), 0, '', '.'); ?> <br/>
    Kopfgelder gewonnen: <?php echo number_format($bountywonlist->GetCount(), 0, '', '.'); ?> <br/>
    Kopfgelder verloren: <?php echo number_format($bountylostlist->GetCount(), 0, '', '.'); ?> <br/>
</div>

<script>
    $(function() {
        // Static values - get them once
        const table = $('table');

        // The height of the window
        const clientheight = 632;

        // How far the table is from the top of the page
        const offset = $(table).offset();

        // Switch the label on the loading text
        /*$('#loading').change(function() {
            const text = this.checked ? 'off' : 'on';
            $(this).prev('span').text(text);
        });*/

        // On reset clear table and repopulate
        /*$('#reset').click(function() {
            table.empty();
            table.populateTable(30,5, true);
        });*/

        // Handle the scroll
        $(document).on('scroll', function() {
            // but only if loading is checked
            //if (!$('#loading').is(':checked')) return;

            // How far have we scrolled
            const scrolltop = $(window).scrollTop();

            // How high is the table now?
            const tableheight = $(table).height();

            // check to see if the bottom of the table is coming into
            // view
            if (tableheight - (scrolltop - offset.top) < clientheight) {
                // Simulate loading data here
                // add some more data
                // This could be a GET request similar to the sample here
                // http://www.marcorpsa.com/ee/t3024.html
                table.populateTable(10,5)
            }
        })

        // set up the table with initial data
        table.populateTable(30,5)
    });
</script>

<script>
    // A simple jQuery extension for populating a table with
    // a given number of rows and columns of data
    jQuery.fn.populateTable = function(nrow, col) {

        this.each(function() {
            //const start = $('tr', this).length;
            //for(var r = start; r < start + row; r++) {
            <?php
            $fightslist = new GeneralList($database, 'lastfights', '*', 'fighters LIKE "%['.$_GET['id'].']%"');
            $id = 0;
            $entry = $fightslist->GetEntry($id);
            if($entry == null)
            {
                ?>
                let tr = this.insertRow();
                tr.insertCell().innerHTML = "Keine Kämpfe vorhanden.";
                <?php
            }
            while($entry != null)
            {
                ?>
                const row = this.insertRow();

                for (let c = 0; c < col; c++)
                {
                    row.insertCell().innerHTML = "<a target='_blank' href='?p=lastfights&fight=<?php echo $entry['id']; ?>'><?php echo date('H:i:s', strtotime($entry['time'])); ?></a>";
                    row.insertCell().innerHTML = "<a target='_blank' title='log' href='?p=lastfightslog&fight=<?php echo $entry['id']; ?>'><?php echo $entry['id']; ?></a>";
                    row.insertCell().innerHTML = "<a target='_blank' href='?p=lastfights&fight=<?php echo $entry['id']; ?>'><?php echo Fight::GetTypeName($entry['type']); ?></a>";
                    <?php
                    /*if($entry['type'] != 1 && $entry['type'] != 13)
                    {
                        ?>
                        row.insertCell().innerHTML = "<a target='_blank' href='?p=lastfights&fight=<?php echo $entry['id']; ?>'><?php echo $entry['name']; ?></a>"
                        <?php
                    }
                    else
                    {
                        $fighters = explode(';', $entry['fighters']);
                        $fighters = str_replace('[', '', $fighters);
                        $fighters = str_replace(']', '', $fighters);
                        foreach ($fighters as $fighter)
                        {
                            if($fighter != $_GET['id'])
                            {
                                $enemy = $fighter;
                            }
                        }
                        $result = $database->Select('*', 'accounts', 'id='.$enemy, 1);
                        if($result)
                        {
                            $enemy = $result->fetch_assoc();
                        }
                        $result = $database->Select('*', 'accounts', 'id='.$_GET['id'], 1);
                        if($result)
                        {
                            $otherPlayer = $result->fetch_assoc();
                        }
                        $color = 'red';
                        if($enemy['clan'] != 0 && $enemy['clan'] == $otherPlayer['clan'])
                            $color = 'green';
                        ?>
                        row.insertCell().innerHTML = "<?php echo $entry['name']; ?> gegen <a target='_blank' href='?p=profil&id=<?php echo $enemy['id']; ?>' style='color: <?php echo $color; ?>'><?php echo $enemy['name']; ?></a>";
                        <?php
                        if(in_array($_GET['id'], explode(';', $entry['winner'])))
                        {
                            ?>
                            row.insertCell().innerHTML = '<span style="color: green;">Gewonnen</span>';
                            <?php
                        }
                        else
                        {
                            ?>
                            row.insertCell().innerHTML = '<span style="color: red;">Verloren</span>';
                            <?php
                        }
                        if(in_array($_GET['id'], explode(';', $entry['winner'])) && in_array($_GET['id'], explode(';', $entry['gainaccs'])))
                        {
                            if($entry['zeni'] > 0)
                            {
                                $berry = number_format($entry['berry'], 0, '', '.');
                                ?>
                                row.insertCell().innerHTML = "<?php echo $berry; ?> <img src='img/offtopic/BerrySymbol.png' alt='Berry' title='Berry' style='position: relative; top: 2px; height: 20px; width: 13px;'/>"
                                <?php
                            }
                        }
                        if(in_array($_GET['id'], explode(';', $entry['winner'])) && in_array($_GET['id'], explode(';', $entry['gainaccs'])))
                        {
                            if($entry['gold'] > 0)
                            {
                                $gold = number_format($entry['gold'], 0, '', '.');
                                ?>
                                row.insertCell().innerHTML = "<?php echo $gold; ?> <img src='img/offtopic/GoldSymbol.png' alt='Gold' title='Gold' style='position: relative; top: 2px; height: 20px; width: 20px;'/>"
                                <?php
                            }
                        }
                        if(in_array($_GET['id'], explode(';', $entry['winner'])) && in_array($_GET['id'], explode(';', $entry['gainaccs'])))
                        {
                            if($entry['kopfgeld'] > 0)
                            {
                                $pvp = number_format($entry['kopfgeld'], 0, '', '.');
                                ?>
                                row.insertCell().innerHTML = "<?php echo $pvp; ?> <img src='img/offtopic/BerrySymbol.png' alt='Berry' title='Berry' style='position: relative; top: 2px; height: 20px; width: 13px;'/>"
                                <?php
                            }
                        }
                    }*/
                    $id++;
                    $entry = $fightslist->GetEntry($id);
                    ?>
                }
                <?php
            }
            ?>
        });

        return this;
    }
</script>