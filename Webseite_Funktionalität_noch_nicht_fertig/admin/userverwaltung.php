<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$user_directories = array_diff(scandir('../user_directories'), array('.', '..'));

echo "<h1>Benutzerverwaltung</h1>";

foreach ($user_directories as $user) {
    echo "<h3>Benutzer: $user</h3>";
    $user_directory_path = "../user_directories/$user/";
    $files = array_diff(scandir($user_directory_path), array('.', '..'));

    foreach ($files as $file) {
        echo "<a href='$user_directory_path$file' download>$file</a> 
              <a href='delete.php?user=$user&file=$file'>Löschen</a><br>";
    }

    echo "<a href='delete_user.php?user=$user'>Benutzerverzeichnis löschen</a><br>";
}
?>
