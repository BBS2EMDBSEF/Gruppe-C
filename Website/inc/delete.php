<?php 

session_start();
require_once 'datenbank.inc.php';
require_once 'funktionen.inc.php';

$id = $_SESSION['id'];

if( !empty($_POST) && $_POST['csrf_token'] === $_SESSION['token']) {
  $sql = 'DELETE FROM users WHERE id = ? ';
  $statement = $db->prepare($sql);
  $statement->execute([$id]);
  session_destroy();
  $_SESSION['meldung'] = 'account wurde gelöscht';
}

redirect('../index.php');