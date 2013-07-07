<?php
require('c.php');
require('f.php');

if (!isset($_GET['page']) or $_GET['page'] < '1')
{
  $page = "1";
}
else
{
  $page = $_GET['page'];
}

if (isset($_GET['search']))
{
  $w = $_GET['search'];
}
elseif (isset($_POST['search']))
{
  $w = $_POST['search'];
  Header ("Location: /search.php?search=" . $w);
}
$w_=$w;
$w = mysql_real_escape_string($w);

$maxKeysOnPage = 15;
$start = $page * $maxKeysOnPage - $maxKeysOnPage;
$counter = mysql_query("SELECT COUNT(`id`) FROM `keys` WHERE `name` LIKE '%$w%' or WHERE `description` LIKE '%w%'");
$counter = mysql_fetch_array($counter);
$pages_all = $counter[0];

if (!isset($_GET['page']) or $_GET['page'] == '1')
{
  $result = mysql_query("SELECT * FROM `keys` WHERE `name` LIKE '%$w%' or `description` LIKE '%w%' ORDER BY `id` DESC LIMIT 0,{$maxKeysOnPage}") or die (mysql_error());
}
else
{
  $result = mysql_query("SELECT * FROM `keys` WHERE `name` LIKE '%$w%' or `description` LIKE '%w%' ORDER BY `id` DESC LIMIT {$start},{$maxKeysOnPage}") or die (mysql_error());
}

$key = mysql_fetch_array($result);
?>
<!doctype html>
<html>
  
  <head>
    <title><?= $settings['title']; ?> — Поиск</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/bootstrap-responsive.css">
    <link rel="stylesheet" href="css/custom.css">
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </head>
  
  <body>
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="brand" href="/"><?= $settings['siteName']; ?></a>
          <div class="navbar-content">
            <ul class="nav ">
              <li class="active">
                <a href="/">Ключи</a> 
              </li>
              <li>
                <a href="/add">Добавить</a> 
              </li>
              <li>
                <a href="/faq.php">FAQ</a>
              </li>
          </div>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="alert">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <b>Внимание!</b> Сайт находится на стадии разработки.
        <a href="mailto:BTSync.LOPK@somepony.org">Написать мне</a>.</div>
      <div class="row main-features"></div><br>
      <form method="post" action="/search.php">
        <input type="text" class="input-block-level" name="search" <?php if ($w != "") { echo "value=\"" . htmlspecialchars($w_) . "\""; } ?> placeholder="Поиск" id="search" required>
      </form>
      <?php
      do
      {
        if (mysql_num_rows($result))
        {
          echo "<div class=\"container\">";
          echo "<hr>";
          echo "<div class=\"key\">";
          echo "<a href=\"/key/" . $key['id'] . "\"><b>" . $key['name'] . "</b></a>";
          echo "</div>";
          echo "<div class=\"description\">";
          echo "<p>" . $key['description'] . "</p>";
          echo "</div>";

          if ($key['accessType'] == 0)
          {
            $accessType = "только чтение";
          }
          elseif ($key['accessType'] == 1)
          {
            $accessType = "полный доступ";
          }

          echo "<div class=\"accessType\">Тип доступа: <i>" . $accessType . "</i>. Размер: <i>" . $key['size'] . "</i>. Теги: <i>";
          $tags = explode(', ',$key['tag']);
          foreach($tags as $tag) {
            if ($tag != end($tags))
            {
              echo '<a href="/tag/'.$tag.'">'.$tag.'</a>, ';
            }
            else
            {
              echo '<a href="/tag/'.$tag.'">'.$tag.'</a>. ';
            }
          }
          echo "</i><br><br>" . formatDate($key['date']) . "| Комментарии: " . countcomments($key['id'])  . "</div>";
          echo "</div>";
        }
        else
        {
          echo "NOPE";
        }
      } while ($key = mysql_fetch_array($result));

      if (mysql_num_rows($result) and $pages_all > 10) 
      {
        echo "<hr>";
        echo "<div class=\"contents-paginator\">";
        echo makepagenavsearch($start, $maxKeysOnPage, $pages_all, 2);
        echo "</div>";
        echo "<hr>";
      }
      ?>
    </div>
  </body>

</html>