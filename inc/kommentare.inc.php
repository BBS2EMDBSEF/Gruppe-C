<h1 class="h1Kommentare">Ihre Kommentar</h1>
<form method="post" action="inc/kommentare_insert.php" class="registrierung" id="kommentare">
  <fieldset>
    <legend>Pflichtfelder</legend>
    <textarea name="comment" placeholder="Kommentar schreiben" required></textarea>
    <input type="hidden" name="userid" value="<?= $_SESSION['id']?>" />
  </fieldset>

<?php if(isset($_SESSION['eingeloggt'])): ?>
    <input type="submit" value="Kommentar schicken" id="kommentarSchicken"/>
<?php else: ?>
  <h2>Liebe Patientin/Lieber Patient, Nach der Anmeldung, können Sie sich ins Kommentare verewigen</h2>
<?php endif;?>
</form>
<!-- *********************************************************************** -->
<article class="patientenKommentaren">
  <h2>Patienten Kommentare</h2>
  <?php

  $sql = 'SELECT *, kommentare.created_at AS datum FROM kommentare INNER JOIN users 
                ON
                kommentare.user_id = users.id
                ORDER BY kommentare.created_at DESC';

$st = $db->query($sql);
$daten = $st->fetchAll();
$count = $st->rowCount();

if($count < 1){
  echo 'Keine Kommentaren vorhanden';
} else{
  echo '<p>'.$count.' Kommentare</p>';
}

$pro_seite = 5;
if($count % $pro_seite === 0){
  $max = $count / $pro_seite;
}else {
  $max = $count / $pro_seite + 1;
}
//------------------
$seite = $_GET['seite'] ?? '';

if( !isset($seite) || !is_numeric($seite) ){
  $seite = 1;
}
//-------------
if($seite > $max || $seite < 1){
  $seite = 1;
}
//-----------
$start = $seite * $pro_seite - $pro_seite;
//-----------
$sql2 = "SELECT * FROM kommentare INNER JOIN users
            ON
            kommentare.user_id = users.id
            ORDER BY kommentare.created_at DESC
            LIMIT $start, $pro_seite ";

$st2 = $db->query($sql2);
$daten2 = $st2->fetchAll();
foreach($daten2 AS $values):  
?>
<section class="kommentaren">
  <p class="datum">Geschrieben von: <?= bereinige($values['nachname'])?></p>
  <p class="ueber"><?= nl2br(bereinige($values['post']))?></p>
</section>

<?php endforeach?>

<div class="page">
   <?php
    for($i = 1; $i <= $max; $i++){
      if($seite == $i){
       echo "<span id='current'>".$i."</span>";
      }else{
      ?>
      <a href="index.php?page=kommentare&seite=<?= $i?>">
        <?= (int)$i?>
      </a>
      <?php
         }
    }
   ?>
  
  </div>
  


