<?php
ob_start();
include_once 'c.php';
include_once 'f.php';
session_start();

$act = isset($_GET['act']) ? $_GET['act'] : '';
if (!isset($_SESSION['admin']) || $_SESSION['admin'] != $settings['adminpass']) {
    $act = '';
}
?>
    <!doctype html>
    <html>

    <head>
        <title><?= $settings['title']; ?> — Панель управления</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
        <link rel="stylesheet" href="/css/bootstrap.css">
        <link rel="stylesheet" href="/css/bootstrap-responsive.css">
        <link rel="stylesheet" href="/css/custom.css">
        <script src="/js/jquery.min.js"></script>
        <script src="/js/bootstrap.min.js"></script>

        <style type="text/css">
            .red{color: red}
            .green{color: green}
        </style>
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
        <hr style="clear:both">
        <?
        switch ($act) {
            case 'delallkeys':
            if (isset($_POST['delallkeys']) and $_POST['ip'] != "")
            {
                $ip = $_POST['ip'];
                mysql_query("DELETE FROM `keys` WHERE `ip` = '$ip'");
                die ("OK");
            }
            elseif (isset($_POST['delallkeys']) and $_POST['ip'] == "")
            {
                die ("WHERE IS IP?");
            }

            echo "<form method=\"post\">";
            echo "<input type=\"text\" name=\"ip\" placeholder=\"IP\" required><br>";
            echo "<input type=\"submit\" name=\"delallkeys\" value=\"Удалить все ключи\">";
            echo "</form>";
            break;

            case 'delallcomments':
            if (isset($_POST['delallcomments']) and $_POST['ip'] != "")
            {
                $ip = $_POST['ip'];
                mysql_query("DELETE FROM `comments` WHERE `ip` = '$ip'");
                die ("OK");
            }
            elseif (isset($_POST['delallcomments']) and $_POST['ip'] == "")
            {
                die ("WHERE IS IP?");
            }

            echo "<form method=\"post\">";
            echo "<input type=\"text\" name=\"ip\" placeholder=\"IP\" required><br>";
            echo "<input type=\"submit\" name=\"delallcomments\" value=\"Удалить все комментарии\">";
            echo "</form>";
            break;

            case 'delkey':
            if (isset($_POST['delkey']) and $_POST['id'] != "")
            {
                $id = $_POST['id'];
                mysql_query("DELETE FROM `keys` WHERE `id` = '$id'");
                die ("OK");
            }
            elseif (isset($_POST['delkey']) and $_POST['id'] == "")
            {
                die ("WHERE IS ID?");
            }

            echo "<form method=\"post\">";
            echo "<input type=\"text\" name=\"id\" placeholder=\"ID\" required><br>";
            echo "<input type=\"submit\" name=\"delkey\" value=\"Удалить ключ\">";
            echo "</form>";
            break;

            case 'delcomment':
            if (isset($_POST['delcomment']) and $_POST['id'] != "")
            {
                $id = $_POST['id'];
                mysql_query("DELETE FROM `comments` WHERE `id` = '$id'");
                die ("OK");
            }
            elseif (isset($_post['delcomment']) and $_POST['id'] == "")
            {
                die ("WHERE IS ID?");
            }

            echo "<form method=\"post\">";
            echo "<input type=\"text\" name=\"id\" placeholder=\"ID\" required><br>";
            echo "<input type=\"submit\" name=\"delcomment\" value=\"Удалить комментарий\">";
            echo "</form>";
            break;

            case 'delete_ban':
                $ip = isset($_GET['ip'])?$_GET['ip']:null;
                if (!$ip) {
                    die('Введи ip, сука');
                }
                $c = mysql_query("SELECT COUNT(*) FROM `bans` WHERE `ip` = '".mysql_real_escape_string($ip)."'");
                if ($c < 0) {
                    die('Бан не найден');
                }
                mysql_query("DELETE FROM `bans` WHERE `ip` = '".mysql_real_escape_string($ip)."'");
                header('location:/admin.php?act=ban');
                break;
            case'ban':
                if (isset($_POST['ip'])) {
                    $ip = $_POST['ip'];
                    $time = intval($_POST['time_ban']);
                    $reason = mysql_real_escape_string(htmlspecialchars($_POST['reason']));
                    mysql_query("INSERT INTO `bans` (`ip`,`time`,`time_end`,`reason`) VALUES ('".$ip."',
                    '".time()."','".(time()+($time*60*60))."','".$reason."')");
                    header("Location:/admin.php?act=ban");
                }else {
                    echo '
                    <form action="/admin.php?act=ban" method="post">
                        <b>Введите ойпи:</b><br>
                        <input type="text" name="ip" id=""/><br>
                        <b>Время:</b><br>
                        <select name="time_ban">
                            <option value="1">1 час</option>
                            <option value="3">3 часа</option>
                            <option value="24">1 день</option>
                            <option value="9999999999999999">Навсегда</option>
                            <!-- ну типо навсегда, ага -->
                        </select><br>
                        <b>Причина:</b><br>
                        <textarea name="reason"></textarea><br>
                        <input type="submit" value="Забанить" class="btn btn-primary"/>
                    </form>
                    ';
                    echo '<hr/>
                    <h3>Пидорнутые:</h3><br>';
                    $q = mysql_query("SELECT * FROM `bans` ORDER BY `id` DESC");
                    while($a=mysql_fetch_array($q)){
                        echo '
                        <div class="well">
                            <b>IP:</b> '.$a['ip'].'<br/>
                            <b>Был забанен:</b> '.formatDate($a['time']).'<br/>
                            <b>Будет разбанен:</b> '.formatDate($a['time_end']).'<br/>
                            <b>Активен:</b> '.(time()<$a['time_end']?'<span class="green">Да</span>':'<span class="red">Нет</span>').'<br/>
                            <b>Причина:</b> '.$a['reason'].'<br />
                            <a href="/admin.php?act=delete_ban&amp;ip='.$a['ip'].'">Удалить бан</a>
                        </div>
                        ';
                    }
                }
                break;
            default:
                if (isset($_POST['pass'])) {
                    $pass = $_POST['pass'];
                    if ($pass == $settings['adminpass']) {
                        $_SESSION['admin'] = $pass;
                    }
                    header('location:/admin.php');
                } else if (!isset($_SESSION['admin']) || $_SESSION['admin'] != $settings['adminpass']) {
                    echo '
                <form action="/admin.php" method="post">
                    <b>Введи пароль:</b><br>
                    <input type="password" name="pass"/><br>
                    <input type="submit" value="OK"/>
                </form>
                ';
                } else {
                    echo '
                    <a href="/admin.php?act=ban">БАН ПО ОЙПИ</a><br>
                    <a href="/admin.php?act=delallkeys">Удалить все ключи</a><br>
                    <a href="/admin.php?act=delallcomments">Удалить все комментарии</a><br>
                    <a href="/admin.php?act=delkey">Удалить ключ</a><br>
                    <a href="/admin.php?act=delcomment">Удалить комментарий</a>
                    ';
                }
                break;
        }
        ?>
    </div>
    </body>

    </html> <? ob_end_flush();