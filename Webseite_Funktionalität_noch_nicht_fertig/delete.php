<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$username = $_SESSION['username'];
$user_directory_path = "user_directories/$username/";

if (isset($_GET['file'])) {
    $file = $user_directory_path . $_GET['file'];
    if (file_exists($file)) {
        unlink($file);
        echo "Datei gelöscht. <a href='home.php'>Zurück</a>";
    } else {
        echo "Datei nicht gefunden. <a href='home.php'>Zurück</a>";
    }
}
?>
