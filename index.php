<?php 

session_start();

if( !isset($_SESSION['token']) ) {
	$_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
}

if(isset($_SESSION['meldung'])) {
	$meldung = $_SESSION['meldung'];
	unset($_SESSION['meldung']);
}

require_once 'inc/datenbank.inc.php';
require_once 'inc/funktionen.inc.php';  

$page = $_GET['page'] ?? '';

require_once 'inc/header.inc.php';

switch($page) {
	case 'registrierung': include 'inc/register_form.inc.php'; break;
	case 'profil': include 'inc/profil.inc.php'; break;
	default: include 'inc/home.inc.php'; break;
}

require_once 'inc/footer.inc.php';

