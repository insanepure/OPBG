<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/blackjack/blackjack.php';
$BlackJack = new BlackJack($database, $player->GetID());

?>
<h2>Meine Hand</h2>
<?php
$myHand = explode(";", $BlackJack->GetHand());
$i = 0;
while($myHand[$i])
{
    echo $myHand[$i]." ";

    ++$i;
}
echo "<br/><br/>Meine Punkte: ".$BlackJack->MyPoints();
?>
<form method="post" action="?p=blackjack&a=openthegame">
    <button class="buttons">Spiel öffnen</button>
</form>
<br /><br />
<form method="post" action="?p=blackjack&a=draw">
    <button class="buttons">Ziehen</button>
</form>
<br /><br />
<form method="post" action="?p=blackjack&a=endthegame">
    <button class="buttons">Spiel beenden</button>
</form>
