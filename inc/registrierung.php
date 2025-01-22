<?php 

session_start();

require_once 'datenbank.inc.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
$nn = trim($_POST['nachname']);
$vn = trim($_POST['vorname']);

$username = trim($_POST['username']);
$pwd = password_hash($_POST['passwort'], PASSWORD_DEFAULT);

$usernameEindeutig = 'SELECT * FROM users WHERE username = ?';
$statement = $db->prepare($usernameEindeutig);
$statement->execute([$username]);
$user = $statement->fetch();

}

if(!($user)) {
  $sql = 'INSERT INTO users(nachname,vorname,username,passwort)
                      VALUES(?,?,?,?)';
  $statement = $db->prepare($sql);
  $statement->execute([$nn,$vn,$username,$pwd]);  
     
  $_SESSION['meldung'] = 'Registrierung erfolgreich, Bitte sich einloggen';

  header('Location:'.'../index.php');
  exit;
}
else {
  $_SESSION['meldung'] = 'Der Benutzername existiert!<br />Bitte andere Benutzername eingeben';
  header('../index.php?page=registrierung');
}
