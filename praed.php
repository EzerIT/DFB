<?php
require_once('head.inc.php');
require_once('oversigt.inc.php');
require_once('holiday.inc.php');

makeheadstart('Prædikentekster');
makeheadend();
makemenus(6);

$hebbooks = [
    '1mos'   => 'Genesis',
    '2mos'   => 'Exodus',
    '3mos'   => 'Leviticus',
    '4mos'   => 'Numeri',
    '5mos'   => 'Deuteronomium',
    'jos'    => 'Josua',
    'dom'    => 'Judices',
    '1sam'   => 'Samuel_I',
    '2sam'   => 'Samuel_II',
    '1kong'  => 'Reges_I',
    '2kong'  => 'Reges_II',
    'es'     => 'Jesaia',
    'jer'    => 'Jeremia',
    'ez'     => 'Ezechiel',
    'hos'    => 'Hosea',
    'joel'   => 'Joel',
    'am'     => 'Amos',
    'obad'   => 'Obadia',
    'jon'    => 'Jona',
    'mika'   => 'Micha',
    'nah'    => 'Nahum',
    'hab'    => 'Habakuk',
    'sef'    => 'Zephania',
    'hagg'   => 'Haggai',
    'zak'    => 'Sacharia',
    'mal'    => 'Maleachi',
    'sl'     => 'Psalmi',
    'job'    => 'Iob',
    'ordsp'  => 'Proverbia',
    'ruth'   => 'Ruth',
    'højs'   => 'Canticum',
    'præd'   => 'Ecclesiastes',
    'klages' => 'Threni',
    'est'    => 'Esther',
    'dan'    => 'Daniel',
    'ezra'   => 'Esra',
    'neh'    => 'Nehemia',
    '1krøn'  => 'Chronica_I',
    '2krøn'  => 'Chronica_II'
];

$grbooks = [
    'matt'   => 'Matthew',
    'mark'   => 'Mark',
    'luk'    => 'Luke',
    'joh'    => 'John',
    'apg'    => 'Acts',
    'rom'    => 'Romans',
    '1kor'   => 'I_Corinthians',
    '2kor'   => 'II_Corinthians',
    'gal'    => 'Galatians',
    'ef'     => 'Ephesians',
    'fil'    => 'Philippians',
    'kol'    => 'Colossians',
    '1thess' => 'I_Thessalonians',
    '2thess' => 'II_Thessalonians',
    '1tim'   => 'I_Timothy',
    '1tim'   => 'II_Timothy',
    'tit'    => 'Titus',
    'filem'  => 'Philemon',
    'hebr'   => 'Hebrews',
    'jak'    => 'James',
    '1pet'   => 'I_Peter',
    '2pet'   => 'II_Peter',
    '1joh'   => 'I_John',
    '2joh'   => 'II_John',
    '3joh'   => 'III_John',
    'jud'    => 'Jude',
    'åb'     => 'Revelation'
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

function make_holiday($holidays,$hn) {
    $date = $holidays->get_holiday_from_number($hn);

    return "<td>" . Holiday::get_holiday_name_from_number($hn) . "</td>"
         . "<td class=\"text-center\">" . (is_null($date) || $holidays->holiday_is_all_saints($hn) ? "⛔" : $holidays->format_date($holidays->get_holiday_from_number($hn))) . "</td>";
}

function make_all_saints_holiday($holidays) {
    return "<td>" . Holiday::get_holiday_name_from_number(99) . "</td>"
         . "<td class=\"text-center\">" . $holidays->format_date($holidays->get_all_saints_sunday()) . "</td>";
}



$lessons1 = [
    // Nytårsdag
    0 => [ ref_refh("1mos",12,1,3),
           ref_refg("gal",3,23,29),
           ref_refg("luk",2,21,21,"Luk 2,21") ],

    // Helligtrekongers søndag
    1 => [ ref_refh("es",60,1,6),
           ref_refg("tit",3,4,7),
           ref_refg("matt",2,1,12) ],

    // 1. søndag efter helligtrekonger
    2 => [ ref_refh("sl",84),
           ref_refg("rom",12,1,5),
           ref_refg("luk",2,41,52) ],

    // 2. søndag efter helligtrekonger
    3 => [ ref_refh("2mos",33,18,23),
           ref_refg("rom",12,6,16,"Rom 12,[6]9-16a"),
           ref_refg("joh",2,1,11) ],

    // 3. søndag efter helligtrekonger
    4 => [ ref_refh("5mos",10,17,21),
           ref_refg("rom",12,16,21,"Rom 12,16b-21"),
           ref_refg("matt",8,1,13) ],

    // 4. søndag efter helligtrekonger
    5 => [ ref_refh("job",38,1,18,"Job 38,1-11[18]") ."<br>og evt. ". ref_refh("job",38,31,33,"[Job 38,31-33]"),
           ref_refg("rom",13,8,10),
           ref_refg("matt",8,23,27) ],

    // 5. søndag efter helligtrekonger
    6 => [ ref_refh("præd",8,9,15),
           ref_refg("kol",3,12,17),
           ref_refg("matt",13,24,30) ."<br>eller ". ref_refg("matt",13,44,52) ],

    // Sidste søndag efter helligtrekonger
    7 => [ ref_refh("2mos",34,27,35),
           ref_refg("2pet",1,16,18),
           ref_refg("matt",17,1,9) ],

    // Søndag septuagesima
    8 => [ ref_refh("jer",9,22,23),
           ref_refg("1kor",9,24,27),
           ref_refg("matt",20,1,16) ],

    // Søndag seksagesima
    9 => [ ref_refh('es',55,6,11),
           ref_refg('1kor',1,18,25,'1 Kor 1,18-21[25]'),
           ref_refg('mark',4,1,20) ],

    // Fastelavns søndag
    10 => [ ref_refh('sl',2),
            ref_refg('1pet',3,18,22),
            ref_refg('matt',3,13,17) ],

    // 1. søndag i fasten
    11 => [ ref_refh('1mos',3,1,19),
            ref_refg('2kor',6,1,10,'2 Kor 6,1-2[10]'),
            ref_refg('matt',4,1,11) ],

    // 2. søndag i fasten
    12 => [ ref_refh('sl',42,2,6),
            ref_refg('1thess',4,1,7),
            ref_refg('matt',15,21,28) ],

    // 3. søndag i fasten
    13 => [ ref_refh('5mos',18,9,15),
            ref_refg('ef',5,1,9,'Ef 5,[1]6-9'),
            ref_refg('luk',11,14,28) ],

    // Midfaste søndag
    14 => [ ref_refh('5mos',8,1,3),
            ref_refg('2kor',9,6,11),
            ref_refg('joh',6,1,15) ],

    // Mariæ bebudelses dag
    15 => [ ref_refh('es',7,10,14),
            ref_refg('1joh',1,1,3),
            ref_refg('luk',1,26,38) ],

    // Palmesøndag
    16 => [ ref_refh('zak',9,9,10),
            ref_refg('fil',2,5,11),
            ref_refg('matt',21,1,9) ],

    // Skærtorsdag
    17 => [ ref_refh('2mos',12,1,11),
            ref_refg('1kor',10,15,17),
            ref_refg('matt',26,17,30) ],

    // Langfredag
    18 => [ ref_refh('es',52,13,15) ."<br>og ". ref_refh('es',53),
            "",
            ref_refg('matt',27,31,56) ],

    // Påskedag
    19 => [ ref_refh('sl',118,19,29),
            ref_refg('1kor',5,7,8),
            ref_refg('mark',16,1,8) ],

    // Anden påskedag
    20 => [ ref_refh('sl',22,22,32,'Sl 22,22b-32'),
            ref_refg('apg',10,34,41),
            ref_refg('luk',24,13,35) ],

    // 1. søndag efter påske
    21 => [ ref_refh('sl',30),
            ref_refg('1joh',5,1,5),
            ref_refg('joh',20,19,31) ],

    // 2. søndag efter påske
    22 => [ ref_refh('ez',34,11,16),
            ref_refg('1pet',2,20,25),
            ref_refg('joh',10,11,16) ],

    // 3. søndag efter påske
    23 => [ ref_refh('es',54,7,10),
            ref_refg('hebr',13,12,16),
            ref_refg('joh',16,16,22) ],

    // Bededag
    24 => [ ref_refh('sl',51,3,19) ."<br>eller ". ref_refh('sl',67),
            ref_refg('hebr',8,10,12),
            ref_refg('matt',3,1,10) ],

    // 4. søndag efter påske
    25 => [ ref_refh('ez',36,26,28),
            ref_refg('jak',1,17,21),
            ref_refg('joh',16,5,15) ],

    // 5. søndag efter påske
    26 => [ ref_refh('1mos',32,25,32),
            ref_refg('jak',1,22,25),
            ref_refg('joh',16,23,28,'Joh 16,23b-28') ],

    // Kristi himmelfarts dag
    27 => [ ref_refh('sl',110,1,4),
            ref_refg('apg',1,1,11),
            ref_refg('mark',16,14,20) ],

    // 6. søndag efter påske
    28 => [ ref_refh('hagg',2,4,9,'Hagg 2,4b-9'),
            ref_refg('1pet',4,7,11,'1 Pet 4,7b-11'),
            ref_refg('joh',15,26,27) ."<br>og ". ref_refg('joh',16,1,4) ],

    // Pinsedag
    29 => [ ref_refh('1mos',11,1,9),
            ref_refg('apg',2,1,11),
            ref_refg('joh',14,22,31) ],

    // Anden pinsedag
    30 => [ ref_refh('sl',104,24,30),
            ref_refg('apg',10,42,48,'ApG 10,42-48a'),
            ref_refg('joh',3,16,21) ],

    // Trinitatis søndag
    31 => [ ref_refh('4mos',21,4,9),
            ref_refg('rom',11,32,36),
            ref_refg('joh',3,1,15) ],

    // 1. søndag efter trinitatis
    32 => [ ref_refh('es',58,5,12),
            ref_refg('1joh',4,16,21,'1 Joh 4,16b-21'),
            ref_refg('luk',16,19,31) ],

    // 2. søndag efter trinitatis
    33 => [ ref_refh('es',25,6,9),
            ref_refg('1joh',3,13,18),
            ref_refg('luk',14,16,24) ],

    // 3. søndag efter trinitatis
    34 => [ ref_refh('es',57,15,19),
            ref_refg('1pet',5,6,11),
            ref_refg('luk',15,1,10) ],

    // 4. søndag efter trinitatis
    35 => [ ref_refh('2sam',11,26,27) ."<br>og ". ref_refh('2sam',12,1,7,'2 Sam 12,1-7a'),
            ref_refg('rom',8,18,23),
            ref_refg('luk',6,36,42) ],

    // 5. søndag efter trinitatis
    36 => [ ref_refh('es',6,1,8),
            ref_refg('1pet',3,8,15,'1 Pet 3,8-9[15a]'),
            ref_refg('luk',5,1,11) ],

    // 6. søndag efter trinitatis
    37 => [ ref_refh('5mos',30,11,14),
            ref_refg('rom',6,3,11),
            ref_refg('matt',5,20,26) ],

    // 7. søndag efter trinitatis
    38 => null,

    // 8. søndag efter trinitatis
    39 => null,

    // 9. søndag efter trinitatis
    40 => null,

    // 10. søndag efter trinitatis
    41 => null,

    // 11. søndag efter trinitatis
    42 => null,

    // 12. søndag efter trinitatis
    43 => null,

    // 13. søndag efter trinitatis
    44 => null,

    // 14. søndag efter trinitatis
    45 => [ ref_refh('sl',103,1,22,'Sl 103,1-13[22]'),
            ref_refg('gal',5,16,25,'Gal 5,[16]22-25'),
            ref_refg('luk',17,11,19) ],

    // 15. søndag efter trinitatis
    46 => [ ref_refh('1mos',8,20,22) ."<br>og ". ref_refh('1mos',9,12,16),
            ref_refg('gal',5,25,26) ."<br>og ". ref_refg('gal',6,1,8),
            ref_refg('matt',6,24,34) ],

    // 16. søndag efter trinitatis
    47 => [ ref_refh('job',19,23,27,'Job 19,23-27a'),
            ref_refg('ef',3,13,21),
            ref_refg('luk',7,11,17) ],

    // 17. søndag efter trinitatis
    48 => [ ref_refh('sl',19,2,7),
            ref_refg('ef',4,1,6),
            ref_refg('luk',14,1,11) ],

    // 18. søndag efter trinitatis
    49 => [ ref_refh('es',40,18,25),
            ref_refg('1kor',1,4,8),
            ref_refg('matt',22,34,46) ],

    // 19. søndag efter trinitatis
    50 => [ ref_refh('es',44,22,28),
            ref_refg('ef',4,22,28),
            ref_refg('mark',2,1,12) ],

    // 20. søndag efter trinitatis
    51 => [ ref_refh('jer',18,1,6),
            ref_refg('ef',5,15,21),
            ref_refg('matt',22,1,14) ],

    // 21. søndag efter trinitatis
    52 => [ ref_refh('2kong',5,1,5) ."<br>og ". ref_refh('2kong',5,9,15),
            ref_refg('ef',6,10,17),
            ref_refg('joh',4,46,53) ],

    // 22. søndag efter trinitatis
    53 => [ ref_refh('1mos',50,15,21),
            ref_refg('fil',1,6,11),
            ref_refg('matt',18,21,35) ],

    // 23. søndag efter trinitatis
    54 => null,

    // 24. søndag efter trinitatis
    55 => [ ref_refh('ez',37,1,14),
            ref_refg('kol',1,9,14,'Kol 1,9b-14'),
            ref_refg('matt',9,18,26) ],

    // 25. søndag efter trinitatis
    56 => null,

    // 26. søndag efter trinitatis
    57 => null,

    // 27. søndag efter trinitatis
    58 => null,

    // Sidste søndag i kirkeåret
    59 => [ ref_refh('es',65,17,19),
            ref_refg('2thess',2,13,17),
            ref_refg('matt',25,31,46) ],

    // 1. søndag i advent
    60 => [ ref_refh('sl',24) ."<br>eller ". ref_refh('sl',100),
            ref_refg('rom',13,11,14),
            ref_refg('matt',21,1,9) ],

    // 2. søndag i advent
    61 => [ ref_refh('es',11,1,10),
            ref_refg('rom',15,4,7),
            ref_refg('luk',21,25,36) ],

    // 3. søndag i advent
    62 => [ ref_refh('es',35),
            ref_refg('1kor',4,1,5),
            ref_refg('matt',11,2,10) ],

    // 4. søndag i advent
    63 => [ ref_refh('es',52,7,10),
            ref_refg('fil',4,4,7),
            ref_refg('joh',1,19,28) ],

    // Juleaften
    64 => [ ref_refh("es",9,1,6,"Es 9,1-6a"),
            "",
            ref_refg("luk",2,1,14) ."<br>eller ". ref_refg("matt",1,18,25) ],

    // Juledag
    65 => [ ref_refh("es",9,1,6,"Es 9,1-6a"),
            ref_refg("hebr",1,1,5),
            ref_refg("luk",2,1,14) ],

    // Anden juledag / Sankt Stefans dag
    66 => [ ref_refh("jer",1,17,19),
            ref_refg("apg",6,8,14) ."<br>og ". ref_refg("apg",7,54,60),
            ref_refg("matt",23,34,39) ],

    // Julesøndag
    67 => [ ref_refh("es",63,7,9),
            ref_refg("gal",4,4,7),
            ref_refg("luk",2,25,40) ],

    // Alle helgens søndag
    99 => [ ref_refh("es",60,18,22),
            ref_refg("åb",7,1,17,"Åb 7,1-12[17]"),
            ref_refg("matt",5,1,12) ]
];
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
                      <th>Dag</th><th class="text-center">Dato i<br>2022-2023</th><th>1. læsning</th><th>2. læsning</th><th>3. læsning</th>
                  </tr>

                  <?php
                  $holidays = new Holiday(2022);

                  for ($hn=60; $hn<=67; ++$hn ) {
                      echo "<tr>\n";
                      echo make_holiday($holidays,$hn),"\n";

                      if (!is_null($lessons1[$hn])) {
                          echo "<td>{$lessons1[$hn][0]}</td>\n";
                          echo "<td>{$lessons1[$hn][1]}</td>\n";
                          echo "<td>{$lessons1[$hn][2]}</td>\n";
                      }
                      else
                          echo "<td colspan=\"3\"><hr></td>\n";
                      echo "</tr>\n";
                  }

                  $holidays = new Holiday(2023);

                  for ($hn=0; $hn<=52; ++$hn ) {
                      echo "<tr>\n";
                      echo make_holiday($holidays,$hn),"\n";

                      if (!is_null($lessons1[$hn])) {
                          echo "<td>{$lessons1[$hn][0]}</td>\n";
                          echo "<td>{$lessons1[$hn][1]}</td>\n";
                          echo "<td>{$lessons1[$hn][2]}</td>\n";
                      }
                      else
                          echo "<td colspan=\"3\"><hr></td>\n";
                      echo "</tr>\n";
                  }


                  // Alle helgens søndag:
                  echo "<tr>\n";
                  echo make_all_saints_holiday($holidays),"\n";

                  if (!is_null($lessons1[99])) {
                      echo "<td>{$lessons1[99][0]}</td>\n";
                      echo "<td>{$lessons1[99][1]}</td>\n";
                      echo "<td>{$lessons1[99][2]}</td>\n";
                  }
                  else
                      echo "<td colspan=\"3\"><hr></td>\n";
                  echo "</tr>\n";


                  for ($hn=53; $hn<=59; ++$hn ) {
                      echo "<tr>\n";
                      echo make_holiday($holidays,$hn),"\n";

                      if (!is_null($lessons1[$hn])) {
                          echo "<td>{$lessons1[$hn][0]}</td>\n";
                          echo "<td>{$lessons1[$hn][1]}</td>\n";
                          echo "<td>{$lessons1[$hn][2]}</td>\n";
                      }
                      else
                          echo "<td colspan=\"3\"><hr></td>\n";
                      echo "</tr>\n";
                  }


                  ?>
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
