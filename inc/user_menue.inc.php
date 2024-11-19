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

</div>

