<script>
  grecaptcha.ready(function() {
    grecaptcha.execute('6Lc5RnwbAAAAAF1iOYAEgWqmIesmvK7S-nES_7Pb', {
      action: 'Map'
    });
  });
</script>
<div class="spacer"></div>
<?php
if ($player->GetPlanet() == 2)
{
?>
  <div id="map" class="map boxSchatten borderL borderR borderT borderB" style="border:1px solid black; background-image: url('/img/planets/travelshipimpeldown.png?004')">
  <?php
}
else
{
  ?>
    <div id="map" class="map boxSchatten borderL borderR borderT borderB" style="border:1px solid black; background-image: url('img/planets/Space.png?004')">
    <?php
  }
    ?>

    <?php
    $planets = new Generallist($database, 'planet', '*', '', '', 99999, 'ASC');
    $id = 0;
    $entry = $planets->GetEntry($id);
    $x = 0;
    $y = 0;
    while ($entry != null)
    {
      $placeCSS = 'mapplace';
      if ($player->GetPlanet() == $entry['id'])
      {
        $x = $entry['x'];
        $y = $entry['y'];
      }

      $canSee = ($entry['minstory'] == 0 || $player->GetStory() >= $entry['minstory']) && ($entry['maxstory'] == 0 || $player->GetStory() <= $entry['maxstory']) && ($player->GetArank() > 2 || $entry['visible'] == 1);
      $canSeeSide = ($entry['minsidestory'] == 0 || $player->GetSideStory() >= $entry['minsidestory']) && ($entry['maxsidestory'] == 0 || $player->GetSideStory() <= $entry['maxsidestory']);

      if ($entry['display'] == 1 && ($canSee && $canSeeSide))
      {
    ?>
        <div class="tooltip" style="position:absolute; left:<?php echo $entry['x']; ?>px; top:<?php echo $entry['y']; ?>px;">
          <a style="cursor:pointer;" onclick="OpenPopupPage('<?php echo $entry['name']; ?>','shiptravel/ocean.php?id=<?php echo $entry['id']; ?>')">
            <div class="mapplace" style="left:60px; top:35px; width:12px; height:14px; background-image: url('img/space/<?php echo $entry['mapimage']; ?>.png?005')"></div>
          </a>
          <span class="tooltiptext" style="width:180px; left:-23px;"><?php echo $entry['name']; ?></span>
        </div>
    <?php
      }
      ++$id;
      $entry = $planets->GetEntry($id);
    }
    if ($player->GetRace() == "Pirat")
    {
      $race = 'Pirat';
    }
    else if ($player->GetRace() == "Marine")
    {
      $race = 'Marine';
    }
    ?>
    <img class="mapcharacter" style="left:<?php echo $x + 55; ?>px; top:<?php echo $y + 25; ?>px; height: 30px; width: 30px;" src="img/planets/schiff.png" />
    </div>
    <?php
    if($player->GetArank() >= 2)
    {
        ?>
        x: <span id="x">0</span> y: <span id="y">0</span>
        <script>
            var map = document.getElementById('map');
            map.onmousemove = function(e) {
                // e = Mouse click event.
                var rect = map.getBoundingClientRect();
                var x = e.clientX - rect.left - 67; //x position within the element.
                var y = e.clientY - rect.top - 36;  //y position within the element.
                document.getElementById('x').innerText = Number(x).toFixed(0);
                document.getElementById('y').innerText = Number(y).toFixed(0);
            }
        </script>
        <?php
    }
    ?>

    <?php
    if ($player->GetARank() >= 2)
    {
      $select = "id, name";
      $where = '';
      $order = 'id';
      $from = 'planet';
      $list = new Generallist($database, $from, $select, $where, $order, 1000, 'DESC');
    ?>
      <form method="POST" action="?p=shiptravel&a=jump">
        <select class="select" name="oceanid">
          <?php
          $id = 0;
          $entry = $list->GetEntry($id);
          while ($entry != null)
          {
          ?>
            <option value="<?php echo $entry['id']; ?>"><?php echo '(' . $entry['id'] . ') ' . $entry['name']; ?></option>
          <?php
            ++$id;
            $entry = $list->GetEntry($id);
          }
          ?>
        </select>
        <input type="submit" value="Springen">
      </form>
      <br />
    <?php
    }
    ?>