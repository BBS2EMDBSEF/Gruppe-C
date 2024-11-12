<?php 

session_start();

require_once 'datenbank.inc.php';
require_once 'funktionen.inc.php';

$gs = bereinige($_POST['geschlecht']);
$nn = bereinige($_POST['nachname']);
$vn = bereinige($_POST['vorname']);
$geb = insertDatum($_POST['geburtstag']);
$tel = bereinige($_POST['telefon']);
$janein = bereinige($_POST['janein']);


$wunschtag = bereinige($_POST['wunschtag']);


$mail = bereinige($_POST['mail']);
$pwd = password_hash($_POST['passwort'], PASSWORD_DEFAULT);

$mailEindeutig = 'SELECT * FROM users WHERE email = ?';
$statement = $db->prepare($mailEindeutig);
$statement->execute([$mail]);
$user = $statement->fetch();





if(!($user)) {
  $sql = 'INSERT INTO users(geschlecht,nachname,vorname,geburtstag,telefon,janein,wunschtag,email,passwort,created_at)
                      VALUES(?,?,?,?,?,?,?,?,?, NOW())';
  $statement = $db->prepare($sql);
  $statement->execute([$gs,$nn,$vn,$geb,$tel,$janein,$wunschtag,$mail,$pwd]);  
     
  $_SESSION['meldung'] = 'Registrierung erfolgreich, Bitte sich einloggen';

  redirect('../index.php');
}
else {
  $_SESSION['meldung'] = 'Die E-Mail existiert!<br />Bitte andere E-Mail eingeben';
  redirect('../index.php?page=registrierung');
}