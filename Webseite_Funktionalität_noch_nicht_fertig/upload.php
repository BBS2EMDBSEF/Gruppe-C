<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$username = $_SESSION['username'];
$user_directory_path = "user_directories/$username/";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $target_file = $user_directory_path . basename($_FILES['file']['name']);

    if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
        echo "Datei erfolgreich hochgeladen. <a href='home.php'>Zurück</a>";
    } else {
        echo "Fehler beim Hochladen. <a href='home.php'>Zurück</a>";
    }
}
?>
