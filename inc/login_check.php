<?php 
session_start();
require_once 'datenbank.inc.php';

$mail = $_POST['mail'];
$pwd = $_POST['pwd'];

if( !empty($mail) && !empty($pwd)) {
  $sql = 'SELECT * FROM users WHERE email = ?';
  $statement = $db->prepare($sql);
  $statement->execute([$mail]);
  $user = $statement->fetch();
  
  if($user && password_verify($pwd, $user['passwort'])) {
    $_SESSION['eingeloggt'] = $user['email'];
    $_SESSION['eingeloggt_user'] = $user['nachname'];
    $_SESSION['id'] = $user['id'];
    $_SESSION['meldung'] = 'Sie sind eingeloggt';
  } 
  else {
    $_SESSION['meldung'] = 'Falsche Logindaten oder Sie sind noch nicht Ihnen registrieren';
  }
  //--------------------------------
}
else {
  $_SESSION['meldung'] = 'Felder dürfen nicht leer sein';
}


header('Location:'.'../index.php');
  exit;
  
