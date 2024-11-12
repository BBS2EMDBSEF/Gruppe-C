<div class="registerForm">

  <form action="inc/registrierung.php" method="post" class="registrierung" id="register">
  
      <fieldset>
        <legend>Registrierung</legend>
        
          <label for="Nachname">Nachname</label>
          <input type="text" name="nachname" id="nachname" placeholder="Nachname muss min. 2Buchst." />
        
          <label for="vorname">Vorname</label>
          <input type="text" name="vorname" id="vorname" placeholder="Vorname muss min. 2Buchst." />
        
          <label for="email">Email </label>
          <input type="email" name="mail" id="mail" placeholder="xxxx@xxxx.xx" />

          <label for="password">Passwort </label>
          <input  type="password" name="passwort" id="passwort" placeholder="Passwort muss min. 6" />
        
        <input class="submit" type="submit" value="Konto anlegen">
      </fieldset>
  </form>
</div>

