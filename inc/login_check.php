<?php
session_start();
require_once 'datenbank.inc.php';

$mail = $_POST['mail'];
$pwd = $_POST['pwd'];

if (!empty($mail) && !empty($pwd)) {
  $sql = 'SELECT * FROM users WHERE email = ?';
  $statement = $db->prepare($sql);
  $statement->execute([$mail]);
  $user = $statement->fetch();

  if ($user && password_verify($pwd, $user['passwort'])) {
    $_SESSION['eingeloggt'] = $user['email'];
    $_SESSION['eingeloggt_user'] = $user['nachname'];
    $_SESSION['id'] = $user['id'];
    $_SESSION['meldung'] = 'Sie sind eingeloggt';


    // Überprüfen, ob der Server unter Windows oder Linux läuft
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
      // Windows
      // Benutzerverzeichnis auf dem Server erstellen
      $homeDir = "/var/www/html/usersEmails/$mail";
      if (!is_dir($homeDir)) {
        mkdir($homeDir, 0755, true); // Verzeichnis erstellen
      }
    }
    else {
      $userdDirectory = "/home/$mail/";
   }
  } else {
    $_SESSION['meldung'] = 'Falsche Logindaten oder Sie sind noch nicht registriert';
  }
  //--------------------------------
} else {
  $_SESSION['meldung'] = 'Felder dürfen nicht leer sein';
}


header('Location:' . '../index.php');
exit;
