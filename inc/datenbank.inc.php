<?php

$optionen = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
];
$db = new PDO('mysql:host=localhost;dbname=php_projekt','root',''); 


$db->query('SET NAMES utf8');


