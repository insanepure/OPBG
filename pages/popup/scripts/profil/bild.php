<?php
include_once $_SERVER['DOCUMENT_ROOT']."classes/header.php";
?>
<script>
    var imageList = document.getElementById('raceimage');
    while (imageList.firstChild) {
        imageList.removeChild(imageList.firstChild);
    }
    for (var i = 1; i <= 4; ++i) {
        var bildName = 'Bild ' + i;
        var bildRace = "<?php echo $player->GetRace(); ?>" + i;
        console.log(bildRace);
        imageList.options[imageList.options.length] = new Option(bildName, bildRace);
    }
    setRaceImage(imageList.options[0].value);

    function onImageSelected(imageOption) {
        setRaceImage(imageOption.value);
    }

    function setRaceImage(imageName) {
        var img = document.getElementById("image");
        img.src = 'img/races/' + imageName + '.png?003';
    }
</script>