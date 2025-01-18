<form method="post" action="?p=taylor&formular=taylor">
<input type="text" name="tj"><br />
    <button>Absenden</button>
</form>
<?php
if(isset($_GET['p']) && $_GET['p'] == 'taylor' && isset($_GET['formular']) && $_GET['formular'] == 'taylor')
{
$tj = $_POST['tj'];
if($tj == 'Liebe')
{
    $message = "Taylor liebt Papa";
}
else
{
    $message = "Der Wert stimmt nicht";
}
}
