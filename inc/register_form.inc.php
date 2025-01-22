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

<!--+++++MENUE+++++-->
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
	require_once 'inc/login_form.inc.php';
endif;
?>

<main>


<div class="registerForm">

  <form action="inc/registrierung.php" method="post" class="registrierung" id="register">
  
      <fieldset>
        <legend>Registrierung</legend>
        
          <label for="Nachname">Nachname</label>
          <input type="text" name="nachname" id="nachname" placeholder="Nachname muss min. 2Buchst." required />
        
          <label for="vorname">Vorname</label>
          <input type="text" name="vorname" id="vorname" placeholder="Vorname muss min. 2Buchst." required/>
        
          <label for="username">Benutzername </label>
          <input type="username" name="username" id="username" placeholder="kein leerzeichen und nur Zahlen und kleine Buchstaben"required/>

          <label for="password">Passwort </label>
          <input  type="password" name="passwort" id="passwort" placeholder="Passwort muss min. 6" required/>
        
        <input class="submit" type="submit" value="Konto anlegen">
      </fieldset>
  </form>
</div>

<script>
    document.getElementById('register').addEventListener('submit', function(event) {
      var nachname = document.getElementById('nachname').value;
      var vorname = document.getElementById('vorname').value;
      var username = document.getElementById('username').value;
      var passwort = document.getElementById('passwort').value;
      
      var namePattern = /^[A-Za-z]+$/;
      var username = /[a-z][a-z0-9]*$/;
      var passwortPattern = /^.{6,}$/;
      
      if (!namePattern.test(nachname) || nachname.length < 2) {
        alert('Nachname muss mindestens 2 Buchstaben lang sein und darf nur Buchstaben enthalten.');
        event.preventDefault();
      }
      
      if (!namePattern.test(vorname) || vorname.length < 2) {
        alert('Vorname muss mindestens 2 Buchstaben lang sein und darf nur Buchstaben enthalten.');
        event.preventDefault();
      }

      if (!usernamePattern.test(username)) {
        alert('Username darf nur am Anfang Buchstaben enthalten und dann kann auch Zahlen kommen. Ohne Leerzeichen.');
        event.preventDefault();
      }
      
      if (!passwortPattern.test(passwort)) {
        alert('Passwort muss mindestens 6 Zeichen lang sein.');
        event.preventDefault();
      }
    });
  </script>

</main>

</div> <!--ende container-->
<!--++++++++++--> 
<footer>
<p>BBS2</p>
</footer>

</div><!--ende container-->
</body>
</html>
