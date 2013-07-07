<?php
ob_start();
include_once 'c.php';
include_once 'f.php';
session_start();

$tag = isset($_GET['tag']) ? htmlspecialchars($_GET['tag'], ENT_QUOTES) : null;
$tag_ = mysql_real_escape_string($tag);
?>
    <!doctype html>
    <html>

    <head>
        <title><?= $settings['title']; ?> — <?= $_GET['tag']; ?></title>
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
        <?php
        // пиздец просто // todo: оптимизировать
        $tagq = $tag_;
        $q = mysql_query("SELECT * FROM `tags` WHERE `tag` = '$tagq' ORDER BY `id` DESC");
        while($a=mysql_fetch_array($q)){
            $key_id = $a['key_id'];
            $o = mysql_query("SELECT * FROM `keys` WHERE `id` = '$key_id' ORDER BY `id` DESC");
            while($p = mysql_fetch_array($o)){
                echo '<div class="key"><a href="/key/'.$p['id'].'"><b>'.$p['name'].'</b></a></div><div class="description"><p>'.$p['description'].'</p></div><div class="accessType">Тип доступа: <i>'.($p['accessType']==0?'только чтение':'полный доступ').'</i>. Размер: <i>'.$p['size'].'</i>. Теги: <i>';
                $i_=0;
                $tags = explode(', ',$p['tag']);
                $c_tags = count($tags);
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
                echo '<br><br>'.formatDate($p['date']).' | Комментарии: ' . countcomments($p['id'])  . '</div><hr>';
            }
        }
        ?>
    </div>
    </body>

    </html> <? ob_end_flush();