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

    <div class="col-lg-10 col-xl-9">
      <div class="card mt-4">
        <div class="card-body">
          <img class="img-fluid float-right d-none d-lg-block" style="width: 300px; margin-top: 0px; margin-left: 10px" src="img/61.jpg" alt="">
          <h1>Kirkeårets prædikentekster</h1>

          <p>På denne side vil der gradvis opstå links til Den Frie Bibels oversættelse af
              kirkeårets læsninger i den danske folkekirke.</p>
          <p>Et klik på Ⓗ eller Ⓖ giver adgang til den hebraiske eller græske grundtekst.</p>

          <p><b>Første tekstrække:</b></p>
          <div class="table-responsive">
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
                      <td><?= ref('1 Pet 3,18-22','1pet',3,18,22) ?> <?= refg('I_Peter',3,18,22) ?></td>
                      <td><?= ref('Matt 3,13-17','matt',3,13,17) ?> <?= refg('Matthew',3,13,17) ?></td>
                  </tr>
                  <tr>
                      <td>21.2</td><td>1. søndag i fasten</td>
                      <td><?= ref('1 Mos 3,1-19','1mos',3,1,19) ?> <?= refh('Genesis',3,1,19) ?></td>
                      <td><?= ref('2 Kor 6,1-2[10]','2kor',6,1,10) ?> <?= refg('II_Corinthians',6,1,10) ?></td>
                      <td><?= ref('Matt 4,1-11','matt',4,1,11) ?> <?= refg('Matthew',4,1,11) ?></td>
                  </tr>
                  <tr>
                      <td>28.2</td><td>2. søndag i fasten</td>
                      <td><?= ref('Sl 42,2-6','sl',42,2,6) ?> <?= refh('Psalmi',42,2,6) ?></td>
                      <td><?= ref('1 Thess 4,1-7','1thess',4,1,7) ?> <?= refg('I_Thessalonians',4,1,7) ?></td>
                      <td><?= ref('Matt 15,21-28','matt',15,21,28) ?> <?= refg('Matthew',15,21,28) ?></td>
                  </tr>
                  <tr>
                      <td>7.3</td><td>3. søndag i fasten</td>
                      <td><?= ref('5 Mos 18,9-15','5mos',18,9,15) ?> <?= refh('Deuteronomium',18,9,15) ?></td>
                      <td><?= ref('Ef 5,[1]6-9','ef',5,1,9) ?> <?= refg('Ephesians',5,1,9) ?></td>
                      <td><?= ref('Luk 11,14-28','luk',11,14,28) ?> <?= refg('Luke',11,14,28) ?></td>
                  </tr>
                  <tr>
                      <td>14.3</td><td>Midfaste søndag</td>
                      <td><?= ref('5 Mos 8,1-3','5mos',8,1,3) ?> <?= refh('Deuteronomium',8,1,3) ?></td>
                      <td><?= ref('2 Kor 9,6-11','2kor',9,6,11) ?> <?= refg('II_Corinthians',9,6,11) ?></td>
                      <td><?= ref('Joh 6,1-15','joh',6,1,15) ?> <?= refg('John',6,1,15) ?></td>
                  </tr>
              </table>
          </div>

        </div>
      </div>
    </div>
 
  </div><!--End of row-->
</div><!--End of container-->

<?php
endbody();
?>
