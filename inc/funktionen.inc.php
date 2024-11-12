<?php

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