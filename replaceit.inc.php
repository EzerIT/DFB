<?php

function replaceit($filename, $chapter, &$title, &$credit, $from_verse, $to_verse) {
    $txt = file_get_contents($filename);

    if (preg_match('/"/',$txt)) {
        echo "<h1>Fejl</h1>\n";
        echo "<p>Tekst indeholder dobbelt citationstegn.</p>\n";
        die;
    }

    $exegetic_layout = preg_match('~//\d~',$txt) && $_SESSION['exegetic']=='on';

    // Handle verse restriction
    if ($from_verse>0)
        $txt = preg_replace("/(===[^=]+===).*(v$from_verse )/s",'\1\2',$txt);


    if ($to_verse>0) {
        // Find first verse > $to_verse
        $matches=array();
        $offset=0;
        while ($found = preg_match('/v([0-9]+)/',$txt,$matches,PREG_OFFSET_CAPTURE,$offset)) {
            $offset = $matches[1][1];
            if (intval($matches[1][0])>intval($to_verse))
                break;
        }
        if ($found) {
            $txt = substr($txt,0,$offset-1);

            // Remove a possible final heading
            if (preg_match('/[^=]==[^=]+==\s*$/',$txt,$matches,PREG_OFFSET_CAPTURE,0)) {
                $txt = substr($txt,0,$matches[0][1]);
            }
        }
    }
    
    global $nextletter;
    $nextletter = 'a';
    global $nextnumber;
    $nextnumber = 1;

    preg_match_all('/!!<(.*)>!!/',$txt,$meta_matches);
    $credit = $meta_matches[1];


    // First collection of substitutions:

    $from[] = '/!!<.*>!!/';  // Credits have been handled above
    $to[] = '';

    $from[] = '/>>>/';
    $to[] = '»›';

    $from[] = '/<<</';
    $to[] = '‹«';

    $from[] = '/>>/';
    $to[] = '»';

    $from[] = '/<</';
    $to[] = '«';

    $from[] = '/>/';
    $to[] = '›';

    $from[] = '/</';
    $to[] = '‹';

    $from[] = '/\'/';
    $to[] = '&rsquo;';

    $from[] = '/^ *\*\*\* *$/m';
    $to[] = '&nbsp;';

    $from[] = '/\*([^\*]+)\*/';
    $to[] = '<i>\1</i>';

    $txt =  preg_replace($from, $to, $txt);


    // Handle footnotes in titles
    if (preg_match('/===(.*)\s*{T: *([^}]+)}===/',$txt,$tit)) {
        $title = $tit[1] . '<span class="ref ref1"><span class="refnumhead" data-toggle="tooltip" data-num="1" data-placement="bottom" title="' . $tit[2]. '" data-html="true"></span></span>';
        ++$nextnumber;
    }
    elseif (preg_match('/===(.*)\s*{E: *([^}]+)}===/',$txt,$tit)) {
        $title = $tit[1] . '<span class="ref refa"><span class="refnumhead" data-toggle="tooltip" data-let="a" data-placement="bottom" title="' . $tit[2] . '" data-html="true">[a]</span></span>';
        ++$nextletter;
    }
    else {
        preg_match('/===(.*)===/',$txt,$tit);
        $title = $tit[1];
    }


    // Second collection of substitutions:

    $from = array();
    $to = array();
    
    $from[] = '/===(.*)===/';  // Titles have been handled above
    $to[] = '';

    if ($exegetic_layout) {
        $from[] = '/==(.*)==/';
        $to[] = '';

        $from[] = '/\n/';
        $to[] = ' ';
    }
    else{
        $from[] = '/^\s*==/m';
        $to[] = "\n@";

        $from[] = '/==\s*$/m';
        $to[] = "@\n";
    }

    $from[] = '/\s*{E: *([^}]+)}/';
    $to[] = '<span class="ref refa"><span class="refnum" data-toggle="tooltip" data-let="REFALET" data-placement="bottom" title="\1" data-html="true"></span></span>';

    $from[] = '/\s*{T: *([^}]+)}/';
    $to[] = '<span class="ref ref1"><span class="refnum" data-toggle="tooltip" data-num="REFANUM" data-placement="bottom" title="\1" data-html="true"></span></span>';

    $from[] = '/\s*{K: *([^}]+)}/';
    $to[] = '';

    $from[] = '/\s*{N: *([^}]+)}/';
    $to[] = '<a class="explain" href="ordforklaring.php?ord=\1">°</a>';

    $from[] = '/JHVHs/i';
    if ($_SESSION['godsname']=='HERREN')
        $to[] = 'H<small>ERRENS</small>';
    elseif ($_SESSION['godsname']=='Herren')
        $to[] = 'Herrens';
    else
        $to[] = $_SESSION['godsname'].'s';

    $from[] = '/JHVHvs/i';
    if ($_SESSION['godsname']=='HERREN')
        $to[] = 'H<small>ERRES</small>';
    elseif ($_SESSION['godsname']=='Herren')
        $to[] = 'Herres';
    else
        $to[] = $_SESSION['godsname'].'s';

    $from[] = '/JHVHv/i';
    if ($_SESSION['godsname']=='HERREN')
        $to[] = 'H<small>ERRE</small>';
    elseif ($_SESSION['godsname']=='Herren')
        $to[] = 'Herre';
    else
        $to[] = $_SESSION['godsname'];

    $from[] = '/JHVH/i';
    if ($_SESSION['godsname']=='HERREN')
        $to[] = 'H<small>ERREN</small>';
    else
        $to[] = $_SESSION['godsname'];


    $from[] = '/HERRENS/';
    if ($_SESSION['godsname']=='HERREN')
        $to[] = 'H<small>ERRENS</small>';
    elseif ($_SESSION['godsname']=='Herren')
        $to[] = 'Herrens';
    else
        $to[] = $_SESSION['godsname'].'s';

    $from[] = '/HERRES/';
    if ($_SESSION['godsname']=='HERREN')
        $to[] = 'H<small>ERRES</small>';
    elseif ($_SESSION['godsname']=='Herren')
        $to[] = 'Herres';
    else
        $to[] = $_SESSION['godsname'].'s';

    $from[] = '/HERREN/';
    if ($_SESSION['godsname']=='HERREN')
        $to[] = 'H<small>ERREN</small>';
    elseif ($_SESSION['godsname']=='Herren')
        $to[] = 'Herren';
    else
        $to[] = $_SESSION['godsname'];

    $from[] = '/HERRE/';
    if ($_SESSION['godsname']=='HERREN')
        $to[] = 'H<small>ERRE</small>';
    elseif ($_SESSION['godsname']=='Herren')
        $to[] = 'Herre';
    else
        $to[] = $_SESSION['godsname'];

    $from[] = '/([^a-z])[vV]([0-9]+)[\n ]*/';
    $to[] = '\1<span class="verseno" data-verse="\2"><span class="chapno">'.$chapter.':</span>\2</span>';

    if ($exegetic_layout) {
        // Handle indentation
        $from[] = '~//(\d+)\s*(.*?)\s*(?=//\d|$)~s';
        $to[] = '<div class="indent" data-indent="\1">\2</div>\3';
    }
    else {
        // Fjern indrykningsmarkør
        $from[] = '~//\d+~';
        $to[] = '';

        $from[] = '/\n *\n/';
        $to[] = 'QQ';

        $from[] = '/\n/';
        $to[] = ' ';

        $from[] = '/QQ/';
        $to[] = "\n";

        $from[] = '/^ *([^\n@]+) *$/m';
        $to[] = '<div class="paragraph">\1</div>';
 
        $from[] = '/@([^@]+)@/';
        $to[] = '<h2>\1</h2>';
    }
 
    $from[] = '/--/';
    $to[] = '&ndash;';
 
    $from[] = '/(\s)-(\s)/';
    $to[] = '\1&ndash;\2';
 
    $from[] = '/\.\.\./';
    $to[] = '…';

    $txt = preg_replace($from, $to, $txt);


    // Generate references

    global $current_verse, $references;
    $current_verse = 0;
    $references = [];

    $txt = preg_replace_callback('/(<span class="verseno" data-verse="([^"]+)">)|(\s*{H: *([^}]+)})/',
                                 function ($matches) {
                                     global $current_verse, $references;
                                     if (!empty($matches[2])) {
                                         $current_verse = $matches[2];
                                         return $matches[0];
                                     }
                                     else {
                                         if (isset($references[$current_verse]))
                                             $references[$current_verse] .= ' ' . $matches[4];
                                         else
                                             $references[$current_verse] = $matches[4];
                                         return '';
                                     }
                                 }, $txt);

    // Generate footnote marks
    
    $txt = preg_replace_callback('/REFALET/',
                                 function ($matches) {
                                     global $nextletter;
                                     return $nextletter++;
                                 }, $txt);

    $txt = preg_replace_callback('/REFANUM/',
                                 function ($matches) {
                                     global $nextnumber;
                                     return $nextnumber++;
                                 }, $txt);

    return  $txt;
  }
