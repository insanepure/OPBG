<h2>
    MultiCheck
</h2>
<form method="GET" action="?p=multicheck">
    <input type="hidden" name="p" value="multicheck">
    <input type="text" name="chara" placeholder="Charaktername">
    <div class="spacer"></div>
    <input type="score" name="score" placeholder="Mindestscore" value=50>
    <div class="spacer"></div>
    <input type="submit" style="width:50%" value="Überprüfen">
</form>

<br />
<hr>
<br />

<form method="GET" action="?p=multicheck">
    <input type="hidden" name="p" value="multicheck">
    <input type="hidden" name="a" value="cookiecheck">
    <input type="submit" style="width:50%" value="Cookies Prüfen">
</form>
<br />
<hr>
<br />
<hr>
<br />

<form method="GET" action="?p=javacheck">
    <input type="hidden" name="p" value="multicheck">
    <input type="hidden" name="a" value="javacheck">
    <input type="submit" style="width:50%" value="Java Accounts Checken">
</form>
<br />
<hr>
<?php
if (isset($_GET['a']) && $_GET['a'] == 'cookiecheck')
{
    set_time_limit(600);
    $score = $_GET['score'];

    $log = $log . 'Multiüberprüfung durch Cookies.<br/>';
    AddToLog($database, $ip, $accs, $log);

    $result = LoginTracker::CheckCookies($accountDB, 'opwx');
    ?>
    Users deren UserID nicht den selben Cookie hat:<br /><br />
    <?php
    echo $result;
}
else if($_GET['a'] && $_GET['a'] == 'javacheck')
{
    $javacheck = new Generallist($database, 'accounts', '*', '1');
    $id = 0;
    $java = $javacheck->GetEntry($id);
    while($java != null)
    {
        if($java['multitext'] == '[""]' || $java['multitext'] == '' || $java['multitext'] == '["'.$java['name'].'"]' || $java['multitext'] == '["'.$java['name'].'",""]' || $java['multitext'] == '["","'.$java['name'].'"]')
        {
            $id++;
            $java = $javacheck->GetEntry($id);
        }
        else
        {
            $multitext = $java['multitext'];
            $multitext = str_replace('[', '', $multitext);
            $multitext = str_replace('"', '', $multitext);
            $multitext = str_replace(',,', ',', $multitext);
            $multitext = str_replace(']', '', $multitext);
            $multitext = str_replace(',', ', ', $multitext);
            echo $java['name'].":<div class='spacer'></div>".$multitext."<div class='spacer'></div>";
            $id++;
            $java = $javacheck->GetEntry($id);
        }
    }
}
else if (isset($_GET['chara']))
{
    set_time_limit(600);
    $score = $_GET['score'];
    if (!is_numeric($score))
        $score = 50;

    $log = $log . 'Multiüberprüfung von Character <b>' . $_GET['chara'] . '</b>.<br/>';
    //AddToLog($database, $ip, $accs, $log);

    $result = LoginTracker::CheckCharacter($accountDB, $_GET['chara'], 'opwx', $score);
    $javaabfrage = $database->Select('*', 'accounts', 'name="'.$_GET['chara'].'"');
    $java = $javaabfrage->fetch_assoc();


    ?>
    Alle Multis von <?php echo $_GET['chara']; ?><br /><br />
    <?php
    echo "<b>Java Logins</b><br><br>".$java['multitext']."<br><br>";
    echo $result;
}
?>