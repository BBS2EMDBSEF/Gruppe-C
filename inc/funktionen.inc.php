<?php

setlocale(LC_ALL, "de_DE", "deu_deu","german");
function formatiereDatum($dbDatum, $format = '%A, %d.%m.%Y %H:%M:%S')
{
	return utf8_encode( strftime($format, strtotime($dbDatum)) );
}
//formatiereDatum(meineSpalteAusTabelle[created_at])
//---------------------------------------------
function insertDatum($wert) {
  return utf8_encode( strftime('%Y-%m-%d', strtotime($wert)) );
}
//insertDatum(meineSpalteAusTabelle[geburtstag])
//---------------------------------------------


function bereinige($userEingabe, $encoding = 'UTF-8') {
  return htmlspecialchars(
                        strip_tags(trim($userEingabe)), 
                        ENT_QUOTES | ENT_HTML5, 
                        $encoding);
}


function redirect($url) {
  header('Location:'.$url);
  exit;
}

function loggeAus() {
  unset($_SESSION['eingeloggt']);
  unset($_SESSION['eingeloggt_user']);
  unset($_SESSION['id']);
  unset($_SESSION['token']);
}