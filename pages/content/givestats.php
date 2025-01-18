<?php
include_once 'classes/header.php';
if($player->GetArank() >= 2)
{

       ?>
    <script language="JavaScript">
        function add()
        {
            let lebenspunkte = document.getElementById('lp').value;
            let realylifepoints = lebenspunkte * 10;
                document.getElementById('outputlp').value = realylifepoints;
            let dstatspunkte = document.getElementsById('dstats').value;
            let dstatrechnung = dstatspunkte * 4;
            document.getElementById('realydstats').value = dstatrechnung;
            let ausdauerpunkte = document.getElementById('ad').value;
            let realadpoints = ausdauerpunkte * 10;
            document.getElementById('realad').value = realadpoints;
            let atkpunkte = document.getElementById('atk').value;
            let realatk = atkpunkte * 2;
            document.getElementById('realatk').value = realatk;
            let defpunkte = document.getElementById('def').value;
            let realdef = defpunkte * 1;
            document.getElementById('realdef').value = realdef;


        }
    </script>
        <form name="givestats" method="post" onkeyup="add()" action="?p=givestats&a=give">
            <table style="align-content: center">
                <tr>
                    <td>Trage eine Anzahl an Stats ein die du haben möchtest</td>
                </tr>
                <tr>
                <td><input type="number" name="stats" value="0"/></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Trage eine Anzahl An Douriki ein die du haben möchtest, dementprechend Stats erhältst du</td>
                </tr>
                <tr>
                    <td><input type="number" name="dstats" id="dstats" value="<?= $player->GetStats(); ?>"/></td>
                    <td><input type="text" id="realydstats" name="realydstats" placeholder="Douriki" readonly/></td>
                </tr>
                <tr>
                    <td>Trag einen LP Wert ein den du haben möchtest</td>
                </tr>
                <tr>
                    <td><input type="number" name="lp" id="lp" value="<?= $player->GetMaxLP() / 10; ?>"/></td>
                    <td><input type="text" id="outputlp" placeholder="LP" readonly/></td>
                </tr>
                <tr>
                    <td>Trag einen AD Wert ein den du haben möchtest</td>
                </tr>
                <tr>
                    <td><input type="number" name="ad" id="ad" value="<?= $player->GetMaxKP() / 10; ?>"/></td>
                    <td><input type="text" id="realad" placeholder="AD" readonly /></td>
                </tr>
                <tr>
                    <td>Trag einen ATK Wert ein den du haben möchtest</td>
                </tr>
                <tr>
                    <td><input type="number" name="atk" id="atk" value="<?= $player->GetAttack() / 2; ?>"/></td>
                    <td><input type="text" id="realatk" placeholder="ATK" readonly/></td>
                </tr>
                <tr>
                    <td>Trag einen Def Wert ein den du haben möchtest</td>
                </tr>
                <tr>
                    <td><input type="number" name="def" id="def" value="<?= $player->GetDefense(); ?>"/></td>
                    <td><input type="text" id="realdef" placeholder="Defense" readonly /></td>
                </tr>
                <tr>
                    <td>Trag ein wie viel Berry du möchtest</td>
                </tr>
                <tr>
                    <td><input type="number" name="berry" value="<?= $player->GetBerry(); ?>"/></td>
                </tr>
                <tr>
                    <td>Trag eine wie viel Gold du möchtest</td>
                </tr>
                <tr>
                    <td><input type="number" name="gold" value="<?= $player->GetGold(); ?>"/></td>
                </tr>
                <tr>
                    <td>Trag eine wie viel Elopunkte du möchtest</td>
                </tr>
                <tr>
                    <td><input type="number" name="elo" value="<?= $player->GetEloPoints(); ?>"/></td>
                </tr>
            </table>
            <br />
            <button value="Absenden">Absenden</button>
        </form>
    <br />
    <fieldset>
        <legend>Information zum Script</legend>
        1. Alle eingegebenen Werte werden mit dem welcher in der Spalte steht ersetzt!<br />
        2. Feld 1 gibt euch den eingetragenen Wert an Stats<br />
        3. Feld 2 rechnet den eingetragenen Wert * 4 da es sich hier nach Douriki richtet.<br />
        4. Ersetzt den eingetragenen Wert mit eurer LP/Max LP<br />
        5. Ersetzt den eingetragenen Wert mit eurer AD/Max AD<br />
        6. Ersetzt den eingetragenen Wert mit eurer ATK<br />
        7. Ersetzt den eingetragenen Wert mit eurer Defense<br />
        8. Ersetzt den eingetragenen Wert mit eurer Berry<br />
        9. Ersetzt den eingetragenen Wert mit eurem Gold<br />
    </fieldset>
       <?php
        if(isset($_GET['p']) == 'givestats' && isset($_GET['a']) == 'give')
        {
            $stats = $_POST['stats'];
            $dstats = floor($_POST['dstats'] * 4);
            $lp = floor($_POST['lp'] * 10);
            $ad = floor($_POST['ad'] * 10);
            $atk = floor($_POST['atk'] * 2);
            $def = $_POST['def'];
            $berry = $_POST['berry'];
            $gold = $_POST['gold'];
            $elo = $_POST['elo'];

            if($stats == 0 && $dstats == 0 && $lp == 0 && $ad == 0 && $atk == 0 && $def == 0 && $berry == 0 && $gold == 0 && $elo == 0)
            {
                $message = "Eines der Werte muss über 0 liegen";
            }
            else if(!is_numeric($stats) || !is_numeric($elo) || !is_numeric($dstats) || !is_numeric($lp) || !is_numeric($ad) || !is_numeric($atk) || !is_numeric($def)|| !is_numeric($berry) || !is_numeric($gold))
            {
                $message = "Es gibt einen Fehler bei den Einträgen";
            }
            else if($stats < 0 || $dstats < 0 || $elo < 0)
            {
                $message = "Der Wert muss bei Stats entweder gleich 0 oder höher sein";
            }
            else if($player->GetArank() < 2)
            {
                $message = "Du hast dazu keine befugnis";
            }
            else
            {
                $database->Debug();
                $result = $database->Update('lp="'.$lp.'", elopoints="'.$elo.'", mlp="'.$lp.'", kp="'.$ad.'", mkp="'.$ad.'", attack="'.$atk.'", defense="'.$def.'", zeni="'.$berry.'", gold="'.$gold.'"', 'accounts', 'id="'.$player->GetID().'"');
                if($stats > 0 && $dstats > 0)
                {
                    $statss = $stats + $dstats;
                    $resultz = $database->Update('stats="'.$statss.'"', 'accounts', 'id="'.$player->GetID().'"');
                }
                else if($stats <= 0 && $dstats > 0)
                {
                    $resultz = $database->Update('stats="'.$dstats.'"', 'accounts', 'id="'.$player->GetID().'"');
                }
                else
                {
                    $resultz = $database->Update('stats="'.$stats.'"', 'accounts', 'id="'.$player->GetID().'"');
                }
                $message = "test";
            }

        }
    }
?>