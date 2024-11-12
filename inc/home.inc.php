<div class="home">
  <div class="foto"><img src="img/zahnarztpraxis.jpg" alt="" width="1025"></div>
   
  <div class="text">			
     <h1>Herzlich Willkommen</h1>
     <p>Hier auf unserer Website können Sie einen ersten Eindruck von unserer Praxis gewinnen, unser Team kennenlernen und sich über unser Leistungsspektrum informieren.
     <br> Zahnmedizin ist für uns nicht nur ein Beruf. Es ist eine Leidenschaft, mit dem Ziel das Beste für unsere Patienten zu tun. Und wir sind froh, dass wir Mitarbeiterinnen haben, die sich engagieren und voll hinter der Praxis stehen.</p>

      <?php
      function datum() {
      ?>
            <span>Heute: 
      <?php
      return strftime('%d.%m.%Y', strtotime('2022-03-04 11:25:33'));
      }

      echo datum();
     ?>
            </span>
     
  </div>
</div>




