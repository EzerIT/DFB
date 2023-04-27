<?php

$chap['1mos']     = [3,8,9,11,12,32,50];
$chap['2mos']     = range(1,40);
$chap['3mos']     = [19];
$chap['4mos']     = range(1,36);
$chap['5mos']     = [6,8,10,18,30];
$chap['dom']      = range(1,21);
$chap['ruth']     = range(1,4);
$chap['2sam']     = [11,12];
$chap['2kong']    = [5];
$chap['job']      = [1,2,5,6,7,8,9,10,11,12,19,32,33,34,35,36,37,38,42];
$chap['sl']       = range(1,150);
$chap['ordsp']    = [3];
$chap['præd']     = [8];
$chap['es']       = range(1,66);
$chap['jer']      = [1, 2, 7, 9, 18, 23];
$chap['ez']       = [34,36,37];
$chap['am']       = [8];
$chap['obad']     = [1];
$chap['jon']      = range(1,4);
$chap['nah']      = range(1,3);
$chap['hab']      = range(1,3);
$chap['sef']      = range(1,3);
$chap['hagg']     = [2];
$chap['zak']      = range(1,14);

$chap['matt']     = range(1,28);
$chap['mark']     = range(1,16);
$chap['luk']      = [1,2,5,7,10,11,14,15,16,17,18,19,21,24];
$chap['joh']      = [1,2,3,4,6,10,14,15,16,20];
$chap['apg']      = [1,2,6,7,10];
$chap['gal']      = range(1,6);
$chap['ef']       = range(1,6);
$chap['fil']      = range(1,4);
$chap['kol']      = range(1,4);
$chap['rom']      = [6,8,11,12,13,15];
$chap['1kor']     = range(1,16);
$chap['2kor']     = range(1,13);
$chap['1thess']   = range(1,5);
$chap['2thess']   = [2];
$chap['tit']      = [3];
$chap['hebr']     = [1,8,13];
$chap['jak']      = [1];
$chap['1pet']     = [2,3,4,5];
$chap['2pet']     = [1];
$chap['1joh']     = range(1,5);
$chap['åb']       = range(1,22);

$total_chap_ot = 929;
$total_chap_nt = 260;



$title['GT']      = 'Det Gamle Testamente';
$title['1mos']    = 'Første Mosebog';
$title['2mos']    = 'Anden Mosebog';
$title['3mos']    = 'Tredje Mosebog';
$title['4mos']    = 'Fjerde Mosebog';
$title['5mos']    = 'Femte Mosebog';
$title['dom']     = 'Dommerbogen';
$title['ruth']    = 'Ruths Bog';
$title['2sam']    = 'Anden Samuelsbog';
$title['2kong']   = 'Anden Kongebog';
$title['job']     = 'Jobs Bog';
$title['sl']      = 'Salmernes Bog';
$title['ordsp']   = 'Ordsprogenes Bog';
$title['præd']    = 'Prædikerens Bog';
$title['es']      = 'Esajas&rsquo; Bog';
$title['jer']     = 'Jeremias&rsquo; Bog';
$title['ez']      = 'Ezekiels Bog';
$title['am']      = 'Amos&rsquo; Bog';
$title['obad']    = 'Obadias&rsquo; Bog';
$title['jon']     = 'Jonas&rsquo; Bog';
$title['nah']     = 'Nahums Bog';
$title['hab']     = 'Habakkuks Bog';
$title['sef']     = 'Sefanias&rsquo; Bog';
$title['hagg']    = 'Haggajs Bog';
$title['zak']     = 'Zakarias&rsquo; Bog';
$title['NT']      = 'Det Nye Testamente';
$title['matt']    = 'Matthæusevangeliet';
$title['mark']    = 'Markusevangeliet';
$title['luk']     = 'Lukasevangeliet';
$title['joh']     = 'Johannesevangeliet';
$title['apg']     = 'Apostlenes Gerninger';
$title['rom']     = 'Paulus&rsquo; Brev til Romerne';
$title['1kor']    = 'Paulus&rsquo; Første Brev til Korintherne';
$title['2kor']    = 'Paulus&rsquo; Andet Brev til Korintherne';
$title['gal']     = 'Paulus&rsquo; Brev til Galaterne';
$title['ef']      = 'Paulus&rsquo; Brev til Efeserne';
$title['fil']     = 'Paulus&rsquo; Brev til Filipperne';
$title['kol']     = 'Paulus&rsquo; Brev til Kolossenserne';
$title['1thess']  = 'Paulus&rsquo; Første Brev til Thessalonikerne';
$title['2thess']  = 'Paulus&rsquo; Andet Brev til Thessalonikerne';
$title['tit']     = 'Paulus&rsquo; Brev til Titus';
$title['hebr']    = 'Brevet til Hebræerne';
$title['jak']     = 'Jakobs Brev';
$title['1pet']    = 'Peters Første Brev';
$title['2pet']    = 'Peters Andet Brev';
$title['1joh']    = 'Johannes&rsquo; Første Brev';
$title['åb']      = 'Johannes&rsquo; Åbenbaring';


foreach ($chap as $k => $v)
    $chaptype[$k] = 'kapitel';

$chaptype['sl']   = 'salme';

$abbrev['1mos'] = '1 Mos';
$abbrev['2mos'] = '2 Mos';
$abbrev['3mos'] = '3 Mos';
$abbrev['4mos'] = '4 Mos';
$abbrev['5mos'] = '5 Mos';
$abbrev['dom']  = 'Dom';
$abbrev['ruth'] = 'Ruth';
$abbrev['2sam'] = '2 Sam';
$abbrev['2kong']= '2 Kong';
$abbrev['job']  = 'Job';
$abbrev['sl']   = 'Sl';
$abbrev['ordsp'] = 'Ordsp';
$abbrev['præd'] = 'Præd';
$abbrev['es']   = 'Es';
$abbrev['jer']  = 'Jer';
$abbrev['ez']   = 'Ez';
$abbrev['am']   = 'Am';
$abbrev['obad'] = 'Obad';
$abbrev['jon']  = 'Jon';
$abbrev['nah']  = 'Nah';
$abbrev['hab']  = 'Hab';
$abbrev['sef']  = 'Sef';
$abbrev['hagg'] = 'Hagg';
$abbrev['zak']  = 'Zak';
$abbrev['matt'] = 'Matt';
$abbrev['mark'] = 'Mark';
$abbrev['luk']  = 'Luk';
$abbrev['joh']  = 'Joh';
$abbrev['apg']  = 'ApG';
$abbrev['rom']  = 'Rom';
$abbrev['1kor'] = '1 Kor';
$abbrev['2kor'] = '2 Kor';
$abbrev['gal']  = 'Gal';
$abbrev['ef']   = 'Ef';
$abbrev['fil']  = 'Fil';
$abbrev['kol']  = 'Kol';
$abbrev['1thess'] = '1 Thess';
$abbrev['2thess'] = '2 Thess';
$abbrev['tit']  = 'Tit';
$abbrev['hebr'] = 'Heb';
$abbrev['jak']  = 'Jak';
$abbrev['1pet'] = '1 Pet';
$abbrev['2pet'] = '2 Pet';
$abbrev['1joh'] = '1 Joh';
$abbrev['åb']   = 'Åb';

$deabbrev = [];
foreach ($abbrev as $k => $v)
    $deabbrev[$v] = $k;



// Modenhed => button styles
$modenhed['ufuldstændigt'] = 'btn-secondary';
$modenhed['rå oversættelse'] = 'btn-warning';
$modenhed['delvis færdig'] = 'btn-info';
$modenhed['færdig'] = 'btn-success';


$style['GT'] = $modenhed['færdig']; // Bedste værdi for GT's kapitler - bruges ved generering af PDF
$style['NT'] = $modenhed['delvis færdig']; // Bedste værdi for NT's kapitler - bruges ved generering af PDF


$style['1mos']    = $modenhed['ufuldstændigt'];
$style['2mos']    = $modenhed['delvis færdig'];
$style['3mos']    = $modenhed['ufuldstændigt'];
$style['4mos']    = $modenhed['delvis færdig'];
$style['5mos']    = $modenhed['ufuldstændigt'];
$style['dom']     = $modenhed['færdig'];
$style['ruth']    = $modenhed['færdig'];
$style['2sam']    = $modenhed['ufuldstændigt'];
$style['2kong']   = $modenhed['ufuldstændigt'];

$style['job']     = array();
for ($k=1; $k<=42; ++$k)
    $style['job'][$k] = $modenhed['ufuldstændigt'];

for ($k=32; $k<=37; ++$k)
    $style['job'][$k] = $modenhed['rå oversættelse'];
$style['job'][1] = $modenhed['delvis færdig'];
$style['job'][2] = $modenhed['delvis færdig'];
$style['job'][6] = $modenhed['delvis færdig'];
$style['job'][7] = $modenhed['delvis færdig'];
$style['job'][8] = $modenhed['delvis færdig'];
$style['job'][9] = $modenhed['delvis færdig'];
$style['job'][10] = $modenhed['delvis færdig'];
$style['job'][11] = $modenhed['delvis færdig'];
$style['job'][12] = $modenhed['delvis færdig'];
$style['job'][42] = $modenhed['delvis færdig'];


$style['sl'] = array();
for ($k=1; $k<=135; ++$k)
    $style['sl'][$k] = $modenhed['færdig'];
for ($k=136; $k<=150; ++$k)
    $style['sl'][$k] = $modenhed['delvis færdig'];
$style['sl'][119] = $modenhed['delvis færdig'];

$style['ordsp']   = $modenhed['ufuldstændigt'];
$style['præd']    = $modenhed['ufuldstændigt'];

for ($k=1; $k<=26; ++$k)
    $style['es'][$k] = $modenhed['færdig'];
for ($k=27; $k<=66; ++$k)
    $style['es'][$k] = $modenhed['rå oversættelse'];

$style['jer']     = [ 1  => $modenhed['færdig'],
                      2  => $modenhed['færdig'], 
                      7  => $modenhed['færdig'], 
                      9  => $modenhed['ufuldstændigt'], 
                      18 => $modenhed['ufuldstændigt'],
                      23 => $modenhed['ufuldstændigt']];

$style['ez']      = $modenhed['ufuldstændigt'];
$style['am']      = $modenhed['ufuldstændigt'];
$style['obad']    = $modenhed['delvis færdig'];
$style['jon']     = $modenhed['delvis færdig'];
$style['nah']     = $modenhed['delvis færdig'];
$style['hab']     = $modenhed['delvis færdig'];
$style['sef']     = $modenhed['delvis færdig'];
$style['hagg']    = $modenhed['ufuldstændigt'];
$style['zak']     = $modenhed['færdig'];
$style['matt']    = $modenhed['delvis færdig'];
$style['mark']    = $modenhed['delvis færdig'];
$style['luk']     = $modenhed['ufuldstændigt'];
$style['joh']     = $modenhed['ufuldstændigt'];
$style['apg']     = $modenhed['ufuldstændigt'];
$style['rom']     = $modenhed['ufuldstændigt'];
$style['1kor']    = $modenhed['rå oversættelse'];
$style['2kor']    = $modenhed['rå oversættelse'];
$style['gal']     = $modenhed['delvis færdig'];
$style['ef']      = $modenhed['delvis færdig'];
$style['fil']     = $modenhed['delvis færdig'];
$style['kol']     = $modenhed['delvis færdig'];
$style['1thess']  = $modenhed['rå oversættelse'];
$style['2thess']  = $modenhed['rå oversættelse'];
$style['tit']     = $modenhed['ufuldstændigt'];
$style['hebr']    = $modenhed['ufuldstændigt'];
$style['jak']     = $modenhed['ufuldstændigt'];
$style['1pet']    = $modenhed['ufuldstændigt'];
$style['2pet']    = $modenhed['ufuldstændigt'];
$style['1joh']    = $modenhed['ufuldstændigt'];
$style['åb']      = $modenhed['delvis færdig'];
