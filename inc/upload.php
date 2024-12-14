<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'datenbank.inc.php';

// Überprüfen, ob der Benutzer eingeloggt ist und eine E-Mail-Adresse in der Session gespeichert ist
if (!isset($_SESSION['eingeloggt'])) {
    $_SESSION['meldung'] =("Sie müssen eingeloggt sein, um diese Funktion zu nutzen.");
}

$mail = $_SESSION['eingeloggt']; // E-Mail-Adresse des Benutzers aus der Session

// Überprüfen, ob der Server unter Windows oder Linux läuft
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    // Windows
    $user_home_dir = "C:/var/www/html/users/$mail/";
} else {
    // Linux
    $user_home_dir = "/home/$mail/";
}

// Funktion zum Abrufen der Dateien im Home-Verzeichnis
function getUserFiles($directory) {
    $files = [];
    if (is_dir($directory)) {
        foreach (scandir($directory) as $file) {
            if ($file !== "." && $file !== "..") {
                $files[] = $file;
            }
        }
    }
    return $files;
}

// Dateien im Home-Verzeichnis abrufen
$user_files = getUserFiles($user_home_dir);

// Upload-Funktion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $upload_file = $_FILES['file'];
    $target_path = $user_home_dir . basename($upload_file['name']);

    if (move_uploaded_file($upload_file['tmp_name'], $target_path)) {
        $_SESSION['meldung'] = "Datei erfolgreich hochgeladen!";
    } else {
        $_SESSION['meldung'] = "Fehler beim Hochladen der Datei.";
    }
    header("Location: ../index.php"); // Seite nach Upload neu laden
    exit();
}

// Löschfunktion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_file'])) {
    $file_to_delete = $user_home_dir . basename($_POST['delete_file']);
    if (file_exists($file_to_delete)) {
        unlink($file_to_delete);
        $_SESSION['meldung'] = "Datei erfolgreich gelöscht!";
        header("Location: ../index.php"); // Seite nach Upload neu laden
        exit();
    } else {
        $_SESSION['meldung'] = "Datei konnte nicht gefunden werden.";
    }
}

// Download-Funktion
if (isset($_GET['download'])) {
    $file_to_download = $user_home_dir . basename($_GET['download']);
    if (file_exists($file_to_download)) {
        header('Content-Description: File Transfer');
        header('Content-type: application/octet-stream');
        header("Content-Type: " . mime_content_type($file_to_download));
        header('Content-Disposition: attachment; filename="' . basename($_GET['download']) . '"');
        header('Content-Length: ' . filesize($file_to_download));
        while (ob_get_level()) {
            ob_end_clean();
        }
        readfile($file_to_download);
        exit();
    } else {
        $_SESSION['meldung'] = "Datei konnte nicht gefunden werden.";
    }
}
