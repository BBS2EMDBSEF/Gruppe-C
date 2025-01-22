<!DOCTYPE html>
<html lang="de">
<head>
	<meta charset="utf-8" />
	<title>BBS 2 Emden - Verwaltung</title>
	<style>
	<?php
		include 'css/master.css';
		include 'css/media.css';
	?>
	</style>
</head>
<body>
<div id="container">
<header>
<a class="logo" href="index.php?page=home"><img src="img/logo.png" alt="" width="200px" height="185px"></a>
</header>

<!--++++++MENUE++++-->
<div class="container">

<nav title="Menü">
  <a href="#open" class="open" id="open">open</a>
  <a href="#" class="close">close</a>
</nav>

<?php if(isset($meldung)): ?>
	<p class="msg"> <?= $meldung ?> </p>
<?php endif;

//wenn ein user angemeldet ist dann das zeigen
if(isset($_SESSION['eingeloggt'])):
	require_once 'inc/user_menue.inc.php';
  $_SESSION['meldung'] = 'Sie sind eingeloggt';
endif;
?>

<main>

<div class="home">
  <?php 
  if(!isset($_SESSION['eingeloggt'])) {?>
  <div class="loginForm">
    <form action="inc/login_check.php" method="post">
      <input type="text" name="username" placeholder="Benutzername eingeben" />
      <input type="password" name="pwd" placeholder="Passwort" />
      <input class="subLoggin" type="submit" value="Einloggen" />
    </form>

  <p> <a href="index.php?page=registrierung">Registrierung</a> </p>
  </div>
  <?php
    }
    ?>

  <div class="text">			
     <h1>Herzlich Willkommen</h1>
  </div>
  
</div>

</main>

</div> <!--ende container-->
<!--++++++++++--> 
<footer>
<p>BBS2</p>
</footer>

</div><!--ende container-->
</body>
</html>



