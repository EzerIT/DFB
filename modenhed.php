<?php
require_once('head.inc.php');

makeheadstart('Tekstens modenhed');
makeheadend();
makemenus(3);
?>

    <div class="container">
      <div class="row justify-content-center">

        <div class="col-lg-10 col-xl-9">
          <div class="card mt-4">
            <div class="card-body">
              <h1 class="card-title">Tekstens modenhed</h1>

              <p class="card-text">Den bibeltekst der foreligger her, er opstået ved en process der i grove træk består af disse trin:</p>

              <ol class="card-text">
                <li>For Det Gamle Testamentes vedkommende: En rå »oversættelse« af den autoriserede
                danske tekst fra 1871 til moderne dansk. For Det Nye Testamentes vedkommende: En rå
                oversættelse af den græske grundtekst til dansk.</li>
                <li>Eksegetisk bearbejdelse, hvor hebraisk- og græskkyndige forskere opdaterer den danske tekst
                  med nye resultater fra bibelforskningen.</li>
                <li>Sproglig bearbejdelse af den danske tekst.</li>
                <li>Endelig afpudsning og færdiggørelse.</li>
              </ol>

              <p class="card-text">Ikke alle de foreliggende kapitler har været igennem hele processen. For nogle
                kapitlers vedkommende er der stadig tale om en foreløbig udgave. Ved hvert kapitel
                er det angivet om der er tale en om <i>rå oversættelse,</i> en <i>delvis færdig</i> udgave eller
                en <i>færdig</i> udgave.</p>

              <p class="card-text">Men selv om et tekst er markeret som »færdig«, er der dog ikke tale om en endelig,
                statisk udgave. De enkelte kapitler vil løbende blive revideret.</p>
            </div>
          </div>
        </div>
   
      </div><!--End of row-->
    </div><!--End of container-->
<?php
endbody();
?>
