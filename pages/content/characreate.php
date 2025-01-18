<div class="spacer"></div>
<?php
//$regi wird in der head/register.php gesetzt

$maxChars = 1;

if ($account->IsLogged())
{
    $charas = new Generallist($database, 'accounts', 'userid, id', 'userid="' . $account->Get('id') . '"', 'id', 999);
    $id = 0;
    $arank = 0;
    $userid = 0;
    $isDonor = false;
    $entry = $charas->GetEntry($id);
    while ($entry != null)
    {
        $displayedPlayer = new Player($database, $entry['id']);
        $arank = $displayedPlayer->GetARank();
        $userid = $displayedPlayer->GetUserID();
        if(!$isDonor && $isDonor != $displayedPlayer->IsDonator())
            $isDonor = $displayedPlayer->IsDonator();
        ++$id;
        $entry = $charas->GetEntry($id);
    }
    $multis = new Generallist($database, 'multichars', 'userid, charnum', 'userid="' . $account->Get('id') . '"', 'id');
    $mid = 0;
    $multi = $multis->GetEntry($mid);
    if ($multi != null)
        $maxChars += $multi['charnum'];
    if($isDonor)
        $maxChars += 1;
    if ($id < $maxChars || $arank >= 2)
    {
        if (!$charaCreationActive && $arank < 2)
        {
            ?>
            <span style="text-align: center;">
				<h2>
					<span style="color: red">Die Charaktererstellung ist derzeit deaktiviert!</span><br/>
				</h2>
			</span>
            <?php
        }
        else if($account->IsBannedInGame('OPWX'))
        {
            ?>
            <span style="text-align: center;"
                  <h1>
                      <span style="color: red;">
                          Du kannst dir keinen Charakter erstellen da du gesperrt wurdest.
                      </span>
                  </h1>
            </span>
            <?php
        }
        else
        {
            ?>
            <div class="regimain borderT borderR borderL borderB">
                <div class="regiheader catGradient borderB">Registrierung</div>
                <div class="regchar borderT borderL borderR borderB">
                    <img id="image" src="img/rasse.png?001" width="100%" height="100%" />
                    <img id="head" src="" style="position: relative; top: -362px;" width="100%" height="100%" hidden/>
                </div>
                <div class="regimput">
                    <form name="form1" action="?p=characreate&a=register" method="post">
                        <input type="text" name="chara" placeholder="Charakter Name">
                        <div class="spacer"></div>
                        <script type="text/javascript">
                            var imgHead = document.getElementById("head");

                            function onRaceSelected(selectObject) {
                                var imageList = document.getElementById('raceimage');
                                while (imageList.firstChild) {
                                    imageList.removeChild(imageList.firstChild);
                                }
                                if (selectObject.value === 'Rasse') {
                                    imageList.options[imageList.options.length] = new Option('Bild', 'Rasse');

                                } else {
                                    var length = 4;
                                    if(selectObject.value === 'Pirat')
                                        length = 13;
                                    else if(selectObject.value === 'Marine')
                                        length = 8;
                                    for (var i = 1; i <= length; ++i) {
                                        var bildName = 'Bild ' + i;
                                        var bildRace = selectObject.value + i;
                                        imageList.options[imageList.options.length] = new Option(bildName, bildRace);
                                    }
                                }
                                setRaceImage(imageList.options[0].value);
                            }

                            function onImageSelected(imageOption) {
                                setRaceImage(imageOption.value);
                            }

                            function setRaceImage(imageName) {
                                var img = document.getElementById("image");
                                img.src = 'img/races/' + imageName + '.png?003';
                                if(imageName !== 'Rasse')
                                {
                                    imgHead.src = 'img/races/' + imageName + 'Head.png?003';
                                    imgHead.hidden = false;
                                }
                                else
                                    imgHead.hidden = true;
                            }
                        </script>
                        <select class="select" name="rasse" id="rasse" onchange="onRaceSelected(this)">
                            <option value="Rasse">Fraktion</option>
                            <option value="Pirat">Pirat</option>
                            <option value="Marine">Marine</option>
                        </select><br>
                        <div class="spacer"></div>
                        <select class="select" name="raceimage" id="raceimage" onchange="onImageSelected(this)">
                            <option value="Rasse">Bild</option>
                        </select><br>
                        <div class="spacer"></div>
                        <select class="select" name="pfad">
                            <option value="Pfad">Pfad</option>
                            <option value="Schwertkaempfer">Schwertkämpfer</option>
                            <option value="Schwarzfuss">Schwarzfuß</option>
                            <option value="Karatekämpfer">Karatekämpfer</option>
                        </select><br>
                        <div class="spacer"></div>
                        <input type="submit" value="Registrieren">
                    </form><br />

                    <div id="preloader">
                        <img src="img/races/Rasse.png" width="1" height="1" />
                        <img src="img/races/zorro.png?001" width="1" height="1" />
                    </div>
                </div>
            </div>

            <div class="spacer"></div>

            <span style="text-align: center;"><b>Eingabehilfe</b><br/>
                <b>
                    <span style="color: #FF0000;">Anzahl:</span>
                </b> Jeder Spieler kann sich nur einen Charakter erstellen, solltest du einen weiteren Charakter benötigen, wende dich bitte an Sload oder Shirobaka.<br/>
                <b>
                    <span style="color: #0066FF;">Charakter Name:</span>
                </b> Der Name deines Charakters<br/>
                <b>
                    <span style="color: #0066FF;">Fraktion:</span>
                </b> Entspricht deinen Lebensweg im Spiel und dessen Besonderheiten.<br/>
            </span>
            <?php
        }
        if ($arank > 1 && !$charaCreationActive)
        { ?>
            <span style="text-align: center;">
                <b>
                    <span style="color: #FF0000">Admin-Info:</span>
                </b>
                Die Registrierung ist aktuell für normale Benutzer gesperrt.
            </span>
            <?php
        }
        ?>
        <div class="spacer"></div>
        <?php
    }
    else
    {
        ?>
        <div class="spacer"></div>
        <img src="img/info.png" alt="regi" height="300">
        <hr>
        Es können nicht mehr als <?php echo $maxChars; ?> Charaktere erstellt werden.<br />
        <div class="spacer"></div>
        <?php
    }
}
else
{
    ?>
    <div class="spacer"></div>
    <img src="img/info.png" alt="regi" height="300">
    <hr>
    Du musst dich zuerst einloggen, damit du einen Charakter erstellen kannst.<br />
    <div class="spacer"></div>
    <?php
}
?>