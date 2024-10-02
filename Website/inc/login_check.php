<?php 

session_start();
require_once 'datenbank.inc.php';
require_once 'funktionen.inc.php';

$mail = isset($_POST['email']) ? $_POST['email'] : '';
$pwd = isset($_POST['pwd']) ? $_POST['pwd'] : '';

if( !empty($mail) && !empty($pwd)) {
  $sql = 'SELECT * FROM users WHERE email = ?';
  $statement = $db->prepare($sql);
  $statement->execute([$mail]);
  $user = $statement->fetch();
  
  if($user && password_verify($pwd, $user['passwort'])) {
    $_SESSION['eingeloggt'] = $user['email'];
    $_SESSION['eingeloggt_user'] = $user['name'];
    $_SESSION['id'] = $user['id'];
  } 
  else {
    $_SESSION['meldung'] = 'Falsche Logindaten oder Sie sind noch nicht Ihnen registrieren';
  }
  //--------------------------------
}
else {
  $_SESSION['meldung'] = 'Felder dürfen nicht leer sein';
}

redirect('../index.php');