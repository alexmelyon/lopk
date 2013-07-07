<?php
$settings = array (
    $mysql = array (
    "host" => "localhost",
    "user" => "root",
    "password" => "",
    "database" => "lopk"
    ),

    "adminpass" => "password",

    "siteName" => "BTSync List of Public Keys",
    "title" => "/b/ sync us"
);

date_default_timezone_set('Asia/Tbilisi'); // часовой пояс (в Europe/Moscow теряется час)
mysql_connect($mysql['host'], $mysql['user'], $mysql['password']);
mysql_select_db($mysql['database']);
mysql_query('SET NAMES utf8');

?>