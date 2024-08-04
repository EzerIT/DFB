<?php
require_once('head.inc.php');
require_once('oversigt.inc.php');
require_once('verses.inc.php');
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
    '2tim'   => 'II_Timothy',
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


function ref($book,$chapt,$from=0,$to=0,$alt_refname=null) {
    global $abbrev, $chap, $verses, $style, $modenhed;

    $refname = !is_null($alt_refname) ? $alt_refname :
               ($from==0 ? "$abbrev[$book] $chapt" :
               "$abbrev[$book] $chapt,$from-$to");

    if (!isset($chap[$book]) || !in_array($chapt,$chap[$book]))
        return $refname;
    else if (($style[$book]==$modenhed['ufuldstændigt'] ||
             is_array($style[$book]) && $style[$book][$chapt]==$modenhed['ufuldstændigt']) &&
            !in_array(($from==0?1:$from),$verses[$book][$chapt]))
        return $refname;
    elseif ($from==0)
        return "<a target=\"_blank\" href=\"show.php?bog=$book&kap=$chapt\">$refname</a>";

    return "<a target=\"_blank\" href=\"show.php?bog=$book&kap=$chapt&fra=$from&til=$to\">$refname</a>";
}

function refh($book,$chapt,$from=0,$to=0) {
    global $hebbooks;
    $hbook = $hebbooks[$book];

    if ($from==0)
        return "<a target=\"_blank\" href=\"https://learner.bible/text/show_text/ETCBC4/$hbook/$chapt\">Ⓗ</a>";

    return "<a target=\"_blank\" href=\"https://learner.bible/text/show_text/ETCBC4/$hbook/$chapt/$from/$to\">Ⓗ</a>";
}

function refg($book,$chapt,$from=0,$to=0) {
    global $grbooks;
    $gbook = $grbooks[$book];

    if ($from==0)
        return "<a target=\"_blank\" href=\"https://learner.bible/text/show_text/nestle1904/$gbook/$chapt\">Ⓖ</a>";

    return "<a target=\"_blank\" href=\"https://learner.bible/text/show_text/nestle1904/$gbook/$chapt/$from/$to\">Ⓖ</a>";
}

function ref_refh($book,$chapt,$from=0,$to=0,$alt_refname=null) {
    return ref($book,$chapt,$from,$to,$alt_refname) . ' ' . refh($book,$chapt,$from,$to);
}

function ref_refg($book,$chapt,$from=0,$to=0,$alt_refname=null) {
    return ref($book,$chapt,$from,$to,$alt_refname) . ' ' . refg($book,$chapt,$from,$to);
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
           ref_refg("luk",2,41,52) . "<br>eller " . ref_refg("mark",10,13,16) ],

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
    18 => [ ref_refh('1mos',22,1,18) ."<br>og/eller: ". ref_refh('es',52,13,15) ."<br>og ". ref_refh('es',53),
            "",
            ref_refg('matt',27,31,56) ."<br>eller ". ref_refg('mark',15,20,39) ],

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
            ref_refg('1joh',5,1,5) ."<br>eller ". ref_refg('apg',2,22,28),
            ref_refg('joh',20,19,31) ],

    // 2. søndag efter påske
    22 => [ ref_refh('ez',34,11,16),
            ref_refg('1pet',2,20,25)  ."<br>eller ". ref_refg('apg',2,36,41),
            ref_refg('joh',10,11,16) ],

    // 3. søndag efter påske
    23 => [ ref_refh('es',54,7,10),
            ref_refg('hebr',13,12,16) ."<br>eller ". ref_refg('apg',4,7,12),
            ref_refg('joh',16,16,22) ],

    // Bededag
    24 => [ ref_refh('sl',51,3,19) ."<br>eller ". ref_refh('sl',67),
            ref_refg('hebr',8,10,12),
            ref_refg('matt',3,1,10) ],

    // 4. søndag efter påske
    25 => [ ref_refh('ez',36,26,28),
            ref_refg('jak',1,17,21) ."<br>eller ". ref_refg('apg',9,1,18),
            ref_refg('joh',16,5,15) ],

    // 5. søndag efter påske
    26 => [ ref_refh('1mos',32,25,32) ."<br>eller ". ref_refh('jer',29,11,13,'Jer 29,11-13a'),
            ref_refg('jak',1,22,25) ."<br>eller ". ref_refg('apg',6,1,4),
            ref_refg('joh',16,23,28,'Joh 16,23b-28') ],

    // Kristi himmelfarts dag
    27 => [ ref_refh('sl',110,1,4),
            ref_refg('apg',1,1,11),
            ref_refg('mark',16,14,20) ],

    // 6. søndag efter påske
    28 => [ ref_refh('hagg',2,4,9,'Hagg 2,4b-9') ."<br>eller ". ref_refh('joel',3,1,5),
            ref_refg('1pet',4,7,11,'1 Pet 4,7b-11') ."<br>eller ". ref_refg('apg',1,12,14),
            ref_refg('joh',15,26,27) ."<br>og ". ref_refg('joh',16,1,4) ],

    // Pinsedag
    29 => [ ref_refh('1mos',11,1,9) ."<br>eller ". ref_refh('1mos',2,4,7,"1 Mos 2,4b-7"),
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
    38 => [ ref_refh('sl',126),
            ref_refg('rom',6,19,23),
            ref_refg('luk',19,1,10) ],

    // 8. søndag efter trinitatis
    39 => [ ref_refh('jer',23,16,24),
            ref_refg('rom',8,14,17),
            ref_refg('matt',7,15,21) ],

    // 9. søndag efter trinitatis
    40 => [ ref_refh('ordsp',3,27,35),
            ref_refg('1joh',1,5,10) ."<br>og ". ref_refg('1joh',2,1,2),
            ref_refg('luk',16,1,9) ],

    // 10. søndag efter trinitatis
    41 => [ ref_refh('5mos',6,4,9),
            ref_refg('1kor',12,1,11,'1 Kor 12,1-7[11]'),
            ref_refg('luk',19,41,48) ],

    // 11. søndag efter trinitatis
    42 => [ ref_refh('job',5,8,16),
            ref_refg('1kor',15,1,10,'1 Kor 15,1-10a'),
            ref_refg('luk',18,9,14) ],

    // 12. søndag efter trinitatis
    43 => [ ref_refh('sl',115,1,9),
            ref_refg('2kor',3,4,9),
            ref_refg('mark',7,31,37) ],

    // 13. søndag efter trinitatis
    44 => [ ref_refh('3mos',19,1,2) ."<br>og ". ref_refh('3mos',19,9,18),
            ref_refg('gal',2,16,21),
            ref_refg('luk',10,23,37) ],

    // 14. søndag efter trinitatis
    45 => [ ref_refh('sl',103,1,22,'Sl 103,1-13[22]'),
            ref_refg('gal',5,16,25,'Gal 5,[16]22-25') ."<br>eller ". ref_refg('rom',7,15,19),
            ref_refg('luk',17,11,19) ],

    // 15. søndag efter trinitatis
    46 => [ ref_refh('1mos',8,20,22) ."<br>og ". ref_refh('1mos',9,12,16),
            ref_refg('gal',5,25,26) ."<br>og ". ref_refg('gal',6,1,8),
            ref_refg('matt',6,24,34) ],

    // 16. søndag efter trinitatis
    47 => [ ref_refh('job',19,23,27,'Job 19,23-27a') ."<br>eller ". ref_refh('job',3,11,22),
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
    52 => [ ref_refh('2kong',5,1,5) ."<br>og ". ref_refh('2kong',5,9,15) ."<br>eller ". ref_refh('ez',18,1,4,"Ez 18,1-4a"),
            ref_refg('ef',6,10,17),
            ref_refg('joh',4,46,53) ],

    // 22. søndag efter trinitatis
    53 => [ ref_refh('1mos',50,15,21),
            ref_refg('fil',1,6,11),
            ref_refg('matt',18,21,35) ],

    // 23. søndag efter trinitatis
    54 => [ ref_refh('am',8,4,7),
            ref_refg('rom',13,1,7),
            ref_refg('matt',22,15,22) ],

    // 24. søndag efter trinitatis
    55 => [ ref_refh('ez',37,1,14) ."<br>eller ". ref_refh('dan',7,9,10) ."<br>og ". ref_refh('dan',7,13,14),
            ref_refg('kol',1,9,14,'Kol 1,9b-14'),
            ref_refg('matt',9,18,26) ],

    // 25. søndag efter trinitatis
    56 => [ ref_refh('es',51,12,16),
            ref_refg('1thess',4,13,18),
            ref_refg('matt',24,15,28) ],

    // 26. søndag efter trinitatis
    57 => [ ref_refh("præd",8,9,15),
            ref_refg("kol",3,12,17),
            ref_refg("matt",13,24,30) ."<br>eller ". ref_refg("matt",13,44,52) ],

    // Sidste søndag i kirkeåret
    58 => [ ref_refh('es',65,17,19),
            ref_refg('2thess',2,13,17),
            ref_refg('matt',25,31,46) ],

    // 1. søndag i advent
    59 => [ ref_refh('sl',24) ."<br>eller ". ref_refh('sl',100),
            ref_refg('rom',13,11,14),
            ref_refg('matt',21,1,9) ],

    // 2. søndag i advent
    60 => [ ref_refh('es',11,1,10),
            ref_refg('rom',15,4,7),
            ref_refg('luk',21,25,36) ],

    // 3. søndag i advent
    61 => [ ref_refh('es',35),
            ref_refg('1kor',4,1,5),
            ref_refg('matt',11,2,10) ],

    // 4. søndag i advent
    62 => [ ref_refh('es',52,7,10),
            ref_refg('fil',4,4,7),
            ref_refg('joh',1,19,28) ],

    // Juleaften
    63 => [ ref_refh("es",9,1,6,"Es 9,1-6a"),
            "",
            ref_refg("luk",2,1,14) ."<br>eller ". ref_refg("matt",1,18,25) ],

    // Juledag
    64 => [ ref_refh("es",9,1,6,"Es 9,1-6a"),
            ref_refg("hebr",1,1,5),
            ref_refg("luk",2,1,14) ],

    // Anden juledag / Sankt Stefans dag
    65 => [ ref_refh("jer",1,17,19),
            ref_refg("apg",6,8,14) ."<br>og ". ref_refg("apg",7,54,60),
            ref_refg("matt",23,34,39) ],

    // Julesøndag
    66 => [ ref_refh("es",63,7,9),
            ref_refg("gal",4,4,7),
            ref_refg("luk",2,25,40) ],

    // Alle helgens søndag
    99 => [ ref_refh("es",60,18,22),
            ref_refg("åb",7,1,17,"Åb 7,1-12[17]"),
            ref_refg("matt",5,1,12) ]
];

$lessons2 = [
    // Nytårsdag
    0 => [ ref_refh("sl",90),
           ref_refg("jak",4,13,17),
           ref_refg("matt",6,5,13) ],

    // Helligtrekongers søndag
    1 => [ ref_refh("es",60,1,6) . "<br>eller " . ref_refh("job",28,12,28),
           ref_refg("1joh",2,7,11),
           ref_refg("matt",2,1,12) . "<br>eller " . ref_refg("joh",8,12,20) ],

    // 1. søndag efter helligtrekonger
    2 => [ ref_refh("sl",8),
           ref_refg("kol",1,15,19),
           ref_refg("mark",10,13,16) ],

    // 2. søndag efter helligtrekonger
    3 => [ ref_refh("1kong",8,1,1,"1 Kong 8,1") . "<br>og " . ref_refh("1kong",8,12,13) . "<br>og " . ref_refh("1kong",8,22,30)
        . "<br>eller " . ref_refh("jer",17,12,14),
           ref_refg("1joh",2,28,29) . "<br>og " . ref_refg("1joh",3,1,3),
           ref_refg("joh",4,5,26) ],

    // 3. søndag efter helligtrekonger
    4 => [ ref_refh("1mos",15,1,6),
           ref_refg("hebr",11,1,6),
           ref_refg("luk",17,5,10) ],

    // 4. søndag efter helligtrekonger
    5 => [ ref_refh("es",40,26,31),
           ref_refg("rom",4,18,25,"Rom 4,18-22[25]"),
           ref_refg("matt",14,22,33) ],

    // 5. søndag efter helligtrekonger
    6 => [ ref_refh("præd",8,9,15),
           ref_refg("kol",3,12,17),
           ref_refg("matt",13,24,30) ."<br>eller ". ref_refg("matt",13,44,52) ],

    // Sidste søndag efter helligtrekonger
    7 => [ ref_refh("es",2,2,5),
           ref_refg("kol",1,25,28,"Kol 1,25d-28"),
           ref_refg("joh",12,23,33) ],

    // Søndag septuagesima
    8 => [ ref_refh("job",9,1,12),
           ref_refg("apg",17,22,34),
           ref_refg("matt",25,14,30 ) ],

    // Søndag seksagesima
    9 => [ ref_refh('es',45,5,12),
           ref_refg('1kor',1,18,25,'1 Kor 1,18-21[25]') ."<br>eller ". ref_refg("2tim",3,10,17),
           ref_refg('mark',4,26,32) ],

    // Fastelavns søndag
    10 => [ ref_refh('sl',31,2,6),
            ref_refg('1kor',13),
            ref_refg('luk',18,31,43) ],

    // 1. søndag i fasten
    11 => [ ref_refh('1mos',4,1,12),
            ref_refg('jak',1,9,16),
            ref_refg('luk',22,24,32) ],

    // 2. søndag i fasten
    12 => [ ref_refh('1mos',1,27,31),
            ref_refg('hebr',5,1,10),
            ref_refg('mark',9,14,29) ],

    // 3. søndag i fasten
    13 => [ ref_refh('2mos',32,7,10) ."<br>og ". ref_refh('2mos',32,30,32),
            ref_refg('åb',2,1,7),
            ref_refg('joh',8,42,51) ],

    // Midfaste søndag
    14 => [ ref_refh('2mos',16,11,18) ."<br>eller ". ref_refh('sl',145,8,16),
            ref_refg('2pet',1,3,11),
            ref_refg('joh',6,24,37,"Joh 6,24-35[37]") ],

    // Mariæ bebudelses dag
    15 => [ ref_refh('es',7,10,14) ."<br>eller ". ref_refh('1mos',18,1,15),
            ref_refg('1kor',1,21,31),
            ref_refg('luk',1,46,55) ],

    // Palmesøndag
    16 => [ ref_refh('zak',9,9,10),
            ref_refg('fil',2,5,11),
            ref_refg('mark',14,3,9) ."<br>eller ". ref_refg('joh',12,1,16) ],

    // Skærtorsdag
    17 => [ ref_refh('sl',116),
            ref_refg('1kor',11,23,26),
            ref_refg('joh',13,1,15) ],

    // Langfredag
    18 => [ ref_refh('sl',22,2,22,"Sl 22,2-22a") ."<br>og/eller: ". ref_refh('es',52,13,15) ."<br>og ". ref_refh('es',53),
            "",
            ref_refg('luk',23,26,49) ."<br>eller ". ref_refg('joh',19,17,37) ],

    // Påskedag
    19 => [ ref_refh('sl',118,19,29) ."<br>eller ". ref_refh('sl',118,13,18),
            ref_refg('1pet',1,3,9),
            ref_refg('matt',28,1,8) ],

    // Anden påskedag
    20 => [ ref_refh('sl',16,5,11),
            ref_refg('1kor',15,12,20) ."<br>eller ". ref_refg('apg',10,34,41),
            ref_refg('joh',20,1,18) ],

    // 1. søndag efter påske
    21 => [ ref_refh('es',43,10,12),
            ref_refg('apg',2,22,28) ."<br>eller ". ref_refg('1pet',1,17,25),
            ref_refg('joh',21,15,19) ],

    // 2. søndag efter påske
    22 => [ ref_refh('sl',23),
            ref_refg('apg',2,36,41) ."<br>eller ". ref_refg('hebr',13,20,21),
            ref_refg('joh',10,22,30) ],

    // 3. søndag efter påske
    23 => [ ref_refh('2mos',3,1,7) ."<br>og ". ref_refh('2mos',3,10,14),
            ref_refg('apg',4,7,12) ."<br>eller ". ref_refg('hebr',4,14,16),
            ref_refg('joh',14,1,11) ],

    // Bededag
    24 => [ ref_refh('sl',130),
            ref_refg('hebr',10,19,25),
            ref_refg('matt',7,7,14) ],

    // 4. søndag efter påske
    25 => [ ref_refh('sl',124),
            ref_refg('apg',9,1,18) ."<br>eller ". ref_refg('2kor',5,14,21),
            ref_refg('joh',8,28,36) ],

    // 5. søndag efter påske
    26 => [ ref_refh('es',44,1,8),
            ref_refg('rom',8,24,28) ."<br>eller ". ref_refg('apg',6,1,4),
            ref_refg('joh',17,1,11) ],

    // Kristi himmelfarts dag
    27 => [ ref_refh('sl',113),
            ref_refg('apg',1,1,11),
            ref_refg('luk',24,46,53) ],

    // 6. søndag efter påske
    28 => [ ref_refh('joel',3,1,5),
            ref_refg('rom',8,31,39,'Rom 8,31b-39') ."<br>eller ". ref_refg('apg',1,12,14),
            ref_refg('joh',17,20,26) ],

    // Pinsedag
    29 => [ ref_refh('jer',31,31,34),
            ref_refg('apg',2,1,11),
            ref_refg('joh',14,15,21) ],

    // Anden pinsedag
    30 => [ ref_refh('ez',11,19,20),
            ref_refg('apg',2,42,47),
            ref_refg('joh',6,44,51) ],

    // Trinitatis søndag
    31 => [ ref_refh('es',49,1,6),
            ref_refg('ef',1,3,14),
            ref_refg('matt',28,16,20) ],

    // 1. søndag efter trinitatis
    32 => [ ref_refh('præd',5,9,19),
            ref_refg('1tim',6,6,12),
            ref_refg('luk',12,13,21) ],

    // 2. søndag efter trinitatis
    33 => [ ref_refh('jer',15,10,10,"Jer 15,10") ."<br>og ". ref_refh('jer',15,15,21),
            ref_refg('åb',3,14,22),
            ref_refg('luk',14,25,35) ],

    // 3. søndag efter trinitatis
    34 => [ ref_refh('es',65,1,2),
            ref_refg('ef',2,17,22),
            ref_refg('luk',15,11,32) ],

    // 4. søndag efter trinitatis
    35 => [ ref_refh('5mos',24,17,22),
            ref_refg('rom',14,7,13),
            ref_refg('matt',5,43,48) ],

    // 5. søndag efter trinitatis
    36 => [ ref_refh('jer',1,4,9),
            ref_refg('1pet',2,4,10),
            ref_refg('matt',16,13,26) ],

    // 6. søndag efter trinitatis
    37 => [ ref_refh('2mos',20,1,17),
            ref_refg('rom',3,23,28),
            ref_refg('matt',19,16,26) ],

    // 7. søndag efter trinitatis
    38 => [ ref_refh('præd',3,1,11),
            ref_refg('rom',8,1,4),
            ref_refg('matt',10,24,31) ],

    // 8. søndag efter trinitatis
    39 => [ ref_refh('mika',3,5,7),
            ref_refg('1joh',4,1,6),
            ref_refg('matt',7,22,29) ],

    // 9. søndag efter trinitatis
    40 => [ ref_refh('es',10,1,3),
            ref_refg('2tim',1,6,11),
            ref_refg('luk',12,32,48) ."<br>eller ". ref_refg('luk',18,1,8) ],

    // 10. søndag efter trinitatis
    41 => [ ref_refh('ez',33,23,23,"Ez 33,23") ."<br>og ". ref_refh('ez',33,30,33),
            ref_refg('hebr',3,12,14),
            ref_refg('matt',11,16,24) ],

    // 11. søndag efter trinitatis
    42 => [ ref_refh('5mos',30,15,20),
            ref_refg('rom',10,4,17,'Rom 10,4-13[17]'),
            ref_refg('luk',7,36,50) ],

    // 12. søndag efter trinitatis
    43 => [ ref_refh('jon',2),
            ref_refg('jak',3,1,12),
            ref_refg('matt',12,31,42) ],

    // 13. søndag efter trinitatis
    44 => [ ref_refh('mika',6,6,8),
            ref_refg('1tim',1,12,17),
            ref_refg('matt',20,20,28) ],

    // 14. søndag efter trinitatis
    45 => [ ref_refh('sl',39,5,14),
            ref_refg('2tim',2,8,13),
            ref_refg('joh',5,1,15) ],

    // 15. søndag efter trinitatis
    46 => [ ref_refh('sl',73,23,28),
            ref_refg('apg',8,26,39),
            ref_refg('luk',10,38,42) ],

    // 16. søndag efter trinitatis
    47 => [ ref_refh('sl',139,1,12),
            ref_refg('1kor',15,21,28),
            ref_refg('joh',11,19,45) ],

    // 17. søndag efter trinitatis
    48 => [ ref_refh('sl',40,2,6),
            ref_refg('jud',1,20,25,"Jud 20-25"),
            ref_refg('mark',2,14,22) ],

    // 18. søndag efter trinitatis
    49 => [ ref_refh('sl',121),
            ref_refg('1joh',4,12,16,"1 Joh 4,12-16a"),
            ref_refg('joh',15,1,11) ],

    // 19. søndag efter trinitatis
    50 => [ ref_refh('1mos',28,10,18),
            ref_refg('1kor',12,12,20),
            ref_refg('joh',1,35,51) ],

    // 20. søndag efter trinitatis
    51 => [ ref_refh('es',5,1,7),
            ref_refg('rom',11,25,32),
            ref_refg('matt',21,28,44) ],

    // 21. søndag efter trinitatis
    52 => [ ref_refh('ez',18,1,4,"Ez 18,1-4a"),
            ref_refg('åb',3,7,13),
            ref_refg('luk',13,1,9) ],

    // 22. søndag efter trinitatis
    53 => [ ref_refh('es',49,13,18),
            ref_refg('ef',4,30,32),
            ref_refg('matt',18,1,14) ],

    // 23. søndag efter trinitatis
    54 => [ ref_refh('jer',7,1,11),
            ref_refg('fil',3,17,21),
            ref_refg('mark',12,38,44) ],

    // 24. søndag efter trinitatis
    55 => [ ref_refh('dan',7,9,10) ."<br>og ". ref_refh('dan',7,13,14),
            ref_refg('2kor',5,1,10),
            ref_refg('joh',5,17,29) ],

    // 25. søndag efter trinitatis
    56 => [ ref_refh('job',14,7,15),
            ref_refg('2pet',3,8,15,"2 Pet 3,8-15a") ."<br>eller ". ref_refg('1kor',15,50,57),
            ref_refg('luk',17,20,33) ],

    // 26. søndag efter trinitatis
    57 => [ ref_refh("præd",8,9,15),
            ref_refg("kol",3,12,17),
            ref_refg("matt",13,24,30) ."<br>eller ". ref_refg("matt",13,44,52) ],

    // Sidste søndag i kirkeåret
    58 => [ ref_refh('mika',4,1,3),
            ref_refg('1kor',3,10,17),
            ref_refg('matt',11,25,30) ],

    // 1. søndag i advent
    59 =>  [ ref_refh('es',42,1,9),
             ref_refg('rom',13,11,14),
             ref_refg('luk',4,16,30) ],

    // 2. søndag i advent
    60 =>  [ ref_refh('es',11,1,10) ."<br>eller ". ref_refh('mal',3,1,3),
             ref_refg('jak',5,7,8),
             ref_refg('matt',25,1,13) ],
    
    // 3. søndag i advent
    61 => [ ref_refh('es',40,1,8),
            ref_refg('2kor',4,5,10),
            ref_refg('luk',1,67,80) ],


    // 4. søndag i advent
    62 => [ ref_refh('es',12),
            ref_refg('2kor',1,18,22),
            ref_refg('joh',3,25,36) ],

    // Juleaften
    63 => [ ref_refh("es",9,1,6,"Es 9,1-6a"),
            "",
            ref_refg("luk",2,1,14) ."<br>eller ". ref_refg("matt",1,18,25) ],

    // Juledag
    64 => [ ref_refh("es",9,1,6,"Es 9,1-6a") ."<br>eller ". ref_refh("1mos",1,1,5),
            ref_refg("1joh",4,7,11), 
            ref_refg("joh",1,1,14) ],

    // Anden juledag / Sankt Stefans dag
    65 => [ ref_refh("es",50,4,7),
            ref_refg("apg",6,8,14) ."<br>og ". ref_refg("apg",7,54,60),
            ref_refg("matt",10,32,42) ],

    // Julesøndag
    66 => [ ref_refh("sl",27,1,5) ."<br>eller ". ref_refh("jer",31,15,17),
            ref_refg("rom",3,19,22,"Rom 3,19-22a"),
            ref_refg("matt",2,13,23) ],

    // Alle helgens søndag
    99 => [ ref_refh("es",49,8,11),
            ref_refg("åb",21,1,7),
            ref_refg("matt",5,13,16) ."<br>eller ". ref_refg("matt",5,1,12) ]
];


$year = idate('Y');
$month = idate('m');
$day = idate('d');

if ($month<11) {
    $tekstraekke = $year%2==0 ? 2 : 1;
    $start_year = $year-1;
}
else {
    $hd = new Holiday($year);
    $start_of_eccl_year = $hd->get_advent(1);

    if ($month < $start_of_eccl_year->month ||
        ($month==$start_of_eccl_year->month && $day < $start_of_eccl_year->day)) {
        $tekstraekke = $year%2==0 ? 2 : 1;
        $start_year = $year-1;
    }
    else {
        $tekstraekke = $year%2==0 ? 1 : 2;
        $start_year = $year;
    }
}

if ($tekstraekke==1) {
    $active1 = "active";
    $active2 = "";
    $show1 = "show";
    $show2 = "";

    $start_year1 = $start_year;
    $start_year2 = $start_year+1;
    $lesson_name = "første";
}
else {
    $active1 = "";
    $active2 = "active";
    $show1 = "";
    $show2 = "show";

    $start_year1 = $start_year+1;
    $start_year2 = $start_year;
    $lesson_name = "anden";
}

$ugedage = ["søndag","mandag","tirsdag","onsdag","torsdag","fredag","lørdag"];
$dow = idate('w');
?>

<div class="container">
    <div class="row justify-content-center">

    <div class="col-lg-10 col-xl-9">
      <div class="card mt-4">
        <div class="card-body">
          <img class="img-fluid float-right d-none d-lg-block" style="width: 300px; margin-top: 0px; margin-left: 10px" src="img/pexels-mart-production-7220071-mod.jpg" alt="">
          <h1>Kirkeårets prædikentekster</h1>

          <p>På denne side finder du links til Den Frie Bibels oversættelse af
              kirkeårets læsninger i den danske folkekirke.</p>
          <p>Et klik på Ⓗ eller Ⓖ giver adgang til den hebraiske eller græske grundtekst.</p>

          <p>Det er i dag <?=$ugedage[$dow]?> den <?=$day?>.<?=$month?>.<?=$year?>. Der læses for tiden efter <?=$lesson_name?> tekstrække.</p>
          <p>Vælg tekstrække her:</p>
          
          <ul class="nav nav-tabs" id="myTab">
              <li class="nav-item">
                  <button class="nav-link <?=$active1?>" id="lesson1-tab" data-toggle="tab" data-target="#lesson1"
                          type="button">Første tekstrække</button>
              </li>
              <li class="nav-item">
                  <button class="nav-link <?=$active2?>" id="lesson2-tab" data-toggle="tab" data-target="#lesson2"
                          type="button">Anden tekstrække</button>
              </li>
          </ul>
          
          <div class="tab-content" id="myTabContent">
              <div class="tab-pane fade <?=$show1?> <?=$active1?>" id="lesson1">
                  <p class="bg-warning mt-2">Bemærk: Nogle få tekster for første tekstrække foreligger endnu ikke i Den Frie Bibel.</p>
                  <div class="table-responsive">
                      <table class="table table-striped">
                          <tr>
                              <th>Dag</th><th class="text-center">Dato i<br><?=$start_year1?>-<?=$start_year1+1?></th><th>1. læsning</th><th>2. læsning</th><th>3. læsning</th>
                          </tr>

                          <?php
                          $holidays = new Holiday($start_year1);

                          for ($hn=59; $hn<=66; ++$hn ) {
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

                          $holidays = new Holiday($start_year1+1);

                          $all_saints_found = false;
                          
                          for ($hn=0; $hn<=58; ++$hn ) {
                              if (!$all_saints_found && $holidays->holiday_is_all_saints($hn)) {
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
                                  $all_saints_found = true;
                              }
                              
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

              <div class="tab-pane fade <?=$show2?> <?=$active2?>" id="lesson2">
                  <p class="bg-warning mt-2">Bemærk: En del tekster for anden tekstrække foreligger endnu ikke i Den Frie Bibel.</p>
                  <div class="table-responsive">
                      <table class="table table-striped">
                          <tr>
                              <th>Dag</th><th class="text-center">Dato i<br><?=$start_year2?>-<?=$start_year2+1?></th><th>1. læsning</th><th>2. læsning</th><th>3. læsning</th>
                          </tr>

                          <?php
                          $holidays = new Holiday($start_year2);

                          for ($hn=59; $hn<=66; ++$hn ) {
                              echo "<tr>\n";
                              echo make_holiday($holidays,$hn),"\n";

                              if (!is_null($lessons2[$hn])) {
                                  echo "<td>{$lessons2[$hn][0]}</td>\n";
                                  echo "<td>{$lessons2[$hn][1]}</td>\n";
                                  echo "<td>{$lessons2[$hn][2]}</td>\n";
                              }
                              else
                                  echo "<td colspan=\"3\"><hr></td>\n";
                              echo "</tr>\n";
                          }

                          $holidays = new Holiday($start_year2+1);

                          $all_saints_found = false;
                          
                          for ($hn=0; $hn<=58; ++$hn ) {
                              if (!$all_saints_found && $holidays->holiday_is_all_saints($hn)) {
                                  // Alle helgens søndag:
                                  echo "<tr>\n";
                                  echo make_all_saints_holiday($holidays),"\n";

                                  if (!is_null($lessons2[99])) {
                                      echo "<td>{$lessons2[99][0]}</td>\n";
                                      echo "<td>{$lessons2[99][1]}</td>\n";
                                      echo "<td>{$lessons2[99][2]}</td>\n";
                                  }
                                  else
                                      echo "<td colspan=\"3\"><hr></td>\n";
                                  echo "</tr>\n";
                                  $all_saints_found = true;
                              }
                              
                              echo "<tr>\n";
                              echo make_holiday($holidays,$hn),"\n";

                              if (!is_null($lessons2[$hn])) {
                                  echo "<td>{$lessons2[$hn][0]}</td>\n";
                                  echo "<td>{$lessons2[$hn][1]}</td>\n";
                                  echo "<td>{$lessons2[$hn][2]}</td>\n";
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
      </div>
    </div>

  </div><!--End of row-->
</div><!--End of container-->

<?php
endbody();
?>
