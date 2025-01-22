<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'datenbank.inc.php';

// Debugging-Hilfsfunktion
function debug_log($message)
{
    error_log("[DEBUG] " . $message);
}

// Überprüfen, ob der Benutzer über Email-Adresse eingeloggt ist
if (!isset($_SESSION['eingeloggt'])) {
    $_SESSION['meldung'] = ("Sie müssen eingeloggt sein, um diese Funktion zu nutzen.");
    debug_log("Benutzer ist nicht eingeloggt.");
    header("Location: ../login.php"); // Weiterleitung zur Login-Seite
    exit();
}

$mail = $_SESSION['eingeloggt']; // E-Mail-Adresse des Benutzers aus der Session
debug_log("Benutzer eingeloggt mit E-Mail: $mail");

// Überprüfen wegen Windows oder Linux
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $userdDirectory = "C:/var/www/html/usersEmails/$mail/";
} else {
    $userdDirectory = "/home/$mail/";
}
debug_log("Benutzerverzeichnis: $userdDirectory");

// Abrufen der Dateien im Home-Verzeichnis
function getUserFiles($dir)
{
    $files = [];
    if (is_dir($dir)) {
        foreach (scandir($dir) as $file) {
            if ($file !== "." && $file !== "..") {
                $files[] = $file;
            }
        }
    } else {
        debug_log("Verzeichnis nicht gefunden: $dir");
    }
    return $files;
}

// Dateien abrufen
$userFiles = getUserFiles($userdDirectory);
debug_log("Dateien im Verzeichnis: " . json_encode($userFiles));

// Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $uploadFile = $_FILES['file'];
    $targetPath = $userdDirectory . basename($uploadFile['name']);

    debug_log("Hochladen von Datei: " . json_encode($uploadFile));
    
    if (move_uploaded_file($uploadFile['tmp_name'], $targetPath)) {
        $_SESSION['meldung'] = "Datei ist hochgeladen";
        debug_log("Datei erfolgreich hochgeladen nach: $targetPath");
    } else {
        $_SESSION['meldung'] = "Fehler beim Hochladen";
        debug_log("Fehler beim Hochladen von Datei: " . json_encode(error_get_last()));
    }
    // Seite neu laden
    header("Location: ../index.php");
    exit();
}

// Datei löschen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_file'])) {
    $file_delete = $userdDirectory . basename($_POST['delete_file']);
    debug_log("Datei löschen: $file_delete");
    if (file_exists($file_delete)) {
        unlink($file_delete);
        $_SESSION['meldung'] = "Datei erfolgreich gelöscht!";
        debug_log("Datei gelöscht: $file_delete");
    } else {
        $_SESSION['meldung'] = "Datei nicht gefunden.";
        debug_log("Datei nicht gefunden: $file_delete");
    }
    header("Location: ../index.php"); // Seite nach Upload neu laden
    exit();
}

// Download Datei
if (isset($_GET['download'])) {
    $file_download = $userdDirectory . basename($_GET['download']);
    debug_log("Datei herunterladen: $file_download");
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
        debug_log("Datei zum Download nicht gefunden: $file_download");
    }
}

// Fehlerkonsole im Frontend ausgeben
if (isset($_SESSION['meldung'])) {
    echo "<script>console.log('" . addslashes($_SESSION['meldung']) . "');</script>";
}
?>
