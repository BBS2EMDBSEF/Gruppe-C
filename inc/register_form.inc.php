<div class="registerForm">

  <form action="inc/registrierung.php" method="post" class="registrierung" id="register">
  
      <fieldset>
        <legend>Registrierung</legend>
        
          <label for="Nachname">Nachname</label>
          <input type="text" name="nachname" id="nachname" placeholder="Nachname muss min. 2Buchst." required />
        
          <label for="vorname">Vorname</label>
          <input type="text" name="vorname" id="vorname" placeholder="Vorname muss min. 2Buchst." required/>
        
          <label for="email">Email </label>
          <input type="email" name="mail" id="mail" placeholder="xxxx@xxxx.xx" required/>

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
      var passwort = document.getElementById('passwort').value;
      
      var namePattern = /^[A-Za-z]+$/;
      var passwortPattern = /^.{6,}$/;
      
      if (!namePattern.test(nachname) || nachname.length < 2) {
        alert('Nachname muss mindestens 2 Buchstaben lang sein und darf nur Buchstaben enthalten.');
        event.preventDefault();
      }
      
      if (!namePattern.test(vorname) || vorname.length < 2) {
        alert('Vorname muss mindestens 2 Buchstaben lang sein und darf nur Buchstaben enthalten.');
        event.preventDefault();
      }
      
      if (!passwortPattern.test(passwort)) {
        alert('Passwort muss mindestens 6 Zeichen lang sein.');
        event.preventDefault();
      }
    });
  </script>
