<?php 

session_start();

if(isset($_SESSION['meldung'])) {
	$meldung = $_SESSION['meldung'];
	unset($_SESSION['meldung']);
}

require_once 'inc/datenbank.inc.php';

$page = $_GET['page'] ?? '';

switch($page) {
	case 'registrierung': include 'inc/register_form.inc.php'; break;
	default: include 'inc/home.inc.php'; break;
}
