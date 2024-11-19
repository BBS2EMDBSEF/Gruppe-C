<?php

session_start();

include 'datenbank.inc.php';
require_once 'funktionen.inc.php';

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ini_set('file_uploads', 'On');
ini_set('upload_max_filesize', '5M');
ini_set('post_max_size', '8M');

*/

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $filename = basename($_FILES["file"]["name"]);
    $filedata = file_get_contents($_FILES["file"]["tmp_name"]);
    $fileType = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $uploadOk = 1;

    // Check file size (limit to 5MB)
    if ($_FILES["file"]["size"] > 5000000) {
        $_SESSION['meldung'] = "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    $allowedTypes = array("jpg", "png", "jpeg", "gif", "pdf");
    if (!in_array($fileType, $allowedTypes)) {
        $_SESSION['meldung'] = "Sorry, only JPG, JPEG, PNG, GIF, & PDF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $_SESSION['meldung'] = "Sorry, your file was not uploaded.";
    } else {
            $stmt = $db->prepare("INSERT INTO files (user_id, filename) VALUES (?, ?)");
            $stmt->bindParam(1, $_SESSION['id']);
                $stmt->bindParam(2, $filename);;
            if ($stmt->execute()) {
                $_SESSION['meldung'] = "The file ". htmlspecialchars($filename) . " has been uploaded.";
        // Insert file info into database
        
        } else {
            $_SESSION['meldung'] = "Sorry, there was an error uploading your file.";
            error_log("Database error: " . implode(", ", $stmt->errorInfo()));
        }
    }
    
    redirect('../index.php');
}

?>