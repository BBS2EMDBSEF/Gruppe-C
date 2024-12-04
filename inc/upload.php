<?php

session_start();

include 'datenbank.inc.php';

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ini_set('file_uploads', 'On');
ini_set('upload_max_filesize', '5M');
ini_set('post_max_size', '8M');
*/
$directory = __DIR__ . '/';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $filename = basename($_FILES["file"]["name"]);
    $filedata = file_get_contents($_FILES["file"]["tmp_name"]);
    $file_tmp =$_FILES['file']['tmp_name'];
    $filePath = ($directory . $filename);
    move_uploaded_file($_FILES["file"]["tmp_name"], $filePath);
    $uploadOk = 1;

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $_SESSION['meldung'] = "Sorry, your file was not uploaded.";
    } else {
            $stmt = $db->prepare("INSERT INTO files (user_id, file_name, file_path) VALUES (?, ?, ?)");
            $stmt->bindParam(1, $_SESSION['id']);
                $stmt->bindParam(2, $filename);
                $stmt->bindParam(3, $filePath);
            if ($stmt->execute()) {
                $_SESSION['meldung'] = "The file ". htmlspecialchars($filename) . " has been uploaded.";
        // Insert file info into database
        
        } else {
            $_SESSION['meldung'] = "Sorry, there was an error uploading your file.";
            error_log("Database error: " . implode(", ", $stmt->errorInfo()));
        }
    }
    
    header('Location:'.'../index.php');
    exit;
}
    
    /*
    session_start();

include 'datenbank.inc.php';
// Überprüft ob server unter Windows oder Linux läuft
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $
}

$directory = __DIR__ . '/';
// Upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $filename = $_FILES["file"];
    $target_path = $user_dir . basename($filename["name"]);
    if (move_uploaded_file($_FILES["tmp_name"], $target_path)) {
        $_SESSION['meldung'] = "The file ". htmlspecialchars($filename) . " has been uploaded.";
        header("Location: user_menue.inc.php"); // Siete neustarten
        exit();
    } else {
        $_SESSION['meldung'] = 'Fehler beim Hochladen';
    }
}
    */

?>
