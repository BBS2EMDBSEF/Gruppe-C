<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if (isset($_GET['user'])) {
    $user_directory_path = "../user_directories/" . $_GET['user'];

    if (is_dir($user_directory_path)) {
        array_map('unlink', glob("$user_directory_path/*"));
        rmdir($user_directory_path);
        echo "Benutzerverzeichnis gelöscht. <a href='manage_users.php'>Zurück</a>";
    } else {
        echo "Benutzerverzeichnis nicht gefunden. <a href='manage_users.php'>Zurück</a>";
    }
}
?>
