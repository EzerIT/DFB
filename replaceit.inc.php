<?php

function replaceit($filename, $chapter, &$title, &$credit, $from_verse, $to_verse) {
    $txt = file_get_contents($filename);

    if (preg_match('/"/',$txt)) {
        echo "<h1>Fejl</h1>\n";
        echo "<p>Tekst indeholder dobbelt citationstegn.</p>\n";
        die;
    }

    // Handle verse restriction
    if ($from_verse>0)
        $txt = preg_replace("/(===[^=]+===).*(v$from_verse )/s",'\1\2',$txt);


    if ($to_verse>0) {
        // Find first verse > $to_verse
        $matches=array();
        $offset=0;
        while ($found = preg_match('/v([0-9]+)/',$txt,$matches,PREG_OFFSET_CAPTURE,$offset)) {
            $offset = $matches[1][1];
            if (intval($matches[1][0]>$to_verse))
                break;
            
        }
        if ($found)
            $txt = substr($txt,0,$offset-1);
    }
    
    global $nextletter;
    $nextletter = 'a';
    global $nextnumber;
    $nextnumber = 1;

    preg_match_all('/!!<(.*)>!!/',$txt,$meta_matches);
    $credit = $meta_matches[1];

    $from[] = '/!!<.*>!!/';
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

    $from = array();
    $to = array();
    
    
    if (preg_match('/===(.*)\s*{T: *([^}]+)}===/',$txt,$tit)) {
        $title = $tit[1] . '<span class="ref ref1"><span class="refnumhead">[1]</span></span>';
        $refbodyhead = '<div class="paragraph"><span class="refbodyhead">' . $tit[2] . '</span></div>';
        ++$nextnumber;
    }
    elseif (preg_match('/===(.*)\s*{E: *([^}]+)}===/',$txt,$tit)) {
        $title = $tit[1] . '<span class="ref refa"><span class="refnumhead">[a]</span></span>';
        $refbodyhead = '<div class="paragraph"><span class="refbodyhead">' . $tit[2] . '</span></div>';
        ++$nextletter;
    }
    else {
        preg_match('/===(.*)===/',$txt,$tit);
        $title = $tit[1];
        $refbodyhead = '';
    }

    $from[] = '/===(.*)===/';
    $to[] = '';

    $from[] = '/^\s*==/m';
    $to[] = "\n@";

    $from[] = '/==\s*$/m';
    $to[] = "@\n";

    $from[] = '/\s*{E: *([^}]+)}/';
    $to[] = '<span class="ref refa"><span class="refnum">[REFALET]</span><span class="refbody">\1</span></span>';

    $from[] = '/\s*{T: *([^}]+)}/';
    $to[] = '<span class="ref ref1"><span class="refnum">[REFANUM]</span><span class="refbody">\1</span></span>';

    $from[] = '/\s*{K: *([^}]+)}/';
    $to[] = '';

    $from[] = '/JHVHs/i';
    $to[] = '<span class="thenames"></span>';

    $from[] = '/JHVHvs/i';
    $to[] = '<span class="thenamevs"></span>';

    $from[] = '/JHVHv/i';
    $to[] = '<span class="thenamev"></span>';

    $from[] = '/JHVH/i';
    $to[] = '<span class="thename"></span>';


    $from[] = '/HERRENS/';
    $to[] = '<span class="thenames"></span>';

    $from[] = '/HERRES/';
    $to[] = '<span class="thenamevs"></span>';

    $from[] = '/HERRE/';
    $to[] = '<span class="thenamev"></span>';

    $from[] = '/HERREN/';
    $to[] = '<span class="thename"></span>';

    $from[] = '/([^a-z])[vV]([0-9]+)[\n ]*/';
    $to[] = '\1<span class="verseno"><span class="chapno">'.$chapter.':</span>\2</span>';

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
 
    $from[] = '/--/';
    $to[] = '&ndash;';
 
    $from[] = '/(\s)-(\s)/';
    $to[] = '\1&ndash;\2';
 
    $from[] = '/\.\.\./';
    $to[] = '…';

    $txt = $refbodyhead . preg_replace($from, $to, $txt);

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
