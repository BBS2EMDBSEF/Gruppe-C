<?php 

session_start();

require_once 'datenbank.inc.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
$nn = trim($_POST['nachname']);
$vn = trim($_POST['vorname']);

$mail = trim($_POST['mail']);
$pwd = password_hash($_POST['passwort'], PASSWORD_DEFAULT);

$mailEindeutig = 'SELECT * FROM users WHERE email = ?';
$statement = $db->prepare($mailEindeutig);
$statement->execute([$mail]);
$user = $statement->fetch();

}

if(!($user)) {
  $sql = 'INSERT INTO users(nachname,vorname,email,passwort)
                      VALUES(?,?,?,?)';
  $statement = $db->prepare($sql);
  $statement->execute([$nn,$vn,$mail,$pwd]);  
     
  $_SESSION['meldung'] = 'Registrierung erfolgreich, Bitte sich einloggen';

  header('Location:'.'../index.php');
  exit;
}
else {
  $_SESSION['meldung'] = 'Die E-Mail existiert!<br />Bitte andere E-Mail eingeben';
  header('../index.php?page=registrierung');
}
