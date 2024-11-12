<div class="userMenue">
<ul>
	<li class="<?php echo ($page === 'profil') ? 'active' : ''; ?>">
		<a href="index.php?page=profil">Ihre Konto</a>
	</li>
	<li><a href="inc/logout.php">Logout</a></li>
</ul>

<p>Eingeloggt als: <em><?= $_SESSION['eingeloggt_user']?></em> </p>
</div>