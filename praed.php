<?php
require_once('head.inc.php');
require_once('oversigt.inc.php');
require_once('holiday.inc.php');

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
    'dom'  => 'Judices',
            //Samuel_I
            //Samuel_II
            //Reges_I
            //Reges_II
    'es'   => 'Jesaia',
    'jer'  => 'Jeremia',
    'ez'   => 'Ezechiel',
            //Hosea
            //Joel
            //Amos
    'obad' => 'Obadia',
            //Jona
            //Micha
    'nah'  => 'Nahum',
    'hab'  => 'Habakuk',
    'sef'  => 'Zephania',
    'hagg' => 'Haggai',
    'zak'  => 'Sacharia',
            //Maleachi
    'sl'   => 'Psalmi',
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
    'apg' => 'Acts',
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
    'hebr' => 'Hebrews',
    'jak'  => 'James',
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

$holidays = new Holiday(2021);

function make_holiday($hn) {
    global $holidays;
    
    return "<td>" . $holidays->format_date($holidays->get_holiday_from_number($hn)) . "</td>" .
           "<td>" . $holidays->get_holiday_name_from_number($hn) . "</td>";
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
                      <th>Dato<br><?= $holidays->get_year() ?></th><th>Dag</th><th>1. læsning</th><th>2. læsning</th><th>3. læsning</th>
                  </tr>
                  <tr>
                      <?= make_holiday(9) ?>
                      <td><?= ref_refh('es',55,6,11)?></td>
                      <td><?= ref_refg('1kor',1,18,25,'1 Kor 1,18-21[25]') ?></td>
                      <td><?= ref_refg('mark',4,1,20) ?></td>
                  </tr>
                  <tr>
                      <?= make_holiday(10) ?>
                      <td><?= ref_refh('sl',2) ?></td>
                      <td><?= ref_refg('1pet',3,18,22) ?></td>
                      <td><?= ref_refg('matt',3,13,17) ?></td>
                  </tr>
                  <tr>
                      <?= make_holiday(11) ?>
                      <td><?= ref_refh('1mos',3,1,19) ?></td>
                      <td><?= ref_refg('2kor',6,1,10,'2 Kor 6,1-2[10]') ?></td>
                      <td><?= ref_refg('matt',4,1,11) ?></td>
                  </tr>
                  <tr>
                      <?= make_holiday(12) ?>
                      <td><?= ref_refh('sl',42,2,6) ?></td>
                      <td><?= ref_refg('1thess',4,1,7) ?></td>
                      <td><?= ref_refg('matt',15,21,28) ?></td>
                  </tr>
                  <tr>
                      <?= make_holiday(13) ?>
                      <td><?= ref_refh('5mos',18,9,15) ?></td>
                      <td><?= ref_refg('ef',5,1,9,'Ef 5,[1]6-9') ?></td>
                      <td><?= ref_refg('luk',11,14,28) ?></td>
                  </tr>
                  <tr>
                      <?= make_holiday(14) ?>
                      <td><?= ref_refh('5mos',8,1,3) ?></td>
                      <td><?= ref_refg('2kor',9,6,11) ?></td>
                      <td><?= ref_refg('joh',6,1,15) ?></td>
                  </tr>
                  <tr>
                      <?= make_holiday(15) ?>
                      <td><?= ref_refh('es',7,10,14) ?></td>
                      <td><?= ref_refg('1joh',1,1,3) ?></td>
                      <td><?= ref_refg('luk',1,26,38) ?></td>
                  </tr>
                  <tr>
                      <?= make_holiday(16) ?>
                      <td><?= ref_refh('zak',9,9,10) ?></td>
                      <td><?= ref_refg('fil',2,5,11) ?></td>
                      <td><?= ref_refg('matt',21,1,9) ?></td>
                  </tr>
                  <tr>
                      <?= make_holiday(17) ?>
                      <td><?= ref_refh('2mos',12,1,11) ?></td>
                      <td><?= ref_refg('1kor',10,15,17) ?></td>
                      <td><?= ref_refg('matt',26,17,30) ?></td>
                  </tr>
                  <tr>
                      <?= make_holiday(18) ?>
                      <td><?= ref_refh('es',52,13,15) ?><br>og <?= ref_refh('es',53) ?></td>
                      <td>&nbsp;</td>
                      <td><?= ref_refg('matt',27,31,56) ?></td>
                  </tr>
                  <tr>
                      <?= make_holiday(19) ?>
                      <td><?= ref_refh('sl',118,19,29) ?></td>
                      <td><?= ref_refg('1kor',5,7,8) ?></td>
                      <td><?= ref_refg('mark',16,1,8) ?></td>
                  </tr>
                  <tr>
                      <?= make_holiday(20) ?>
                      <td><?= ref_refh('sl',22,22,32,'Sl 22,22b-32') ?></td>
                      <td><?= ref_refg('apg',10,34,41) ?></td>
                      <td><?= ref_refg('luk',24,13,35) ?></td>
                  </tr>
                  <tr>
                      <?= make_holiday(21) ?>
                      <td><?= ref_refh('sl',30) ?></td>
                      <td><?= ref_refg('1joh',5,1,5) ?></td>
                      <td><?= ref_refg('joh',20,19,31) ?></td>
                  </tr>
                  <tr>
                      <?= make_holiday(22) ?>
                      <td><?= ref_refh('ez',34,11,16) ?></td>
                      <td><?= ref_refg('1pet',2,20,25) ?></td>
                      <td><?= ref_refg('joh',10,11,16) ?></td>
                  </tr>
                  <tr>
                      <?= make_holiday(23) ?>
                      <td><?= ref_refh('es',54,7,10) ?></td>
                      <td><?= ref_refg('hebr',13,12,16) ?></td>
                      <td><?= ref_refg('joh',16,16,22) ?></td>
                  </tr>
                  <tr>
                      <?= make_holiday(24) ?>
                      <td><?= ref_refh('sl',51,3,19) ?><br>eller <?= ref_refh('sl',67) ?></td>
                      <td><?= ref_refg('hebr',8,10,12) ?></td>
                      <td><?= ref_refg('matt',3,1,10) ?></td>
                  </tr>
                  <tr>
                      <?= make_holiday(25) ?>
                      <td><?= ref_refh('ez',36,26,28) ?></td>
                      <td><?= ref_refg('jak',1,17,21) ?></td>
                      <td><?= ref_refg('joh',16,5,15) ?></td>
                  </tr>
                  <tr>
                      <?= make_holiday(26) ?>
                      <td><?= ref_refh('1mos',32,25,32) ?></td>
                      <td><?= ref_refg('jak',1,22,25) ?></td>
                      <td><?= ref_refg('joh',16,23,28,'Joh 16,23b-28') ?></td>
                  </tr>
                  <tr>
                      <?= make_holiday(27) ?>
                      <td><?= ref_refh('sl',110,1,4) ?></td>
                      <td><?= ref_refg('apg',1,1,11) ?></td>
                      <td><?= ref_refg('mark',16,14,20) ?></td>
                  </tr>
                  <tr>
                      <?= make_holiday(28) ?>
                      <td><?= ref_refh('hagg',2,4,9,'Hagg 2,4b-9') ?></td>
                      <td><?= ref_refg('1pet',4,7,11,'1 Pet 4,7b-11') ?></td>
                      <td><?= ref_refg('joh',15,26,27) ?><br>og <?= ref_refg('joh',16,1,4) ?></td>
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
