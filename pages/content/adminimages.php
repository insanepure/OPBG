<?php
if ($player->GetArank() >= 2)
{
    $validDirectories = array('actions', 'attacks', 'ausruestung', 'events', 'items', 'npc', 'places', 'storyimages', 'planets', 'space', 'marketing', 'verzeichnis', 'gameevents', 'schiffe', 'titel', 'offtopic', 'races', 'schatzsuche', 'backgrounds', 'profilebackgrounds', 'header');
    if($player->GetArank() >= 3)
    {
        $files = scandir('img/');
        $files = array_diff(scandir('img'), array('.', '..'));
        $validDirectories = array();
        foreach ($files as &$file)
        {
            if(is_dir('img/'.$file))
            {
                $validDirectories[] = $file;
            }
        }
    }
?>
  <div class="spacer"></div>
  <div class="catGradient borderT borderB" style="width:90%;">
    <h2>Bilder Verwaltung</h2>
  </div>
  <div class="spacer"></div>

  <?php
  foreach ($validDirectories as &$directory)
  {
    if (isset($_GET['directory']) && $_GET['directory'] == $directory)
    {
  ?>- <b><a href="?p=adminimages&directory=<?php echo $directory; ?>"><?php echo ucwords($directory); ?></a></b> -<?php
                                                                                                                }
                                                                                                                else
                                                                                                                {
                                                                                                                  ?>- <a href="?p=adminimages&directory=<?php echo $directory; ?>"><?php echo ucwords($directory); ?></a> -<?php
                                                                                                                }
                                                                                                              }
                                                                                                            ?>
  <div class="spacer"></div>
  <hr>
  <div class="spacer"></div>
  <?php
  if (isset($_GET['directory']) && in_array($_GET['directory'], $validDirectories))
  {
  ?>
      <?php
      if($_GET['directory'] == 'backgrounds')
      {
          $validTypes = 'image/jpeg';
      }
      else
      {
          $validTypes = 'image/png';
      }
      ?>
      <table style="text-align: center;">
          <tr>
              <td style="width: 40%;">
                  <form name="form1" action="?p=adminimages&directory=<?php echo $_GET['directory']; ?>&a=upload" method="post" enctype="multipart/form-data">
                      <input type="file" name="file_upload[]" accept="<?php echo $validTypes; ?>" multiple/><input type="hidden" name="image"/>
                      <div class="spacer"></div>
                      <input type="submit" value="Hochladen">
                  </form>
              </td>
              <td style="width: 20%;"></td>
              <td style="width: 40%;">
                  <input type="text" id="search" placeholder="Suche" oninput="Search(this.value);">
              </td>
          </tr>
      </table>
      <?php
      if(isset($uploadedimages))
      {
          echo '<div class="spacer"></div>';
          foreach ($uploadedimages as $image)
          {
              echo $image . '<br/>';
          }
      }
      ?>
    <div class="spacer"></div>
    <hr>
    <div class="spacer"></div>
    <?php
    $path    = 'img/' . $_GET['directory'];
    $files = scandir($path);
    $files = array_diff(scandir($path), array('.', '..'));
      foreach ($files as &$file)
      {
          $info = pathinfo($file);
          $filename =  basename($file,'.'.$info['extension']);
          ?>
          <div style="height:200px; width:200px; float:left;" class="images" id="<?php echo $filename; ?>">
              <img src="<?php echo $path.'/'.$file; ?>" title="<?php echo $filename; ?>" width="100px" height="100px"><br/>
              <?php echo $file; ?> <br/>
              <form method="POST" action="?p=adminimages&directory=<?php echo $_GET['directory']; ?>&a=delete">
                  <input type="hidden" value="<?php echo $file; ?>" name="file">
                  <input type="submit" value="Löschen">
              </form>
          </div>
          <?php
      }
  }
}
?>
<script>
    if(sessionStorage.getItem('search') !== 'null')
    {
        document.getElementById('search').value = sessionStorage.getItem('search');
        Search(sessionStorage.getItem('search'));
    }

    function Search(input)
    {
        var images = document.getElementsByClassName('images');
        for (let i = 0; i < images.length; i++) {
            images[i].style.display = 'block';
            if(!images[i].id.toLowerCase().includes(input.toLowerCase()))
                images[i].style.display = 'none';
        }
        sessionStorage.setItem('search', input);
    }
</script>