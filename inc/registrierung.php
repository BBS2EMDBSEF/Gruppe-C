<?php
session_start();

require_once 'datenbank.inc.php';
require_once 'funktionen.inc.php';


$name = isset($_POST['name']) ? $_POST['name'] : '';
$mail = isset($_POST['email']) ? $_POST['email'] : '';
$pwd = password_hash($_POST['passwort'], PASSWORD_DEFAULT);


$mailEindeutig = 'SELECT * FROM users WHERE email = ?';
$statement = $db->prepare($mailEindeutig);
$statement->execute([$mail]);
$user = $statement->fetch();



if(!($user)) {
    $sql = 'INSERT INTO users(name,email,passwort,created_at)
                        VALUES(?,?,?, NOW())';
    $statement = $db->prepare($sql);
    $statement->execute([$name, $mail,$pwd]);  
       
    $_SESSION['meldung'] = 'Registrierung erfolgreich, Bitte sich einloggen';
  
    redirect('../index.php');
  }
  else {
    $_SESSION['meldung'] = 'Die E-Mail existiert!<br />Bitte andere E-Mail eingeben';
    redirect('../index.php?page=registrierung');
  }









