<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'classes/ticketsystem/ticketmanager.php';
    $ticketManager = new TicketManager($database);
?>
<h1>Aktuell gibt es <?= number_format($ticketManager->NumOpenTickets(),0,'','.'); ?> offene Tickets</h1>
<?php
    if($ticketManager->NumOpenTickets() > 0)
    {
        $openTickets = $ticketManager->GetOpenTickets();
        foreach($openTickets as $ticket)
        {
            $ticketOwner = new Player($database, $ticket['ersteller']);
            if($ticket['gelesen'] == 0)
            {
                ?>
                <a href="?p=ticketsystem&id=<?= $ticket['id'] ?>">
                    Zum Ticket von <?= $ticketOwner->GetName() ?>
                </a>

                <?php
            }
            else
            {
                ?>
                <a href="?p=ticketsystem&id=<?= $ticket['id'] ?>">
                <span style="color: red;">
                    Zum Ticket von <?= $ticketOwner->GetName() ?>
                </span>
                </a>
                <?php
            }
            echo '<div class="spacer"></div>';
        }
    }

    if($player->GetArank() == 3)
    {
// PAGINATOR

        $start = 0;
        $limit = 15;

        if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0)
        {
            $start = $limit * ($_GET['page'] - 1);
        }

////////////
/// ?>
        <table width="98%" cellspacing="0" border="1">
            <tr>
                <td colspan="5" class="catGradient" style="text-align: center;">
                    <b>Ticket Archiv</b>
                </td>
            </tr>
            <tr>
                <td style="width: 20%; text-align: center;">Erstellt am:</td>
                <td style="width: 20%; text-align: center;">Ticket:</td>
                <td style="width: 20%; text-align: center;">Von:</td>
                <td style="width: 20%; text-align: center;">Geschlossen:</td>
                <td style="width: 20%; text-align: center;">Archiv:</td>
            </tr>
            <?php

            $id = 0;
            $where = 'active=1';
            if(isset($_GET['user']) && is_numeric($_GET['user']) && $_GET['user'] != 0)
                $where = 'ersteller="'.$_GET['user'].'"';
            $tickets = new Generallist($database, 'ticket', '*', $where, 'id', $start . ',' . $limit, 'DESC');
            $entry = $tickets->GetEntry($id);
            while($entry != NULL)
            {
                $betreff = str_replace('Allgemeine ', '', $entry['betreff']);
                $players = new Player($database, $entry['ersteller']);
                $closer = new Player($database, $entry['closedby']);
                ?>
                <tr>
                    <td style="width: 20%; text-align: center;"><?= $entry['erstellt']; ?></td>
                    <td style="width: 20%; text-align: center;"><a href="?p=ticketsystem&id=<?= $entry['id']; ?>"><?php echo $betreff; ?></a></td>
                    <td style="width: 20%; text-align: center;"><a href="?p=profil&id=<?= $players->GetID(); ?>"><?= $players->GetName(); ?></a></td>
                    <td style="width: 20%; text-align: center;"><a href="?p=profil&id=<?= $closer->GetID(); ?>"><?= $closer->GetName(); ?></a></td>
                    <td style="width: 20%; text-align: center;"><b><a href='?p=admintickets&user=<?php echo $players->GetID(); ?>'>Alle Tickets</a></b></td>
                </tr>
                <?php
                $id++;
                $entry = $tickets->GetEntry($id);
            }
            ?>
        </table>
            <div class="spacer"></div>
            <form method="get">
                <table style="width: 90%;">
                    <tr>
                        <td>
                            Alle Tickets von:
                        </td>
                        <td>
                            <input type="hidden" name="p" value="admintickets">
                            <select class="select" name="user" style="width:200px;">
                                <option value="0">Spieler auswählen</option>
                                <?php
                                    $accounts = new Generallist($database, 'accounts', 'id, name', '', '', 99999999999, 'ASC');
                                    $id = 0;
                                    $entry = $accounts->GetEntry($id);
                                    while ($entry != null)
                                    {
                                        ?>
                                        <option value="<?php echo $entry['id']; ?>" <?php if ($_GET['user'] == $entry['id']) echo 'selected'; ?>> <?php echo $entry['name']; ?></option>
                                        <?php
                                        ++$id;
                                        $entry = $accounts->GetEntry($id);
                                    }
                                ?>
                            </select>
                        </td>
                        <td>
                            <input type="submit" value="Anzeigen">
                        </td>
                    </tr>
                </table>
            </form>
            <?php
            $result = $database->Select('COUNT(id) as total', 'ticket');
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
                    if(!isset($_GET['page']) && $i == 0 || isset($_GET['page']) && $_GET['page'] == ($i + 1))
                    {
                        ?>
                        <b>Seite <?php echo number_format($i + 1,'0', '', '.'); ?></b>
                        <?php
                    }
                    else
                    {
                        ?>
                        <a href="?p=admintickets&page=<?php echo $i + 1; ?>">Seite <?php echo number_format($i + 1,'0', '', '.'); ?></a>
                        <?php
                    }
                    ++$i;
                }
            }
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
