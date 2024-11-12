<!DOCTYPE html>
<html lang="de">
<head>
	<meta charset="utf-8" />
	<title>PHP-Projekt</title>
	<script src="jquery/jquery-3.6.0.js"></script>
	<script src="jquery/jquery-ui.min.js"></script>
	<script src="js/index.js"></script>
 
	<style>
	<?php
		include 'css/master.css';
		include 'css/fonts.css';
		include 'css/media.css';
	?>
	</style>
</head>
<body>
<div id="container">
<header>
<a class="logo" href="index.php?page=home"><img src="img/logo.jpg" alt="" width="150" height="80"></a>
</header>

<!--++++++++++-->
<div class="container">

<nav title="Menü">
  <a href="#open" class="open" id="open">open</a>
  <a href="#" class="close">close</a>

  <ul class="resp-menu">

	  <li class="<?= empty($page) ? 'active' : ''; ?>">
		  <a href="index.php" title="Home">HOME</a>
	  </li>

		<li id="liRegister" class="<?php echo ($page === 'registrierung') ? 'active' : ''; ?>">
		      <a href="index.php?page=registrierung">REGISTRIERUNG</a>
	  </li>

  </ul>
</nav>

<?php if(isset($meldung)): ?>
	<p class="msg"> <?= $meldung ?> </p>
<?php endif;?>

<?php
//wenn ein user angemeldet ist dann das zeigen
if(isset($_SESSION['eingeloggt'])):
	require_once 'inc/user_menue.inc.php';
else:
	require_once 'inc/login_form.inc.php';
endif;
?>



<main>

