<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set("memory_limit", "-1");
set_time_limit(0);

include 'EmailValidation.php';

$emailValuidObj = new EmailValidation();
$t = $emailValuidObj->checkEmail('example@gmail.com');
echo "<pre>";print_r($t);
echo "<br />";
echo '-------Hurray Over-------';
