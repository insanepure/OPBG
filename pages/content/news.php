<?php
    $timestamp = mktime(18, 0, 0, 4, 15, 2022);
    $currentTime = strtotime("now");
    $difference = $timestamp - $currentTime;
    if ($difference < 0)
        $difference = 0;

    if($difference > 0)
    {
        ?>
            <div class="newscontainer smallBG borderR borderL borderT borderB">
                <div class="newshead catGradient borderB">
                    <div class="newstitle">
                        <h1 style="font-size: 94%; margin-top:3px; margin-right:150px; width: 350px; text-align: left;">Wiedereröffnung</h1>
                    </div>
                </div>
                <article>
                    <div class="newscontent smallBG">
                        <div id="timer" style="font-size: 60px">Init
                            <script>
                                countdown(<?php echo $difference; ?>, 'timer');
                            </script>
                        </div>
                    </div>
                </article>
            </div>
        <?php
    }
?>
<!-- -->
<div class="newsspan"></div>
<?php
$newsCount = $newsManager->GetNewsCount();
for ($i = 0; $i < $newsCount; $i++)
{
  $news = $newsManager->GetNews($i);
?>
  <div class="newscontainer smallBG borderR borderL borderT borderB">
    <div class="newshead catGradient borderB">
      <div class="newsimage">
        <img src="<?php echo $news->GetAuthorImage(); ?>" width="40px" height="40px">
      </div>
      <div class="newsauthortime">
        <a class="textColor" href="?p=profil&id=<?php echo $news->GetAuthorID(); ?>"><?php echo $news->GetAuthor(); ?></a><br />
        <?php echo $news->GetDate(); ?>
      </div>
      <div class="newstitle">
        <?php
        if($player->GetArank() == 3)
        {
            ?>
                <h1 style="font-size: 94%; margin-top:3px; margin-right:150px; width: 350px; text-align: left;"><a href="index.php?p=admin&a=see&table=News&id=<?php echo $news->GetID(); ?>" target="blank"><?php echo $news->GetTitle(); ?></a></h1>
            <?php
        }
        else
        {
        ?>
            <h1 style="font-size: 94%; margin-top:3px; margin-right:150px; width: 350px; text-align: left;"><?php echo $news->GetTitle(); ?></h1>
        <?php
        }
      ?>
      </div>
    </div>
    <article>
      <div class="newscontent smallBG">
        <?php echo $bbcode->parse($news->GetText()); ?>
      </div>
    </article>
    <hr>
    <details>
      <summary style="cursor: pointer;"><b> <?php $commentCount = $news->GetCommentCount();
                    echo number_format($commentCount,'0', '', '.');
                    if ($commentCount == 1) echo ' Kommentar';
                    else echo ' Kommentare'; ?></b></summary>
      <?php
      if ($commentCount != 0)
      {
      ?>
        <table width="100%" cellspacing="0" border="0">
          <tr>
            <td class="catGradient borderB borderT" colspan="6" align="center"><b>User Antworten</b></td>
          </tr>
          <?php
          {
            $comments = $news->GetComments();
            $j = 0;
            while (isset($comments[$j]))
            {
              $comment = explode(';', $comments[$j]);
          ?>
              <tr>
                  <?php
                      if($player->GetArank() >= 2)
                      {
                          ?>
                              <td width="5%">
                                  <center><b><a href="?p=news&a=delete&id=<?php echo $news->GetID(); ?>&comment=<?php echo array_search($comments[$j], $comments); ?>">X</a></b></center>
                              </td>
                          <?php
                      }
                  ?>
                <td width="25%">
                  <center><b><a href="?p=profil&id=<?php echo $comment[1] ?>"><?php echo $comment[0]; ?></a>: </b></center>
                </td>
                <td width="75%">
                  <center><?php echo $comment[2] ?></center>
                </td>
              </tr>
          <?php
              ++$j;
            }
          }
          ?>
        </table>
      <?php
      }
      ?>
      <?php
      if ($player->IsLogged())
      {
      ?>
        <div class="spacer"></div>
        <form method="post" action="?p=news&a=post&id=<?php echo $news->GetID(); ?>">
          <table width="100%" cellspacing="0" border="0">
            <tr>
              <td class="catGradient borderB borderT" colspan="6" align="center"><b>Kommentar Posten</b></td>
            </tr>
            <tr>
              <td width="100%">
                <div class="spacer"></div>
                <center> <textarea class="textarea" name="text" maxlength="300000" style="resize: none; width:400px; height:100px;"></textarea> </center>
              </td>
            </tr>
            <tr>
              <td width="100%">
                <center><input type="submit" value="Senden">
                  <div class="spacer"></div>
                </center>
              </td>
            </tr>
          </table>
        </form>
        <fieldset>
          <legend><b>Kommentar Regeln:</b></legend>
          <table>
            <tr>
              <td>
                <b>
                  <span style="color: #0066FF">§1:</span>
                </b>
              </td>
              <td>Dein Kommentar soll sich nur auf den aktuellen News Post beziehen.</td>
            </tr>
            <tr>
              <td>
                <b>
                  <span style="color: #0066FF">§2:</span>
                </b>
              </td>
              <td>Dein Kommentar darf keine beleidigungen, rassistischen oder pornografischen Inhalte und/oder Texte enthalten.</td>
            </tr>
            <tr>
              <td>
                <b>
                  <span style="color: #0066FF">§3:</span>
                </b>
              </td>
              <td>Dein Kommentar darf kein duplikat eines anderen Kommentars sein.</td>
            </tr>
            <tr>
              <td>
              </td>
            </tr>
          </table>
        </fieldset>
      <?php
      }
      ?>
    </details>
    <div class="newsfooter"></div>
  </div>
  <div class="newsspanbig"></div>
<?php
}
?>