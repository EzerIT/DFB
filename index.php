<?php
require_once('head.inc.php');
require_once('oversigt.inc.php');

makeheadstart('Den Frie Bibel');
makeheadend();
makemenus(0);
?>
<div class="container-fluid">
  <div class="row">

    <div class="col-md-9">
      <div class="card mt-4">
        <div class="card-body">
          <img class="img-fluid float-right" style="margin-left: 5px; margin-bottom: 5px;" src="img/Community6-300.jpg" alt="">
    
          <h1>Vel&shy;kom&shy;men til Den Frie Bibel</h1>

          <p>Den Frie Bibel udspringer af ønsket om at skabe en tekstnær dansk bibeloversættelse som
            er fri for enhver form for copyright. Altså en tekst som alle og enhver har lov til at
            kopiere og benytte som de ønsker. Forbilledet har været den
            engelske <a href="http://ebible.org/web/copyright.htm" target="_blank">World English
            Bible</a>.</p>

            <p>Foreløbig er det kun nogle få kapitler der foreligger i mere eller mindre færdiggjort
            grad. Forlægget har for det Gamle Testamentes vedkommende været den danske oversættelse
            fra 1871, som er blevet moderniseret og opdateret med nye resultater fra bibelforskningen.
            Den Nye Testamentes er blevet til ud fra den autoriserede oversættelse fra 1907
            eller direkte fra grundteksten.</p>
             
          <p>Oversættelsen er <i>tekstnær.</i> Det betyder at der er lagt vægt på at den danske
            tekst skal være en så præcis gengivelse af grundteksten som muligt, også på
            steder hvor grundteksten er vanskelig at forstå. Hvor originalen er tvetydig, bør
            oversættelsen også være det. Der er altså ikke tale om en gendigtning af den bibelske
            tekst.</p>

          <h2>Udnyttelse af mediet</h2>

          <p>Moderne computerteknik gør det muligt at lade læseren have betydlig indflydelse på
            hvorledes teksten skal præsenteres. Under menupunktet »Læseoplevelse« kan man fx vælge om
            man ønsker teksten vist med eller uden versnumre, med eller uden fodnoter osv. Her kan man
            også vælge mellem forskellige skrifttyper.</p>


          <h2>Direkte links</h2>

          <p>Det er muligt at linke direkte til et kapitel eller et antal vers i teksten. Følgende
          eksempler viser hvordan en URL konstrueres:</p>

          <p>Link til Dommerbogen kapitel 5:</p>
          <pre>     https://denfriebibel.dk/show.php?bog=dom&kap=5</pre>

          <p>Link til Dommerbogen kapitel 5 vers 8-10:</p>
          <pre>     https://denfriebibel.dk/show.php?bog=dom&kap=5&fra=8&til=10</pre>

          <p>De bogforkortelser der benyttes i disse URL'er, finder man nemmest ved at åbne et bestemt
              kapitel på normal vis og iagttage sidens URL.</p>
          
          <h2>Kommentarer</h2>

          <p>Kommentarer og spørgsmål kan sendes til Claus Tøndering (<span id="cloak6668">This email address is being protected from spambots. You need JavaScript enabled to view it.</span><script type='text/javascript'>
 //<!--
 document.getElementById('cloak6668').innerHTML = '';
 var prefix = '&#109;a' + 'i&#108;' + '&#116;o';
 var path = 'hr' + 'ef' + '=';
 var addy6668 = 'claus' + '&#64;';
 addy6668 = addy6668 + 'ez&#101;r' + '&#46;' + 'd&#107;';
 document.getElementById('cloak6668').innerHTML += '<a ' + path + '\'' + prefix + ':' + addy6668 + '\'>' +addy6668+'<\/a>';
 //-->
 </script>) eller Nicolai Winther-Nielsen (<span id="cloak6663">This email address is being protected from spambots. You need JavaScript enabled to view it.</span><script type='text/javascript'>
 //<!--
 document.getElementById('cloak6663').innerHTML = '';
 var prefix = '&#109;a' + 'i&#108;' + '&#116;o';
 var path = 'hr' + 'ef' + '=';
 var addy6663 = 'nwn' + '&#64;';
 addy6663 = addy6663 + 'db&#105;' + '&#46;' + '&#101;d&#117;';
 document.getElementById('cloak6663').innerHTML += '<a ' + path + '\'' + prefix + ':' + addy6663 + '\'>' +addy6663+'<\/a>';
 //-->
 </script>).</p>

        </div>
      </div>
    </div>
   
    <div class="col-md-3">
      <div class="card mt-4">
        <h1 class="card-header bg-info text-light">Ophavsret</h1>
        <div class="card-body">
          <p>Intet indhold i Den Frie Bibel er belagt med ophavsret. Det betyder at alle har ret
            til at gøre hvad som helst med teksten: Kopiere den, udskrive den, citere den, lægge
            den på andre websider, radiotransmittere den, sælge den, prædike over den, osv.</p>
          <p>Derimod er selve betegnelsen <i>Den Frie Bibel</i> et varemærke, og hvis du ændrer
            teksten, må du ikke benytte betegnelsen <i>Den Frie Bibel</i> om den ændrede
            tekst.</p>
        </div>
      </div>
   
      <div class="card mt-3">
        <h1 class="card-header bg-success text-light">Personer</h1>
        <div class="card-body">
          <p>Under hvert kapitel nævnes de personer der har stået for »oversættelsen« (eller
            modernisering af teksten) og den videnskablige bearbejdning af indholdet.</p>
          <p>Programmering: Claus Tøndering</p>
          
          <p>Bemærk at bibeloversættelsen løbende bliver revideret og opdateret.</p>
        </div>
      </div>
   
      <div class="card mt-3">
        <h1 class="card-header bg-success text-light">Hvor langt er vi?</h1>
        <div class="card-body">
          <?php
            $otcount = 0;
            $ntcount = 0;
            foreach ($title as $key => $ignore) {
                if ($key=='GT')
                    $curcount = &$otcount;
                elseif ($key=='NT')
                    $curcount = &$ntcount;
                else
                    $curcount += count($chap[$key]);
            }
          ?>
          <p>GT: Vi har <?= $otcount ?> kapitler ud af <?= $total_chap_ot ?>.</p>
          <p><progress value="<?= $otcount ?>" max="<?= $total_chap_ot ?>"></progress> <?= round($otcount*100/$total_chap_ot) ?>%</p>
          <p>NT: Vi har <?= $ntcount ?> kapitler ud af <?= $total_chap_nt ?>.</p>
          <p><progress value="<?= $ntcount ?>" max="<?= $total_chap_nt ?>"></progress> <?= round($ntcount*100/$total_chap_nt) ?>%</p>
        </div>
      </div>
    </div>

  </div><!--End of row-->

  <div class="row">
    <div class="col-sm-6">
      <div class="credit mt-3">
        <p>Idé til website-design: <a target="_blank" href="http://startbootstrap.com/">Start Bootstrap</a></p>
        <p>Baggrund: <a target="_blank" href="http://backgroundlabs.com/">Background Labs</a></p>
        <p>Foto: <a target="_blank" href="http://deathtothestockphoto.com/">Death to the Stock Photo</a></p>
      </div>
    </div>
  </div><!--End of row-->

</div><!--End of container-fluid-->


<?php
endbody();
?>
