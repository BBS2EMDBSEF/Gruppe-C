<?php

session_start();

unset($_SESSION['eingeloggt']);
unset($_SESSION['eingeloggt_user']);
unset($_SESSION['id']);
$_SESSION['meldung'] = 'Sie sind abgemeldet.';

header('Location:' . '../index.php');
exit;
