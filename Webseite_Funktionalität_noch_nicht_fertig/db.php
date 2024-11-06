<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bbs_projekt";

// Verbindung herstellen
$conn = new mysqli($servername, $username, $password);

// Verbindung prüfen
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Datenbank erstellen, wenn sie nicht existiert
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    $conn->select_db($dbname);

    // Tabelle erstellen, falls sie nicht existiert
    $table = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        password VARCHAR(255) NOT NULL
    )";

    $conn->query($table);

    // Prüfen, ob die Spalte 'email' existiert
    $column_check = "SHOW COLUMNS FROM users LIKE 'email'";
    $result = $conn->query($column_check);

    if ($result->num_rows == 0) {
        // Spalte 'email' hinzufügen, falls sie nicht existiert
        $alter_table = "ALTER TABLE users ADD email VARCHAR(50) NOT NULL DEFAULT ''";
    }
} else {
    die("Fehler beim Erstellen der Datenbank: " . $conn->error);
}
?>
