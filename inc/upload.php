<?php

session_start();

include 'datenbank.inc.php';
require_once 'funktionen.inc.php';
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
    
    redirect('../index.php');
}

?>
