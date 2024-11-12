<?php 

session_start();
require_once 'funktionen.inc.php';

loggeAus();
$_SESSION['meldung'] = 'Sie sind abgemeldet.';

redirect('../index.php');