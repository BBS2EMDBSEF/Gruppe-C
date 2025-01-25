<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'datenbank.inc.php';

// 1) Prüfen, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['eingeloggt'])) {
    $_SESSION['meldung'] = "Sie müssen eingeloggt sein, um diese Funktion zu nutzen.";
    // Zurück zur Startseite
    header("Location: ../index.php");
    exit;
}

// Name des aktuell eingeloggten Benutzers aus der Session
$username = $_SESSION['eingeloggt'];

// 2) Je nach OS ein anderes Basis-Verzeichnis
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    // Unter Windows (Beispielpfad, ggf. anpassen!)
    $userDirectory = "C:/var/www/html/usernames/$username/";
} else {
    // Unter Linux (Beispielpfad, ggf. anpassen!)
    $userDirectory = "/home/$username/";
}

// 3) Verzeichnis ggf. anlegen, falls nicht vorhanden
if (!is_dir($userDirectory)) {
    // 0775 = lesen/schreiben für User+Gruppe, lesen für andere
    // true = ggf. verschachtelte Unterordner automatisch anlegen
    mkdir($userDirectory, 0775, true);
    
    // Unter Linux ggf. Ownership auf www-data:www-data setzen:
    // chown($userDirectory, 'www-data');
    // chgrp($userDirectory, 'www-data');
}

// 4) Dateien im Benutzerverzeichnis auflisten (zur Anzeige)
function getUserFiles($dir) {
    $files = [];
    if (is_dir($dir)) {
        foreach (scandir($dir) as $file) {
            if ($file !== "." && $file !== "..") {
                $files[] = $file;
            }
        }
    }
    return $files;
}

$userFiles = getUserFiles($userDirectory);

// 5) Upload-Bereich
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    // Prüfen, ob Upload ohne Fehler
    if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        // Datei kam nicht fehlerfrei an – Grund ausgeben
        $errorCode = $_FILES['file']['error'];
        $_SESSION['meldung'] = "Fehler beim Hochladen (Error Code: $errorCode)";
        header("Location: ../index.php");
        exit;
    }

    // Pfad zum Hochladen definieren
    $uploadFile = $_FILES['file'];
    $targetPath = $userDirectory . basename($uploadFile['name']);

    // Datei verschieben
    if (move_uploaded_file($uploadFile['tmp_name'], $targetPath)) {
        $_SESSION['meldung'] = "Datei „" . basename($uploadFile['name']) . "“ erfolgreich hochgeladen!";
    } else {
        $_SESSION['meldung'] = "Fehler beim Hochladen (move_uploaded_file schlug fehl).";
    }
    // Seite neu laden
    header("Location: ../index.php");
    exit;
}

// 6) Datei löschen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_file'])) {
    $fileToDelete = $userDirectory . basename($_POST['delete_file']);
    if (file_exists($fileToDelete)) {
        unlink($fileToDelete);
        $_SESSION['meldung'] = "Datei „" . basename($_POST['delete_file']) . "“ wurde gelöscht.";
    } else {
        $_SESSION['meldung'] = "Datei nicht gefunden.";
    }
    header("Location: ../index.php");
    exit;
}

// 7) Datei-Download
if (isset($_GET['download'])) {
    $fileToDownload = $userDirectory . basename($_GET['download']);
    if (file_exists($fileToDownload)) {
        // Header für den Download
        header('Content-Description: File Transfer');
        header('Content-Type: ' . mime_content_type($fileToDownload));
        header('Content-Disposition: attachment; filename="' . basename($fileToDownload) . '"');
        header('Content-Length: ' . filesize($fileToDownload));
        // Alle Ausgabe-Puffer leeren
        while (ob_get_level()) {
            ob_end_clean();
        }
        readfile($fileToDownload);
        exit;
    } else {
        $_SESSION['meldung'] = "Datei nicht gefunden.";
    }
    // Weiterleitung, wenn die Datei nicht existiert
    header("Location: ../index.php");
    exit;
}
