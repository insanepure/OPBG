<div class="logincontent">

    <img width="300px" src="img/info.png">
    <div class="spacer"></div>
    <div class="logincontentlogin">
        <?php
            if ($userRegisterActive)
            {
                ?>
                <form name="form1" action="?p=register&a=registrieren" method="post">
                    <fieldset style="width:60%">
                        <legend><b>Registrierung</b></legend>
                        <table width="100%" cellspacing="8">
                            <tr>
                                <td width="30%" align="center"><b>Login</b></td>
                                <td width="70%" align="center"><input type="text" placeholder="Accountname" name="acc" required></td>
                            </tr>
                            <tr>
                                <td width="30%" align="center"><b>Passwort</b></td>
                                <td width="70%" align="center"><input pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Das Passwort sollte mindestens 8 Zeichen lang sein und einen Groß- und Kleinbuchstaben, eine Zahl und ein Sonderzeichen enthalten." type="password" placeholder="Passwort" name="pw" required></td>
                            </tr>
                            <tr>
                                <td width="30%" align="center"><b>Passwort wiederholen</b></td>
                                <td width="70%" align="center"><input pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Das Passwort sollte mindestens 8 Zeichen lang sein und einen Groß- und Kleinbuchstaben, eine Zahl und ein Sonderzeichen enthalten." type="password" placeholder="Passwort wiederholen" name="pw2" required></td>
                            </tr>
                            <tr>
                                <td width="30%" align="center"><b>E-mail</b></td>
                                <td width="70%" align="center"><input pattern="[a-zA-Z0-9]+([-_\.]?[a-zA-Z0-9])+@[a-zA-Z0-9]+([-_\.]?[a-zA-Z0-9])+\.[a-zA-Z]{2,4}" type="text" placeholder="E-Mail" name="email" required></td>
                            </tr>
                            <tr>
                                <td width="30%" align="center"><b>E-mail wiederholen</b></td>
                                <td width="70%" align="center"><input  pattern="[a-zA-Z0-9]+([-_\.]?[a-zA-Z0-9])+@[a-zA-Z0-9]+([-_\.]?[a-zA-Z0-9])+\.[a-zA-Z]{2,4}"  type="text" placeholder="E-Mail wiederholen" name="email2" required></td>
                            </tr>
                            <tr>
                                <td width="100%" align="center" colspan="2"><input type="checkbox" id="c1" name="regeln" required/><label for="c1">
                                        <span style="color: #eee;" class="schatten"> Ich habe die <a href="?p=regeln" target="_blank">Regeln</a> und die <a href="?p=info&info=dsgvo" target="_blank">Datenschutzerklärung</a> gelesen und akzeptiere sie.</span>
                                    </label></td>
                            </tr>
                            <tr>
                                <td width="100%" align="center" colspan="2"><input type="submit" value="Registrieren"></td>
                            </tr>
                        </table>
                    </fieldset>
                </form>
                <div class="spacer"></div>
                <fieldset style="width:80%">
                    <legend><b>Eingabehilfe:</b></legend>
                    <table>
                        <tr>
                            <td>
                                <span style="color: #0066FF;">Loginname:</span></b> Dein Loginnamen, mit dem du dich einloggen kannst.
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="color: #0066FF;">Passwort:</span></b> Ein sicheres und gut ausgewähltes Passwort.
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="color: #0066FF;">Passwort wiederholen:</span></b> Das obige Passwort nochmal wiederholen.
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="color: #0066FF;">E-Mail:</span></b> Deine E-Mail, damit wir dir einen Aktivierungscode zusenden können.
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="color: #0066FF;">E-Mail wiederholen:</span></b> Die obige E-Mail nochmal wiederholen.
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="color: #FF0000;">Info:</span></b> Bitte verwende eine richtige E-Mail Adresse, Charaktere mit einer Wegwerf-E-Mail Adresse werden nicht verifiziert.
                            </td>
                        </tr>
                    </table>
                </fieldset>
                <?php
            }
            else
            {
                ?>
                <center>
                    <h1>
                        <span style='color: red'>Die Anmeldung ist offline!</span>
                    </h1><br/>
                    Infos zur Anmeldung findet ihr in den News.<br/>
                    Mit Freundlichen Grüßen<br/>
                    Das One Piece Browsergame Team
                </center>
                <?php
            }
        ?>
        <div class="spacer"></div>
    </div>
    <div class="spacer"></div>
</div>
<div class="spacer2"></div>