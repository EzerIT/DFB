<?php
require_once('head.inc.php');
require_once('oversigt.inc.php');

makeheadstart('Prædikentekster');
makeheadend();
makemenus(6);

$hebbooks = [
    '1mos' => 'Genesis',
    '2mos' => 'Exodus',
    '3mos' => 'Leviticus',
    '4mos' => 'Numeri',
    '5mos' => 'Deuteronomium',
            //Josua
    'dom' => 'Judices',
            //Samuel_I
            //Samuel_II
            //Reges_I
            //Reges_II
    'es' => 'Jesaia',
    'jer' => 'Jeremia',
            //Ezechiel
            //Hosea
            //Joel
            //Amos
    'obad' => 'Obadia',
            //Jona
            //Micha
    'nah' => 'Nahum',
    'hab' => 'Habakuk',
    'sef' => 'Zephania',
            //Haggai
    'zak' => 'Sacharia',
            //Maleachi
    'sl' => 'Psalmi',
            //Iob
            //Proverbia
    'ruth' => 'Ruth',
            //Canticum
            //Ecclesiastes
            //Threni
            //Esther
            //Daniel
            //Esra
            //Nehemia
            //Chronica_I
            //Chronica_II
];

$grbooks = [
    'matt' => 'Matthew',
    'mark' => 'Mark',
    'luk' => 'Luke',
    'joh' => 'John',
            //Acts
            //Romans
    '1kor' => 'I_Corinthians',
    '2kor' => 'II_Corinthians',
            //Galatians
    'ef' => 'Ephesians',
    'fil' => 'Philippians',
            //Colossians
    '1thess' => 'I_Thessalonians',
            //II_Thessalonians
            //I_Timothy
            //II_Timothy
            //Titus
            //Philemon
            //Hebrews
            //James
    '1pet' => 'I_Peter',
            //II_Peter
    '1joh' => 'I_John',
            //II_John
            //III_John
            //Jude
            //Revelation
];


function ref($book,$chap,$from=0,$to=0,$alt_refname=null) {
    global $abbrev;

    $refname = !is_null($alt_refname) ? $alt_refname :
               ($from==0 ? "$abbrev[$book] $chap" :
               "$abbrev[$book] $chap,$from-$to");
    
    if ($from==0)
        return "<a target=\"_blank\" href=\"show.php?bog=$book&kap=$chap\">$refname</a>";

    return "<a target=\"_blank\" href=\"show.php?bog=$book&kap=$chap&fra=$from&til=$to\">$refname</a>";
}

function refh($book,$chap,$from=0,$to=0) {
    global $hebbooks;
    $hbook = $hebbooks[$book];

    if ($from==0)
        return "<a target=\"_blank\" href=\"https://bibleol.3bmoodle.dk/text/show_text/ETCBC4/$hbook/$chap\">Ⓗ</a>";

    return "<a target=\"_blank\" href=\"https://bibleol.3bmoodle.dk/text/show_text/ETCBC4/$hbook/$chap/$from/$to\">Ⓗ</a>";
}

function refg($book,$chap,$from=0,$to=0) {
    global $grbooks;
    $gbook = $grbooks[$book];

    if ($from==0)
        return "<a target=\"_blank\" href=\"https://bibleol.3bmoodle.dk/text/show_text/nestle1904/$gbook/$chap\">Ⓖ</a>";

    return "<a target=\"_blank\" href=\"https://bibleol.3bmoodle.dk/text/show_text/nestle1904/$gbook/$chap/$from/$to\">Ⓖ</a>";
}

function ref_refh($book,$chap,$from=0,$to=0,$alt_refname=null) {
    return ref($book,$chap,$from,$to,$alt_refname) . ' ' . refh($book,$chap,$from,$to);
}

function ref_refg($book,$chap,$from=0,$to=0,$alt_refname=null) {
    return ref($book,$chap,$from,$to,$alt_refname) . ' ' . refg($book,$chap,$from,$to);
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
                      <td><?= ref_refh('es',55,6,11)?></td>
                      <td><?= ref_refg('1kor',1,18,25,'1 Kor 1,18-21[25]') ?></td>
                      <td><?= ref_refg('mark',4,1,20) ?></td>
                  </tr>
                  <tr>
                      <td>14.2</td><td>Fastelavns søndag</td>
                      <td><?= ref_refh('sl',2) ?></td>
                      <td><?= ref_refg('1pet',3,18,22) ?></td>
                      <td><?= ref_refg('matt',3,13,17) ?></td>
                  </tr>
                  <tr>
                      <td>21.2</td><td>1. søndag i fasten</td>
                      <td><?= ref_refh('1mos',3,1,19) ?></td>
                      <td><?= ref_refg('2kor',6,1,10,'2 Kor 6,1-2[10]') ?></td>
                      <td><?= ref_refg('matt',4,1,11) ?></td>
                  </tr>
                  <tr>
                      <td>28.2</td><td>2. søndag i fasten</td>
                      <td><?= ref_refh('sl',42,2,6) ?></td>
                      <td><?= ref_refg('1thess',4,1,7) ?></td>
                      <td><?= ref_refg('matt',15,21,28) ?></td>
                  </tr>
                  <tr>
                      <td>7.3</td><td>3. søndag i fasten</td>
                      <td><?= ref_refh('5mos',18,9,15) ?></td>
                      <td><?= ref_refg('ef',5,1,9,'Ef 5,[1]6-9') ?></td>
                      <td><?= ref_refg('luk',11,14,28) ?></td>
                  </tr>
                  <tr>
                      <td>14.3</td><td>Midfaste søndag</td>
                      <td><?= ref_refh('5mos',8,1,3) ?></td>
                      <td><?= ref_refg('2kor',9,6,11) ?></td>
                      <td><?= ref_refg('joh',6,1,15) ?></td>
                  </tr>
                  <tr>
                      <td>21.3</td><td>Mariæ bebudelses dag</td>
                      <td><?= ref_refh('es',7,10,14) ?></td>
                      <td><?= ref_refg('1joh',1,1,3) ?></td>
                      <td><?= ref_refg('luk',1,26,38) ?></td>
                  </tr>
                  <tr>
                      <td>28.3</td><td>Palmesøndag</td>
                      <td><?= ref_refh('zak',9,9,10) ?></td>
                      <td><?= ref_refg('fil',2,5,11) ?></td>
                      <td><?= ref_refg('matt',21,1,9) ?></td>
                  </tr>
                  <tr>
                      <td>1.4</td><td>Skærtorsdag</td>
                      <td><?= ref_refh('2mos',12,1,11) ?></td>
                      <td><?= ref_refg('1kor',10,15,17) ?></td>
                      <td><?= ref_refg('matt',26,17,30) ?></td>
                  </tr>
                  <tr>
                      <td>2.4</td><td>Langfredag</td>
                      <td><?= ref_refh('es',52,13,15) ?><br>og <?= ref_refh('es',53) ?></td>
                      <td>&nbsp;</td>
                      <td><?= ref_refg('matt',27,31,56) ?></td>
                  </tr>
                  <tr>
                      <td>4.4</td><td>Påskedag</td>
                      <td><?= ref_refh('sl',118,19,29) ?></td>
                      <td><?= ref_refg('1kor',5,7,8) ?></td>
                      <td><?= ref_refg('mark',16,1,8) ?></td>
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
