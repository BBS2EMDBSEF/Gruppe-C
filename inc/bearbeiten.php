<?php 

session_start();

require_once 'datenbank.inc.php';
require_once 'funktionen.inc.php';

$gs = bereinige($_GET['geschlecht']);
$nn = bereinige($_GET['nachname']);
$vn = bereinige($_GET['vorname']);
$geb = insertDatum($_GET['geburtstag']);
$tel = bereinige($_GET['telefon']);
$janein = bereinige($_GET['janein']);


$wunschtag = bereinige($_GET['wunschtag']);


$mail = bereinige($_GET['mail']);
$pwd = password_hash($_GET['passwort'], PASSWORD_DEFAULT);

$mailEindeutig = 'SELECT * FROM users WHERE email = ?';
$statement = $db->prepare($mailEindeutig);
$statement->execute([$mail]);
$user = $statement->fetch();





if(!($user)) {
  $sql = 'SELECT FROM users(geschlecht,nachname,vorname,geburtstag,telefon,janein,wunschtag,email,passwort,created_at)
                      VALUES(?,?,?,?,?,?,?,?,?, NOW())';
  $statement = $db->prepare($sql);
  $statement->execute([$gs,$nn,$vn,$geb,$tel,$janein,$wunschtag,$mail,$pwd]);  
     
  $_SESSION['meldung'] = 'Registrierung erfolgreich, Bitte sich einloggen';

  redirect('../index.php');
}
else {
  $_SESSION['meldung'] = 'Die E-Mail existiert!<br />Bitte andere E-Mail eingeben';
  redirect('../index.php?page=bearbeiten');
}