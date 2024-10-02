<?php
setlocale(LC_ALL, "de_DE", "deu_deu", "german");

function formatiereDatum($dbDatum, $format = 'EEEE, dd.MM.yyyy HH:mm:ss')
{
    $datum = new DateTime($dbDatum);
    $formatter = new IntlDateFormatter(
        'de_DE',                // Deutsche Lokalisierung
        IntlDateFormatter::FULL, // Datumslänge (kann angepasst werden)
        IntlDateFormatter::FULL  // Zeitlänge (kann angepasst werden)
    );
    
    // Setzt das benutzerdefinierte Format
    $formatter->setPattern($format);
    
    return $formatter->format($datum);
}


//formatiereDatum(meineSpalteAusTabelle[created_at])
//---------------------------------------------
function insertDatum($wert) {
  return date('Y-m-d', strtotime($wert));
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