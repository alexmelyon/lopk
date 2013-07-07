<?php
require('c.php');
require('f.php');
is_banned();
session_start();

if (!isset($_GET['id']))
{
  die ("Wrong ID.");
}
else
{
  $id = intval($_GET['id']);
}

if (isset($_POST['sendComment']) and $_SESSION['security_code'] == $_POST['captcha'])
{
  if ($_POST['comment'] == "")
  {
    die("HUI SOSI");
  }

  $comment = mysql_real_escape_string(nl2br(htmlspecialchars($_POST['comment'])));
  $date = time();
  $ip = $_SERVER['REMOTE_ADDR'];

  $send_query = mysql_query("INSERT INTO `comments` VALUES ('', '$id', '$comment', '$date', '$ip')") or die (mysql_error());
}
elseif (isset($_POST['sendComment']) and $_SESSION['security_code'] != $_POST['captcha'])
{
  die("Wrong captcha!");
}

$result = mysql_query("SELECT * FROM `keys` WHERE `id` = '$id'") or die (mysql_error());
$key = mysql_fetch_array($result);

$comments = mysql_query("SELECT * FROM `comments` WHERE `keyID` = '$id' ORDER BY `id` ASC") or die (mysql_error());
$comment = mysql_fetch_array($comments);
?>
<!doctype html>
<html>
  
  <head>
    <title><?= $settings['title']; ?> — <?= $key['name']; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
    <link rel="stylesheet" href="/css/bootstrap.css">
    <link rel="stylesheet" href="/css/bootstrap-responsive.css">
    <link rel="stylesheet" href="/css/custom.css">
    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
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
            </ul>
          </div>
        </div>
      </div>
    </div>
    <br>
    <div class="container">
      <div class="row main-features"></div>
      <?php
      if (mysql_num_rows($result))
      {
        echo "<div class=\"container\">";
        echo "<hr>";
        echo "<b>" . $key['name'] . "</b>";
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
        echo "</i><br>Ключ: " . $key['key'] . "<br><br>" . formatDate($key['date']) . "<br>";
        if (isset($_SESSION['admin']) && $_SESSION['admin'] == $settings['adminpass'])
        {
          echo "<i>" . $key['ip'] . "</i>";
        }
        echo "</div>";
        echo "</div>";
      }
      else
      {
        die("Данного ключа не существует. Или он был удален.");
      }
      ?>
      <div class="container"><hr> Комментарии:</div>
      <?php
      $i = 1;
      do
      {
        if (mysql_num_rows($comments))
        {
          echo "<div class=\"container\">";
          echo "<hr>";
          echo '<b style="float:right;color:green;">'.$i.'</b>';
          echo "<span class=\"date\">" . formatDate($comment['date']) . "</span>";
            if (isset($_SESSION['admin']) && $_SESSION['admin'] == $settings['adminpass']){
                echo " [<a href=\"/admin.php?act=ban\">БАН ПО ОЙПИ</a>] (<i>" . $comment['ip'] ."</i>) #" . $comment['id'] . "";
            }
          echo "<p class=\"comment\">" . $comment['comment'] . "</p>";
          echo "</div>";
          $i++;
        }
        else
        {
          echo "<center><b>Комментариев нет.</b></center>";
        }
      } while ($comment = mysql_fetch_array($comments));
      ?>
    <br>
    <hr>
    <div class="container">
      <form method="post">
        <textarea class="input-block-level" name="comment" rows="5" required></textarea>
        <hr>
        <p class="form-captcha"><a href="#" onclick="javascript:document.getElementById('captchaimg').src = '/captcha.php?' + Math.random();return false;"><img alt src="/captcha/" id="captchaimg" width="100" height="40"></a> <input type="text" name="captcha" id="captcha" required></p><hr>
        <div class="form-actions">
          <button type="submit" name="sendComment" class="btn btn-primary btn-block">Оставить комментарий</button>
        </div>
      </form>
    </div>
  </body>

</html>