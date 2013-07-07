<?php
require('c.php');
require('f.php');
session_start();
is_banned();

if (isset($_POST['sendKey']) and $_SESSION['security_code'] == $_POST['captcha'])
{
  if ($_POST['name'] == "" or $_POST['key'] == "" or $_POST['description'] == "" or $_POST['tag'] == "")
  {
    die("HUI SOSI");
  }

  $key = mysql_real_escape_string(htmlspecialchars($_POST['key']));
  $name = mysql_real_escape_string(htmlspecialchars($_POST['name']));
  $description = mysql_real_escape_string(nl2br(strip_tags($_POST['description'])));
  $tag = mysql_real_escape_string(htmlspecialchars($_POST['tag']));
  $accessType = intval($_POST['accessType']);
  $temp = intval($_POST['24hours']);
  $date = time();
  $size = mysql_real_escape_string(htmlspecialchars($_POST['size']));
  $ip = $_SERVER['REMOTE_ADDR'];
  
  $exists_tags = array();
  $tags = explode(', ', $tag);

  if (count($tags)>5) die ("Тегов должно быть не более 5");

  $send_query = mysql_query("INSERT INTO `keys` VALUES ('', '$key', '$name', '$description', '$accessType', '$temp', '$date', '$tag', '$size', '$ip')") or die (mysql_error());
  
  $key_id = mysql_insert_id();
  
  foreach($tags as $tag) {
    if (!empty($tag)) {
        if (!in_array($tag, $exists_tags)) {
            mysql_query("INSERT INTO `tags` (`tag`,`key_id`) VALUES('".$tag."','".$key_id."')") or die(mysql_error());
            $exists_tags[]=$tag;
        }
    }
  }
  Header ("Location: /key/" . $key_id);
}
elseif (isset($_POST['sendKey']) and $_SESSION['security_code'] != $_POST['captcha'])
{
  die("Wrong captcha!");
}
?>
<!doctype html>
<html>
  
  <head>
    <title><?= $settings['title']; ?> — Добавить ключ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
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
              <li>
                <a href="/">Ключи</a> 
              </li>
              <li class="active">
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
    <div class="container">
      <div class="row main-features"></div>
      <div class="container">
        <div class="description"></div>
      </div>
      <br><br>
      <div class="container">
        <form method="post">
          <center><b>Внимание!</b> Все раздачи на версии <a href="http://syncapp.bittorrent.com/1.1.27/">1.1.27</a>.</center><hr>
          <input type="text" class="input-block-level" name="name" placeholder="Заголовок"
          id="name" required>
          <input type="text" pattern=".{11,}" class="input-block-level" name="key" placeholder="Ключ"
          id="key" required>
          <textarea class="input-block-level" rows="5" name="description" placeholder="Описание"
          id="description" required></textarea>
          <input type="text" class="input-block-level" name="tag" placeholder="Теги (разделяются запятыми, после запятой нужен пробел!)"
          id="tag" required>
          <input type="text" class="input-block-level" name="size" placeholder="Вес (размер), указывать в MB/GB"
          id="size" required>
          <select name="accessType" id="accessType">
            <option value="0">Только чтение</option>
            <option value="1">Полный доступ</option>
          </select>
          <label class="checkbox" for="checkbox">
            <input type="checkbox" id="checkbox" name="checkbox">
            <span>Ключ действует в течение 24 часов</span>
            <span></span> 
          </label>
          <hr>
          <p class="form-captcha"><a href="#" onclick="javascript:document.getElementById('captchaimg').src = '/captcha/?' + Math.random();return false;"><img alt src="/captcha/" id="captchaimg" width="100" height="40"></a> <input type="text" name="captcha" id="captcha" required></p><hr>
          <div class="form-actions">
            <button type="submit" name="sendKey" class="btn btn-block btn-success">Добавить</button>
          </div>
        </form>
      </div>
    </div>
  </body>

</html>