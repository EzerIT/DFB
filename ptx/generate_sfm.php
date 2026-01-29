<?php

require_once('../oversigt.inc.php');

$sfm_name['1mos']     = 'GEN';
$sfm_name['2mos']     = 'EXO';
$sfm_name['3mos']     = 'LEV';
$sfm_name['4mos']     = 'NUM';
$sfm_name['5mos']     = 'DEU';
$sfm_name['dom']      = 'JDG';
$sfm_name['ruth']     = 'RUT';
$sfm_name['2sam']     = '2SA';
$sfm_name['2kong']    = '2KI';
$sfm_name['job']      = 'JOB';
$sfm_name['sl']       = 'PSA';
$sfm_name['ordsp']    = 'PRO';
$sfm_name['præd']     = 'ECC';
$sfm_name['es']       = 'ISA';
$sfm_name['jer']      = 'JER';
$sfm_name['ez']       = 'EZK';
$sfm_name['am']       = 'AMO';
$sfm_name['obad']     = 'OBA';
$sfm_name['jon']      = 'JON';
$sfm_name['nah']      = 'NAM';
$sfm_name['hab']      = 'HAB';
$sfm_name['sef']      = 'ZEP';
$sfm_name['hagg']     = 'HAG';
$sfm_name['zak']      = 'ZEK';

$sfm_name['matt']     = 'MAT';
$sfm_name['mark']     = 'MRK';
$sfm_name['luk']      = 'LUK';
$sfm_name['joh']      = 'JHN';
$sfm_name['apg']      = 'ACT';
$sfm_name['gal']      = 'ROM';
$sfm_name['ef']       = '1CO';
$sfm_name['fil']      = '2CO';
$sfm_name['kol']      = 'GAL';
$sfm_name['rom']      = 'EPH';
$sfm_name['1kor']     = 'PHP';
$sfm_name['2kor']     = 'COL';
$sfm_name['1thess']   = '1TH';
$sfm_name['2thess']   = '2TH';
$sfm_name['1tim']     = '1TI';
$sfm_name['2tim']     = '2TI';
$sfm_name['tit']      = 'TIT';
$sfm_name['filem']    = 'PHM';
$sfm_name['hebr']     = 'HEB';
$sfm_name['jak']      = 'JAS';
$sfm_name['1pet']     = '1PE';
$sfm_name['2pet']     = '2PE';
$sfm_name['1joh']     = '1JN';
$sfm_name['2joh']     = '2JN';
$sfm_name['3joh']     = '3JN';
$sfm_name['jud']      = 'JUD';
$sfm_name['åb']       = 'REV';

foreach ($sfm_name as $abb => $sfmnm) {
    if ($style[$abb]==$modenhed['ufuldstændigt'])
        continue;

    echo "php replaceitsfm.php -n $sfmnm generated/$sfmnm.sfm";
    
    foreach ($chap[$abb] as $ch) {
        if (is_array($style[$abb]) && $style[$abb][$ch]==$modenhed['ufuldstændigt'])
            continue;
        echo sprintf(' ../tekst/%s%03d.txt',$abb,$ch);
    }
    echo "\n";
}
