<?php

$chap['1mos']     = array(3);
$chap['2mos']     = range(1,30);
$chap['4mos']     = range(1,36);
$chap['5mos']     = array(18);
$chap['dom']      = range(1,21);
$chap['ruth']     = range(1,4);
$chap['sl']       = range(1,150);
$chap['es']       = range(1,66);
$chap['jer']      = array(1, 2, 7);
$chap['obad']     = array(1);
$chap['nah']      = range(1,3);
$chap['sef']      = range(1,3);
$chap['hab']      = range(1,3);
$chap['zak']      = range(1,14);

$chap['matt']     = array(3,4,15);
$chap['mark']     = range(1,16);
$chap['luk']      = array(11);
$chap['ef']       = array(5);
$chap['1kor']     = range(1,16);
$chap['2kor']     = range(1,13);
$chap['1thess']   = range(1,5);
$chap['1pet']     = array(3);

$total_chap_ot = 929;
$total_chap_nt = 260;



$title['GT']      = 'Det Gamle Testamente';
$title['1mos']    = 'Første Mosebog';
$title['2mos']    = 'Anden Mosebog';
$title['4mos']    = 'Fjerde Mosebog';
$title['5mos']    = 'Femte Mosebog';
$title['dom']     = 'Dommerbogen';
$title['ruth']    = 'Ruths Bog';
$title['sl']      = 'Salmernes Bog';
$title['es']      = 'Esajas&rsquo; Bog';
$title['jer']     = 'Jeremias&rsquo; Bog';
$title['obad']    = 'Obadias&rsquo; Bog';
$title['nah']     = 'Nahums Bog';
$title['sef']     = 'Sefanias&rsquo; Bog';
$title['hab']     = 'Habakkuks Bog';
$title['zak']     = 'Zakarias&rsquo; Bog';
$title['NT']      = 'Det Nye Testamente';
$title['matt']    = 'Matthæusevangeliet';
$title['mark']    = 'Markusevangeliet';
$title['luk']     = 'Lukasevangeliet';
$title['1kor']    = 'Første Korintherbrev';
$title['2kor']    = 'Andet Korintherbrev';
$title['ef']      = 'Efeserbrevet';
$title['1thess']  = 'Første Thessalonikerbrev';
$title['1pet']    = 'Første Petersbrev';

$style['GT'] = 'btn-success'; // Bedste værdi for GT's kapitler
$style['NT'] = 'btn-warning'; // Bedste værdi for NT's kapitler


$chaptype['1mos'] = 'kapitel';
$chaptype['2mos'] = 'kapitel';
$chaptype['4mos'] = 'kapitel';
$chaptype['5mos'] = 'kapitel';
$chaptype['dom']  = 'kapitel';
$chaptype['ruth'] = 'kapitel';
$chaptype['sl']   = 'salme';
$chaptype['es']   = 'kapitel';
$chaptype['jer']  = 'kapitel';
$chaptype['obad'] = 'kapitel';
$chaptype['nah']  = 'kapitel';
$chaptype['sef']  = 'kapitel';
$chaptype['hab']  = 'kapitel';
$chaptype['zak']  = 'kapitel';
$chaptype['matt'] = 'kapitel';
$chaptype['mark'] = 'kapitel';
$chaptype['luk']  = 'kapitel';
$chaptype['1kor'] = 'kapitel';
$chaptype['2kor'] = 'kapitel';
$chaptype['ef']   = 'kapitel';
$chaptype['1thess'] = 'kapitel';
$chaptype['1pet'] = 'kapitel';

$abbrev['1mos'] = '1 Mos';
$abbrev['2mos'] = '2 Mos';
$abbrev['4mos'] = '4 Mos';
$abbrev['5mos'] = '5 Mos';
$abbrev['dom']  = 'Dom';
$abbrev['ruth'] = 'Ruth';
$abbrev['sl']   = 'Sl';
$abbrev['es']   = 'Es';
$abbrev['jer']  = 'Jer';
$abbrev['obad'] = 'Obad';
$abbrev['nah']  = 'Nah';
$abbrev['sef']  = 'Sef';
$abbrev['hab']  = 'Hab';
$abbrev['zak']  = 'Zak';
$abbrev['matt'] = 'Matt';
$abbrev['mark'] = 'Mark';
$abbrev['luk']  = 'Luk';
$abbrev['1kor'] = '1 Kor';
$abbrev['2kor'] = '2 Kor';
$abbrev['ef']   = 'Ef';
$abbrev['1thess'] = '1 Thess';
$abbrev['1pet'] = '1 Pet';

$deabbrev = [];
foreach ($abbrev as $k => $v)
    $deabbrev[$v] = $k;


$style['1mos']    = 'btn-secondary';
$style['2mos']    = 'btn-info';
$style['4mos']    = 'btn-info';
$style['5mos']    = 'btn-secondary';
$style['dom']     = 'btn-success';
$style['ruth']    = 'btn-success';
$style['matt']    = 'btn-secondary';
$style['1pet']    = 'btn-secondary';

$style['sl'] = array();
for ($k=1; $k<=135; ++$k)
    $style['sl'][$k] = 'btn-success';
for ($k=136; $k<=150; ++$k)
    $style['sl'][$k] = 'btn-info';
$style['sl'][119] = 'btn-info';

$style['es']      = array( 1 => 'btn-success',
                           2 => 'btn-success', 
                           3 => 'btn-success', 
                           4 => 'btn-success', 
                           5 => 'btn-success', 
                           6 => 'btn-success', 
                           7 => 'btn-success');
for ($k=8; $k<=66; ++$k)
    $style['es'][$k] = 'btn-warning';

$style['jer']     = 'btn-success';
$style['obad']    = 'btn-info';
$style['nah']     = 'btn-info';
$style['sef']     = 'btn-info';
$style['hab']     = 'btn-info';
$style['zak']     = 'btn-success';
$style['mark']    = 'btn-warning';
$style['luk']     = 'btn-secondary';
$style['1kor']    = 'btn-warning';
$style['2kor']    = 'btn-warning';
$style['ef']      = 'btn-secondary';
$style['1thess']  = 'btn-warning';


