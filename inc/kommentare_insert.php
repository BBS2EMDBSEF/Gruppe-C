<?php 

session_start();

require_once 'datenbank.inc.php';
require_once 'funktionen.inc.php';

if( !empty($_POST) ) {
  $sql = 'INSERT INTO kommentare(user_id, post, created_at)
                      VALUES(:userid, :comment, NOW() )';
  $statement = $db->prepare($sql);
  $statement->execute($_POST);

  $_SESSION['meldung'] = 'Kommentar wurde eingefügt<br />Vielen Dank';
  redirect('../index.php?page=kommentare');
}