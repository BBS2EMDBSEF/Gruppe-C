<?php
$sql = 'SELECT * FROM users WHERE email = ? ';
$st = $db->prepare($sql);
$st->execute([$_SESSION['eingeloggt']]);
$daten = $st->fetch();
?>

<article class="profil">
  <h2 class="header"><?= $daten['nachname'] ?></h2>

  <p class="datum">Sie sind angemeldet seit: <?= formatiereDatum($daten['created_at']) ?></p>

  <div class="btn">
     <a href="index.php?page=profil&id=<?= $daten['id'] ?>&del" class="delete">Profil löschen</a> 
  </div>

  <?php 
  if( isset($_GET['del']) && isset($_GET['id'])  && $_GET['id'] === $_SESSION['id']):
  ?>
  <form action="inc/delete.php" method="post" class="weg">
    <fieldset>
      <legend>Wollen Sie wirklich Ihr Profil löschen?</legend>
      <button type="submit" class="abrechen"><a href="index.php?page=neu">Abrechen</a></button>
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['token'] ?>" />
      
      <button type="submit" class="delete">Profil endgültig löschen</button>
    </fieldset>
  </form>
  <?php endif;?>
</article>
