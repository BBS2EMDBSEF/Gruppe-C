<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'datenbank.inc.php';

// Überprüfen, ob der Benutzer über Email-Adresse eingeloggt ist
if (!isset($_SESSION['eingeloggt'])) {
    $_SESSION['meldung'] =("Sie müssen eingeloggt sein, um diese Funktion zu nutzen.");
}

$mail = $_SESSION['eingeloggt']; // E-Mail-Adresse des Benutzers aus der Session

// Überprüfen wegen Windows oder Linux
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $userdDirectory = "C:/var/www/html/usersEmails/$mail/";
} else {
    $userdDirectory = "/home/$mail/";
}

//Abrufen die Dateien im Home-Verzeichnis
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

// Dateien abrufen
$userFiles = getUserFiles($userdDirectory);

// Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    if (!is_dir($userdDirectory)) {
        mkdir($userdDirectory, 0777, true);
    }

    $uploadFile = $_FILES['file'];
    $targetPath = $userdDirectory . basename($uploadFile['name']);

    if ($uploadFile['error'] === UPLOAD_ERR_OK) {
        if (move_uploaded_file($uploadFile['tmp_name'], $targetPath)) {
            $_SESSION['meldung'] = "Datei wurde erfolgreich hochgeladen.";
        } else {
            $_SESSION['meldung'] = "Fehler beim Verschieben der Datei.";
        }
    } else {
        $_SESSION['meldung'] = "Upload-Fehler: " . $uploadFile['error'];
    }

    // Seite neu laden
    header("Location: ../index.php");
    exit();
}


// Datei löschen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_file'])) {
    $file_delete = $userdDirectory . basename($_POST['delete_file']);
    if (file_exists($file_delete)) {
        unlink($file_delete);
        $_SESSION['meldung'] = "Datei erfolgreich gelöscht!";
        header("Location: ../index.php"); // Seite nach Upload neu laden
        exit();
    } else {
        $_SESSION['meldung'] = "Datei nicht gefunden.";
    }
}

// Download Datei
if (isset($_GET['download'])) {
    $file_download = $userdDirectory . basename($_GET['download']);
    if (file_exists($file_download)) {
        header('Content-Description: File Transfer');
        header('Content-type: application/octet-stream');
        header("Content-Type: " . mime_content_type($file_download));
        header('Content-Disposition: attachment; filename="' . basename($_GET['download']) . '"');
        header('Content-Length: ' . filesize($file_download));
        while (ob_get_level()) {
            ob_end_clean();
        }
        readfile($file_download);
        exit();
    } else {
        $_SESSION['meldung'] = "Datei nicht gefunden.";
    }
}
