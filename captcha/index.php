<?php

error_reporting (E_ALL);

include('kcaptcha.php');

session_start();

$captcha = new KCAPTCHA();

$_SESSION['security_code'] = $captcha->getKeyString();

?>