<div class="userMenue">
	<ul>
		<li>
		Ihre Konto
		</li>
		<li><a href="inc/logout.php">Logout</a></li>
	</ul>

	<p>Eingeloggt als: <em><?= $_SESSION['eingeloggt_user']?></em> </p>

	<form action="inc/upload.php" method="post" enctype="multipart/form-data">
		<label for="file">Datei hochladen:</label>
		<input type="file" name="file" id="file">
		<input type="submit" value="Hochladen" name="submit">
	</form>
	<h3>Hochgeladene Dateien:</h3>
  	<ul>
    		<?php
    		require 'inc/datenbank.inc.php'; // Datei mit der Datenbankverbindung
    		$stmt = $db->prepare("SELECT * FROM files WHERE user_id = ?");
    		$stmt->execute([$_SESSION['id']]);
    		$files = $stmt->fetchAll();

    		foreach ($files as $file) {
      			echo '<li><a href="inc/download.php?file_id/' . htmlspecialchars($file['id']) . '">' . 
        		htmlspecialchars($file['filename']) . '</a></li>';
    		}
    		?>
  	</ul>

</div>

