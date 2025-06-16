<?php
require_once('head.inc.php');
require_once('oversigt.inc.php');

makeheadstart('Den Frie Bibel');
makeheadend();
makemenus(0);


$otcount = 0;
$ntcount = 0;
foreach ($title as $key => $ignore) {
    if ($key=='GT')
        $curcount = &$otcount;
    elseif ($key=='NT')
        $curcount = &$ntcount;
    else {
        if (empty($chap[$key]))
            continue;
        
        $c = 0;
        if ($style[$key]=='btn-secondary')
            continue;
        if (is_array($style[$key])) {
            foreach ($chap[$key] as $ch)
                if ($style[$key][$ch]!='btn-secondary')
                    ++$curcount;
        }
        else
            $curcount += count($chap[$key]);
    }
}

$find_bibleref = <<<'END'
    <h1 class="card-header bg-success text-light">Find bibelsted</h1>
    <div class="card-body">
        <form id="bible-find-form%1$d">
            <label for="bibleref%1$d">Indtast et bibelsted som du gerne finde:</label>
            <div class="input-group mb-3">
                <input type="text" class="form-control" id="bibleref%1$d" placeholder="Fx: 2 Mos 3,5-6">
                <div class="input-group-append">
                    <input class="btn btn-outline-secondary" type="submit" value="Find" id="bible-find%1$d">
                </div>
            </div>
        </form>
        <p>En liste over forkortelser findes <a href="#" onclick="$('#bibleBooksModal').modal()">her</a>.</p>
    </div>

END;

?>

<script>
    $(function() {
        $('#bible-find-form1').on('submit', function() {
            window.location.href="lookup.php?find="+encodeURIComponent($('#bibleref1').val());
            return false;
        });
        $('#bible-find-form2').on('submit', function() {
            window.location.href="lookup.php?find="+encodeURIComponent($('#bibleref2').val());
            return false;
        });
    });
</script>

<div class="container-fluid">
  <div class="row">

      <div class="col-lg-9">
        <div class="d-block d-lg-none">
          <div class="card mt-4">
              <?php printf($find_bibleref,1); ?>
          </div>
        </div>

        <div class="card mt-4">
        <div class="card-body">
          <img class="img-fluid float-right" style="margin-left: 5px; margin-bottom: 5px;" src="img/pexels-tima-miroshnichenko-5199809-mod.jpg" alt="">
    
          <h1>Vel&shy;kom&shy;men til Den Frie Bibel</h1>

          <p>Den Frie Bibel udspringer af ønsket om at skabe en tekstnær dansk bibeloversættelse som
              er fri for enhver form for copyright. Altså en tekst som alle og enhver har lov til at
              kopiere og benytte som de ønsker. Forbilledet har været den
              engelske <a href="https://ebible.org/web/copyright.htm" target="_blank">World English
              Bible</a>.</p>

          <p>Oversættelsen er <i>tekstnær.</i> Det betyder at der er lagt vægt på at den danske
              tekst skal være en så præcis gengivelse af grundteksten som muligt, også på
              steder hvor grundteksten er vanskelig at forstå. Hvor originalen er tvetydig, bør
              oversættelsen også være det. Der er altså ikke tale om en gendigtning af den bibelske
              tekst.</p>

          <h2>Fremgangsmåde</h2>

          <p>De enkelte bøger er oversat af forskellige personer. Nogle har valgt at gå ud fra den 
              danske oversættelse fra 1871 af Det Gamle Testamente og har moderniseret sproget i den.
              Teksten er derefter blevet revideret på baggrund af resultater fra nyere
              bibelforskning.</p>

          <p>Andre har valgt at oversætte teksten direkte fra den hebraiske eller græske grundtekst,
              hvorefter sproget er blevet revideret for at tilstræbe et letlæst, nutidigt dansk.</p>

          <p>Det kan ikke undgås at de enkelte oversættere har sat deres personlige præg på den
              endelige tekst. En oversætter er kun ansvarlig for tekster han eller hun selv har
              oversat.</p>

          <p>Oversættelsen er en igangværende proces. Teksterne bliver løbende gennemlæst og
              revideret. Teksten er med andre ord ikke statisk og uforanderlig.</p>
              
          <h2>Omfang</h2>

          <p>Foreløbig foreligger kun
              <?=round(($otcount+$ntcount)*100/($total_chap_ot+$total_chap_nt)) ?>% af Bibelens
              kapitler, men håbet er naturligvis at vi med tiden får en komplet bibeloversættelse.</p>
             
          <h2>Udnyttelse af mediet</h2>

          <p>Moderne computerteknik gør det muligt at lade læseren have betydelig indflydelse på
            hvorledes teksten skal præsenteres. Under menupunktet »Læseoplevelse« kan man fx vælge om
            man ønsker teksten vist med eller uden versnumre, med eller uden fodnoter osv. Her kan man
            også vælge mellem forskellige skrifttyper.</p>

          <h2>Tegn og noter</h2>
          <p>Der skelnes mellem to former for fodnoter: Generelle fodnoter (markeret med et tal)
              giver oplysninger som den almindelige bibellæser kan have nytte af. Faglige fodnoter
              (markeret med et bogstav) giver oplysninger som den sprogligt og teologisk kyndige læser
              kan have nytte af. Under menupunktet »Læseoplevelse« kan man vælge hvilke noter
              man ønsker at se.</p>
                
          <p>I teksten markeres visse ord med tegnet °. Det betyder at det pågældende ord har en
              nærmere forklaring, som kan findes ved at klikke på den lille cirkel.</p>
          
          <p>Visse ord er markeret med tegnet *. Det betyder at der til det pågældende
              sted er knyttet henvisninger til andre steder i Bibelen. Disse henvisninger kan findes ved
              at klikke på den lille stjerne.</p>
                
          <p>Cirklen og stjernen vises ikke hvis man fravælger de forklarende fodnoter.</p>

          <h2>Syntaktisk layout</h2>

          <p>Normalt vises en tekst opstillet som almindelig prosa eller poesi. Man kan imidlertid
              vælge at få vist teksten med <i>syntaktisk layout</i>. Det betyder at tekstens
              elementer vises med forskellige grader af indrykning, som illustrerer den indbyrdes relation
              mellem tekstens bestanddele. Nogle læsere finder at dette giver en bedre forståelse af
              tekstens sammenhæng.</p>

          <p>Syntaktisk layout er kun tilgængelig i det Gamle Testamente og kun i visse kapitler.</p>

          <p>Syntaktisk layout kan slås til og fra under menupunktet »Læseoplevelse«.</p>
          

          <h2>Direkte links</h2>

          <p>Det er muligt at linke direkte til et kapitel eller et antal vers i teksten. Følgende
          eksempler viser hvordan en URL konstrueres:</p>

          <p>Link til Dommerbogen kapitel 5:</p>
          <pre>     https://denfriebibel.dk/show.php?bog=dom&kap=5</pre>

          <p>Link til Dommerbogen kapitel 5 vers 8-10:</p>
          <pre>     https://denfriebibel.dk/show.php?bog=dom&kap=5&fra=8&til=10</pre>

          <p>De bogforkortelser der benyttes i disse URL'er, er ikke de sædvanlige forkortelser
              for Bibelens bøger. De korrekte forkortelser finder man nemmest ved at åbne et bestemt
              kapitel på normal vis og iagttage sidens URL.</p>

          <h2>Links til hebraisk og græsk tekst</h2>

          <p>I overskriften for de enkelte kapitler står enten et Ⓗ eller et Ⓖ. Dette er et link til
              den tilsvarende hebraiske/aramæiske eller græske tekst.</p>
          
          <h2>Kommentarer</h2>

          <p>Kommentarer og spørgsmål til selve websiden kan sendes til Claus Tøndering (<span id="cloak6668">This email address is being protected from spambots. You need JavaScript enabled to view it.</span><script>
 //<!--
 document.getElementById('cloak6668').innerHTML = '';
 var prefix = '&#109;a' + 'i&#108;' + '&#116;o';
 var path = 'hr' + 'ef' + '=';
 var addy6668 = 'claus' + '&#64;';
 addy6668 = addy6668 + 'tond&#101;r' + 'ing&#46;' + 'd&#107;';
 document.getElementById('cloak6668').innerHTML += '<a ' + path + '\'' + prefix + ':' + addy6668 + '\'>' +addy6668+'<\/a>';
 //-->
          </script>).</p>

          <p> Kommentarer og spørgsmål til de enkelte kapitler kan sendes til den eller de personer
 der er ansvarlige for den pågældende oversættelse.</p>

        </div>
      </div>
    </div>
    
    <div class="col-lg-3">
        <div class="card mt-4 d-none d-lg-block">
            <?php printf($find_bibleref,2); ?>
        </div>

        <div class="card mt-4">
            <h1 class="card-header bg-info text-light">Ophavsret</h1>
            <div class="card-body">
                <p>Intet <i>dansk</i> indhold i Den Frie Bibel er belagt med ophavsret. Det betyder at alle har ret
                    til at gøre hvad som helst med teksten: Kopiere den, udskrive den, citere den, lægge
                    den på andre websider, radiotransmittere den, sælge den, prædike over den, osv.</p>
                <p>Derimod er selve betegnelsen <i>Den Frie Bibel</i> et varemærke, og hvis du ændrer
                    teksten, må du ikke benytte betegnelsen <i>Den Frie Bibel</i> om den ændrede
                    tekst.</p>
                <p>Den <i>hebraiske eller aramæiske</i> tekst som kan vises sammen med visse kapitler, er underlagt denne ophavsret:
                    © 2015 Eep Talstra Center for Bible and Computer, licenseret under en
                    <a target="_blank" rel="license" href="http://creativecommons.org/licenses/by-nc/4.0/deed.da">Creative Commons Navngivelse-IkkeKommerciel 4.0 International Licens</a>.
                    (Selve tekstdatabasen kan findes via denne bestandige identifikator: <a href="http://www.persistent-identifier.nl/?identifier=urn:nbn:nl:ui:13-048i-71" target="_blank">urn:nbn:nl:ui:13-048i-71</a>.)</p>
            </div>
        </div>
   
      <div class="card mt-3">
        <h1 class="card-header bg-success text-light">Personer</h1>
        <div class="card-body">
          <p>Under hvert kapitel nævnes de personer der har stået for det pågældende kapitel.</p>

          <p>Programmering: Claus Tøndering</p>
        </div>
      </div>
   
      <div class="card mt-3">
        <h1 class="card-header bg-success text-light">Hvor langt er vi?</h1>
        <div class="card-body">
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
        <p>Fotografier: <a target="_blank" href="https://www.pexels.com/da-dk/">Pexels</a></p>
        <p>Idé til website-design: <a target="_blank" href="https://startbootstrap.com/">Start Bootstrap</a></p>
        <p>Baggrund: <a target="_blank" href="https://backgroundlabs.com/">Background Labs</a></p>
      </div>
    </div>
  </div><!--End of row-->

</div><!--End of container-fluid-->

<?php

require_once('booklist.inc.php');

endbody();
?>
