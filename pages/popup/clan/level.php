<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
    include_once '../../../classes/header.php';
    include_once '../../../classes/clan/clan.php';

    $allowed = array('ad', 'def', 'atk', 'lp', 'level');
    $stat = '';
    $image = '';
    if(!isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['a']) || !in_array($_GET['a'], $allowed))
    {
        return;
    }

    if($_GET['id'] != $player->GetClan())
    {
        return;
    }

    $clan = new Clan($database, $_GET['id']);
    if (!$clan->IsValid() || !$clan->GetRankPermission($player->GetClanRank(), 'management'))
    {
        return;
    }

    switch ($_GET['a']) {
        case 'ad':
            $stat = 'AD';
            $image = 'img/offtopic/AD.png?1';
            $berry = number_format($clan->GetLevelUpCostBerry($clan->GetAD() + 1), 0, '', '.');
            $gold = number_format($clan->GetLevelUpCostGold($clan->GetAD() + 1), 0, '', '.');
            break;
        case 'lp':
            $stat = 'LP';
            $image = 'img/offtopic/LP.png?1';
            $berry = number_format($clan->GetLevelUpCostBerry($clan->GetLP() + 1), 0, '', '.');
            $gold = number_format($clan->GetLevelUpCostGold($clan->GetLP() + 1), 0, '', '.');
            break;
        case 'atk':
            $stat = 'Angriff';
            $image = 'img/offtopic/ANG.png?1';
            $berry = number_format($clan->GetLevelUpCostBerry($clan->GetAttack() + 1), 0, '', '.');
            $gold = number_format($clan->GetLevelUpCostGold($clan->GetAttack() + 1), 0, '', '.');
            break;
        case 'def':
            $stat = 'Abwehr';
            $image = 'img/offtopic/DEF.png?1';
            $berry = number_format($clan->GetLevelUpCostBerry($clan->GetDefense() + 1), 0, '', '.');
            $gold = number_format($clan->GetLevelUpCostGold($clan->GetDefense() + 1), 0, '', '.');
            break;
        case 'level':
            $image = 'img/offtopic/lvlup.png?1';
            $berry = number_format($clan->GetLevelUpCostBerry($clan->GetLevel() + 1, false), 0, '', '.');
            $gold = number_format($clan->GetLevelUpCostGold($clan->GetLevel() + 1, false), 0, '', '.');
            break;
    }
    if($_GET['a'] != 'level')
    {
        ?>
            Bist du sicher, dass du <?php echo $stat; ?>
            <div class="spacer"></div>
            <img src="<?php echo $image; ?>" alt="<?php echo $stat; ?>" title="<?php echo $stat; ?>" style="width: 100px; height: 100px">
            <div class="spacer"></div>
            aufwerten möchtest für:
            <div class="spacer"></div>
            <?php echo $berry; ?> <img src="img/offtopic/BerrySymbol.png" alt="Berry" title="Berry" style="position: relative; top: 2px; height: 20px; width: 13px;"/><br/>
            <?php echo $gold; ?> <img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 2px; height: 20px; width: 20px;"/>
            <div class="spacer"></div>
            <form method="post" action="?p=clanmanage&a=levelstat">
                <input type="hidden" name="stat" value="<?php echo $_GET['a']; ?>">
                <input type="submit" class="ja" value="Aufwerten">
            </form>
        <?php
    }
    else
    {
        if($clan->GetExp() < $clan->GetRequiredExp())
            exit();
        ?>
            Bist du sicher, <br/>
            dass du die Bande von Level <?php echo number_format($clan->GetLevel(), 0, '', '.'); ?> auf Level <?php echo number_format($clan->GetLevel() + 1, 0, '', '.'); ?>
            <div class="spacer"></div>
            <img src="<?php echo $image; ?>" alt="Aufwertung" title="Aufwertung" style="width: 100px; height: 100px">
            <div class="spacer"></div>
            aufwerten möchtest für:
            <div class="spacer"></div>
            <?php echo $gold; ?> <img src="img/offtopic/GoldSymbol.png" alt="Gold" title="Gold" style="position: relative; top: 2px; height: 20px; width: 20px;"/>
            <div class="spacer"></div>
            <form method="post" action="?p=clanmanage&a=level">
                <input type="submit" class="ja" value="Aufwerten">
            </form>
        <?php
    }
?>
<div class="spacer"></div>

