<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$username = $_SESSION['username'];

// Pfad für Benutzerverzeichnisse
$user_directory_path = "user_directories/$username/";

// Überprüfen, ob das Verzeichnis existiert. Wenn nicht, wird es erstellt.
if (!is_dir($user_directory_path)) {
    mkdir($user_directory_path, 0777, true);
}

// Dateien im Benutzerverzeichnis anzeigen
echo "<h1>Willkommen, $username</h1>";
echo "<h3>Dein persönliches Home-Verzeichnis:</h3>";

$files = array_diff(scandir($user_directory_path), array('.', '..'));

foreach ($files as $file) {
    echo "<a href='$user_directory_path$file' download>$file</a> 
          <a href='delete.php?file=$file'>Löschen</a><br>";
}
?>

<form action="upload.php" method="post" enctype="multipart/form-data">
    Datei hochladen: <input type="file" name="file"><br>
    <button type="submit">Hochladen</button>
</form>

<a href="index.php">Abmelden</a>
