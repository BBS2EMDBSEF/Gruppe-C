<?php

$optionen = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
];
$db = new PDO('mysql:host=localhost;dbname=BBS_Projekt','root',''); 


$db->query('SET NAMES utf8');


