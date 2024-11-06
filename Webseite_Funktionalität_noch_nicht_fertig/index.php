<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['username'] = $username;
            header("Location: home.php");
        } else {
            echo "Passwort falsch!";
        }
    } else {
        echo "Benutzer nicht gefunden!";
    }
}
?>

<form method="post" action="">
    Benutzername: <input type="text" name="username" required><br>
    Passwort: <input type="password" name="password" required><br>
    <button type="submit">Login</button>
</form>
<p>Noch keinen Account? <a href="register.php">Jetzt registrieren</a></p>
