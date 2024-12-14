<div class="userMenue">
  <ul>
    <li>
      Ihre Konto
    </li>
    <li><a href="inc/logout.php">Logout</a></li>
  </ul>

  <p>Eingeloggt als: <em><?= $_SESSION['eingeloggt_user'] ?></em> </p>

  <form action="inc/upload.php" method="post" enctype="multipart/form-data">
    <label for="file">Datei hochladen:</label>
    <input type="file" name="file" id="file" required>
    <input type="submit" value="Hochladen" name="submit">
  </form>
  
  <h3>Hochgeladene Dateien:</h3>
  <ul>
    <?php
    // Importiere die Funktionalität zur Anzeige der Dateien aus upload.php
    include_once 'inc/upload.php';

    // Überprüfen, ob Dateien existieren und auflisten
    if (!empty($user_files)) {
        foreach ($user_files as $file) {
            echo "<li>";
            echo htmlspecialchars($file); // Sicherheitsmaßnahme gegen XSS
            echo " <button><a href='inc/upload.php?download=" . urlencode($file) . "'>Download</a><button>"; // Download-Link
            echo " <form style='display:inline;' action='inc/upload.php' method='post'>"; // Löschen-Button
            echo "   <input type='hidden' name='delete_file' value='" . htmlspecialchars($file) . "'>";
            echo "   <button type='submit'>Löschen</button>";
            echo " </form>";
            echo "</li>";
        }
    } else {
        echo "<li>Keine Dateien hochgeladen.</li>";
    }
    ?>
  </ul>
</div>
