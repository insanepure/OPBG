<div class="spacer"></div>
<h2>News</h2><br/>
<button onclick="OpenPopupPage('Neuen Eintrag erstellen','writer/newpost.php')">Neuen Eintrag erstellen</button>
<div class="spacer"></div>
<hr>
<?php
    $loadNews = $database->Select('*', 'writer', '');
    if($loadNews)
    {
        $id = 0;
        while($news = $loadNews->fetch_assoc())
        {
            ?>
            <b>Titel:</b> <?= $news["titel"] ?>
            <a href='?p=writer&a=delete&id=<?= $news["id"] ?>' style="color:red" onclick="return confirm('Möchtest du den Eintrag wirklich löschen?');">X</a> 
            <button onclick="OpenPopupPage('Neuen Eintrag erstellen','writer/postedit.php?id=<?= $news['id'] ?>')">✏️</button><br /><br />
            <b>Mode:</b> <?= $news["mode"] ?><br />
            <b>Bild (URL):</b> <?= $news["bild"] ?><br />
            <b>Datum:</b> <?= $news['datum'] ?><br />
            <b>Text:</b> <br /><?= $news["text"] ?><br />
            <hr>
            <br />
            <?php
            ++$id;
        }
    }
?>