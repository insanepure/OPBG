<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '../../main/www/classes/session.php';
    include_once '../../../classes/header.php';
    include_once '../../../classes/treasurehunt/treasurehunt.php';
    include_once '../../../classes/treasurehunt/treasurehuntisland.php';
    include_once '../../../classes/treasurehunt/treasurehuntprogress.php';
    include_once '../../../classes/treasurehunt/treasurehuntmanager.php';
    if(!$player->IsLogged())
    {
        echo 'Du bist nicht eingeloggt.';
        exit();
    }
    $treasurehuntmanager = new treasurehuntmanager($database);
    $treasurehuntprogress = $treasurehuntmanager->LoadPlayerData($player->GetID());
    if($treasurehuntprogress->GetTreasurehuntid() % 5 != 0)
    {
        echo 'Diese Aktion ist ungültig.';
        exit();
    }
?>
<style>
    .itemButton:hover {
        background-image:linear-gradient(#4a4d56,#333438);
    }
</style>
<form method="post" action="?p=treasurehunt&a=treasure">
    <input type="radio" name="item" id="52" value="52" style="visibility: hidden;">
    <input type="radio" name="item" id="53" value="53" style="visibility: hidden;">
    <input type="radio" name="item" id="54" value="54" style="visibility: hidden;">
    <input type="radio" name="item" id="55" value="55" style="visibility: hidden;">
    <input type="radio" name="item" id="56" value="56" style="visibility: hidden;">
    <input type="radio" name="item" id="57" value="57" style="visibility: hidden;">

    <center>
        Nach welchem Item möchtest du suchen?
    </center>
    <table>
        <tr>
            <td class="itemButton" style="padding: 10px; cursor: pointer;">
                <label for="52">
                    <img src="img/items/fruchtzoan.png" class="item" width="75px" height="75px" style="cursor: pointer;" onclick="ChangeItem(this);">
                </label>
            </td>
            <td class="itemButton" style="padding: 10px; cursor: pointer;">
                <label for="53">
                    <img src="img/items/parafrucht.png" class="item" width="75px" height="75px" style="cursor: pointer;"  onclick="ChangeItem(this);">
                </label>
            </td>
            <td class="itemButton" style="padding: 10px; cursor: pointer;">
                <label for="54">
                    <img src="img/items/logiafrucht.png" class="item" width="75px" height="75px" style="cursor: pointer;"  onclick="ChangeItem(this);">
                </label>
            </td>
        </tr>
        <tr>
            <td class="itemButton" style="padding: 10px; cursor: pointer;">
                <label for="55">
                    <img src="img/items/Fischitem1.png" class="item" width="75px" style="cursor: pointer;"  height="75px" onclick="ChangeItem(this);">
                </label>
            </td>
            <td class="itemButton" style="padding: 10px; cursor: pointer;">
                <label for="56">
                    <img src="img/items/needitemschert.png" class="item" width="75px" style="cursor: pointer;"  height="75px" onclick="ChangeItem(this);">
                </label>
            </td>
            <td class="itemButton" style="padding: 10px; cursor: pointer;">
                <label for="57">
                    <img src="img/items/needitemschwarzfu.png" class="item" width="75px" style="cursor: pointer;"  height="75px" onclick="ChangeItem(this);">
                </label>
            </td>
        </tr>
        <tr>
           <td style="height: 20px;"></td>
        </tr>
        <tr>
            <td colspan="3">
                <center>
                    <input type="submit" class="ja" value="Suchen">
                </center>
            </td>
        </tr>
        <tr>
            <td style="height: 20px;"></td>
        </tr>
    </table>
</form>