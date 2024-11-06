<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Passwort sicher hashen

    // E-Mail und Benutzername prüfen
    $check = "SELECT * FROM users WHERE email = '$email' OR username = '$username'";
    $result = $conn->query($check);

    if ($result->num_rows > 0) {
        echo "E-Mail oder Benutzername ist bereits registriert.";
    } else {
        $sql = "INSERT INTO users (email, username, password) VALUES ('$email', '$username', '$password')";
        if ($conn->query($sql) === TRUE) {
            echo "Registrierung erfolgreich! <a href='index.php'>Hier einloggen</a>";
        } else {
            echo "Fehler bei der Registrierung: " . $conn->error;
        }
    }
}
?>

<form method="post" action="">
    E-Mail: <input type="email" name="email" required><br>
    Benutzername: <input type="text" name="username" required><br>
    Passwort: <input type="password" name="password" required><br>
    <button type="submit">Registrieren</button>
</form>
<a href="index.php">Zurück zum Login</a>
