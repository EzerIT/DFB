<?php
require_once('head.inc.php');

makeheadstart('Prædikentekster');
makeheadend();
makemenus(6);

function ref($refname,$book,$chap,$from=0,$to=0) {
    if ($from==0)
        return "<a target=\"_blank\" href=\"show.php?bog=$book&kap=$chap\">$refname</a>";

    return "<a target=\"_blank\" href=\"show.php?bog=$book&kap=$chap&fra=$from&til=$to\">$refname</a>";
}

function refh($book,$chap,$from=0,$to=0) {
    if ($from==0)
        return "<a target=\"_blank\" href=\"https://bibleol.3bmoodle.dk/text/show_text/ETCBC4/$book/$chap\">Ⓗ</a>";

    return "<a target=\"_blank\" href=\"https://bibleol.3bmoodle.dk/text/show_text/ETCBC4/$book/$chap/$from/$to\">Ⓗ</a>";
}

function refg($book,$chap,$from=0,$to=0) {
    if ($from==0)
        return "<a target=\"_blank\" href=\"https://bibleol.3bmoodle.dk/text/show_text/nestle1904/$book/$chap\">Ⓖ</a>";

    return "<a target=\"_blank\" href=\"https://bibleol.3bmoodle.dk/text/show_text/nestle1904/$book/$chap/$from/$to\">Ⓖ</a>";
}
?>


<div class="container">
    <div class="row justify-content-center">

    <div class="col-lg-10 col-xl-8">
      <div class="card mt-4">
        <div class="card-body">
          <img class="img-fluid float-right d-none d-lg-block" style="width: 300px; margin-top: 0px; margin-left: 10px" src="img/61.jpg" alt="">
          <h1>Kirkeårets prædikentekster</h1>

          <p>På denne side vil der gradvis opstå links til Den Frie Bibels oversættelse af
              kirkeårets prædikentekster i den danske folkekirke.</p>
          <p>Et klik på Ⓗ eller Ⓖ giver adgang til den hebraiske eller græske grundtekst.</p>

          <table class="table table-striped">
              <tr>
                  <th>Dato<br>2021</th><th>Dag</th><th>1. læsning</th><th>2. læsning</th><th>3. læsning</th>
              </tr>
              <tr>
                  <td>7.2</td><td>Søndag seksagesima</td>
                  <td><?= ref('Es 55,6-11','es',55,6,11) ?> <?= refh('Jesaia',55,6,11) ?></td>
                  <td><?= ref('1 Kor 1,18-21[25]','1kor',1,18,25) ?> <?= refg('I_Corinthians',1,18,25) ?></td>
                  <td><?= ref('Mark 4,1-20','mark',4,1,20) ?> <?= refg('Mark',4,1,20) ?></td>
              </tr>
              <tr>
                  <td>14.2</td><td>Fastelavns søndag</td>
                  <td><?= ref('Sl 2','sl',2) ?> <?= refh('Psalmi',2) ?></td>
                  <td>&nbsp;</td><td>&nbsp;</td>
              </tr>
          </table>

        </div>
      </div>
    </div>
 
  </div><!--End of row-->
</div><!--End of container-->

<?php
endbody();
?>
