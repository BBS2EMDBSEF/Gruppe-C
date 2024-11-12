<div class="registerForm">

  <form action="inc/bearbeiten.php" method="post" class="registrierung" id="register">
  
      <fieldset>
        <legend>Bearbeiten</legend>

          <label for="geschlecht">Geschlecht</label>
          <select name="geschlecht" id="geschlecht">
            <option value="0">Bitte auswählen</option>
            <option value="frau">Frau</option>
            <option value="herr">Herr</option>
            <option value="divers">Divers</option>
            </select><br>

          <label for="Nachname">Nachname</label>
          <input type="text" name="nachname" id="nachname" placeholder="Nachname muss min. 2Buchst." />
        
          <label for="vorname">Vorname</label>
          <input type="text" name="vorname" id="vorname" placeholder="Vorname muss min. 2Buchst." />

          <label for="geburtstag">Geburtstag</label>
          <input  type="date" name="geburtstag" id="geburtstag" placeholder="Geburtstag eingeben" />

          <label for="telefon">Telefon Nr.</label>
          <input  type="text" name="telefon" id="telefon" placeholder="00/+ 49 41 xxxxxx" />

          <label for="warenByUns">waren Sie schon mal bei uns?</label>
          <p id="warenByUns">
          <input type="radio" name="janein" value="Ja" class="janein" />Ja
          <input type="radio" name="janein" value="Nein" class="janein" />Nein
          </p> 

          <label for="wunschtag">welche Tage pass das bei Ihnen zu uns kommen?</label>
          <p id="wunschtag">
			      <input type="checkbox" name="wunschtag" value="Montag" /> Montag
			      <input type="checkbox" name="wunschtag" value="Dienstag" /> Dienstag
			      <input type="checkbox" name="wunschtag" value="Mittwoch" /> Mittwoch <br>
			      <input type="checkbox" name="wunschtag" value="Donnerstag" />	Donnerstag
			      <input type="checkbox" name="wunschtag" value="Freitag" />	Freitag
	      	</p>
        
          <label for="email">Email </label>
          <input type="email" name="mail" id="mail" placeholder="xxxx@xxxx.xx" />

          <label for="password">Passwort </label>
          <input  type="password" name="passwort" id="passwort" placeholder="Passwort muss min. 6" />
        
        <input class="submit" type="submit" value="Konto anlegen">
      </fieldset>
  </form>
</div>

