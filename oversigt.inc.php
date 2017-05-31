<?php

$chap['4mos']     = array_merge(array(6), range(10,17), range(20,25));
$chap['dom']      = range(1,21);
$chap['ruth']     = range(1,4);
$chap['sl']       = range(1,150);
$chap['es']       = range(1,66);
$chap['jer']      = array(1, 2, 7);
$chap['zak']      = range(1,14);

$chap['1thess']   = range(1,5);

$total_chap_ot = 929;
$total_chap_nt = 260;



$title['GT']      = 'Det Gamle Testamente';
$title['4mos']    = 'Fjerde Mosebog';
$title['dom']     = 'Dommerbogen';
$title['ruth']    = 'Ruths Bog';
$title['sl']      = 'Salmernes Bog';
$title['es']      = 'Esajas&rsquo; Bog';
$title['jer']     = 'Jeremias&rsquo; Bog';
$title['zak']     = 'Zakarias&rsquo; Bog';
$title['NT']      = 'Det Nye Testamente';
$title['1thess']  = 'Første Thessalonikerbrev';

$style['GT'] = 'btn-success'; // Bedste værdi for GT's kapitler
$style['NT'] = 'btn-warning'; // Bedste værdi for NT's kapitler


$chaptype['4mos'] = 'kapitel';
$chaptype['dom']  = 'kapitel';
$chaptype['ruth'] = 'kapitel';
$chaptype['sl']   = 'salme';
$chaptype['es']   = 'kapitel';
$chaptype['jer']  = 'kapitel';
$chaptype['zak']  = 'kapitel';
$chaptype['1thess'] = 'kapitel';

$abbrev['4mos'] = '4 Mos';
$abbrev['dom']  = 'Dom';
$abbrev['ruth'] = 'Ruth';
$abbrev['sl']   = 'Sl';
$abbrev['es']   = 'Es';
$abbrev['jer']  = 'Jer';
$abbrev['zak']  = 'Zak';
$abbrev['1thess'] = '1 Thess';

$style['4mos']    = 'btn-info';
$style['dom']     = 'btn-success';
$style['ruth']    = 'btn-success';

$style['sl'] = array();
for ($k=1; $k<=50; ++$k)
    $style['sl'][$k] = 'btn-success';
for ($k=51; $k<=150; ++$k)
    $style['sl'][$k] = 'btn-info';

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
$style['zak']     = 'btn-success';
$style['1thess']  = 'btn-warning';
