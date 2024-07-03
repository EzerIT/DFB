<?php

class FormatSfm extends Formatter {
    private $output = '';       // HTML string is generated here
    private $filename;
    private $chapter;
    private $from_verse;
    private $to_verse;


    public function __construct(string $filename, int $chapter, int $from_verse, int $to_verse) {
        $this->filename = $filename;
        $this->chapter = $chapter;
        $this->from_verse = $from_verse;
        $this->to_verse = $to_verse;
    }
    
    private function finish($building, string $buffer) {
        switch ($building) {
            case 'MT1':
                $this->title = $buffer;
                break;
            case 'HEADER':
                $this->output .= "<h2>$buffer</h2>\n";
                break;
            case 'PARAGRAPH':
                $this->output .= "<div class=\"paragraph\">$buffer</div>\n";
                break;
            case 'PARAGRAPH1':
                $this->output .= "<div class=\"paragraph paragraph1\">$buffer</div>\n";
                break;
        }
    }

    public function to_html() {
        $txt = file_get_contents($this->filename);

        if (strstr($txt,"\"")!==false)
            throw new ParserException("Double quotation mark in text");

        $exegetic_layout = preg_match('/\\zei/',$txt) && $_SESSION['exegetic']=='on';


        // Remove following chapters
        $chapter1 = $this->chapter+1;
        $txt = preg_replace("/\\\\c +${chapter1}[^0-9].*/s",'',$txt);
        
        // Remove preceding chapters, if any
        if ($this->chapter>1)
            $txt = preg_replace("/\\\\c +1[^0-9].*(\\\\c +{$this->chapter}[^0-9].*)/s",'\1',$txt);

        // TODO:
        //    // Handle verse restriction
        //    if ($this->from_verse>0)
        //        $txt = preg_replace("/(===[^=]+===).*(v$this->from_verse )/s",'\1\2',$txt);
        // 
        // 
        //    if ($this->to_verse>0) {
        //        // Find first verse > $this->to_verse
        //        $matches=array();
        //        $offset=0;
        //        while ($found = preg_match('/v([0-9]+)/',$txt,$matches,PREG_OFFSET_CAPTURE,$offset)) {
        //            $offset = $matches[1][1];
        //            if (intval($matches[1][0])>intval($this->to_verse))
        //                break;
        //        }
        //        if ($found) {
        //            $txt = substr($txt,0,$offset-1);
        // 
        //            // Remove a possible final heading
        //            if (preg_match('/[^=]==[^=]+==\s*$/',$txt,$matches,PREG_OFFSET_CAPTURE,0)) {
        //                $txt = substr($txt,0,$matches[0][1]);
        //            }
        //        }
        //    }
        

        // TODO:
        //    preg_match_all('/!!<(.*)>!!/',$txt,$meta_matches);
        //    $credit = $meta_matches[1];


        // First, a collection of substitutions:

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
        
        $from[] = '/--/';
        $to[] = '&ndash;';
        
        $from[] = '/(\s)-(\s)/';
        $to[] = '\1&ndash;\2';
        
        $from[] = '/\.\.\./';
        $to[] = '…';
        
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
        

        if (!$exegetic_layout) {
            $from[] = '/\\\\zei[ 0-9]+\\\\zei\\*/';
            $to[] = '';
        }
        
        $txt =  preg_replace($from, $to, $txt);
        
        
        // Second, a state-event machine
        
        $tokenizer = new SfmTokenizer($txt);
        
        $buffer = '';
        $building = null;
        
        // Markers:
        // \s \w \add \add* \zei \zei* \p \b \m \q1 \q2 \b
        
        while (($token = $tokenizer->get_token())!=='') {
            switch ($token) {
                    // Paragraph starters
                case '\id':
                    $this->finish($building,$buffer);
                    $building = 'ID';
                    $buffer = '';
                    break;
                    
                case '\toc1':
                    $this->finish($building,$buffer);
                    $building = 'TOC1';
                    $buffer = '';
                    break;
                    
                case '\mt1':
                    $this->finish($building,$buffer);
                    $building = 'MT1';
                    $buffer = '';
                    break;
                case '\s':
                    $this->finish($building,$buffer);
                    $building = 'HEADER';
                    $buffer = '';
                    break;
                case '\p':
                    $this->finish($building,$buffer);
                    $building = 'PARAGRAPH';
                    $buffer = '';
                    break;
                case '\m':
                    $this->finish($building,$buffer);
                    $building = 'PARAGRAPH1';
                    $buffer = '';
                    break;
                case '\c':
                    $this->read_chapter = $tokenizer->get_token();
                    break;

                    // Modifiers
                case '\v':
                    $verseno = $tokenizer->get_token();
                    $buffer .= "\n<span class=\"verseno\" data-verse=\"$verseno\"><span class=\"chapno\">$this->read_chapter:</span>$verseno</span>";
                    break;

//                case '\zei':
//                    $tokenizer->get_token(); // Indentation
//                    if ($tokenizer->get_token()!='\zei*')
//                        throw new ParserException("\zei* not found");
//                    break;

                case '\w': // Glossary
                    $word = $tokenizer->get_token();
                    if (!preg_match('/(.*)\|(.*)/',$word,$mat))
                        throw new ParserException("Badly formed glossary string");

                    $buffer .= "$mat[1]<a class=\"explain\" href=\"ordforklaring.php?ord=$mat[2]\">°</a>";

                    if ($tokenizer->get_token()!='\w*')
                        throw new ParserException("\w* not found");

                    $next_token = $tokenizer->peek_token();
                    if (!empty($next_token) && IntlChar::isalnum($next_token[0]))
                        $buffer .= ' ';
                    
                    break;

                case '\+tl': // Transliterated text
                    $buffer .= '<i>';
                    break;

                case '\+tl*': // Transliterated text ends
                    $buffer .= '</i>';
                    break;

                case '\f': // Normal footnote
                    $buffer = rtrim($buffer) . '<span class="ref ref1"><span class="refnum" data-toggle="tooltip" data-num="'
                             . $this->nextnumber++
                              . '" data-placement="bottom" title="';
                    if ($tokenizer->get_token()!='+')
                        throw new ParserException('No + after \\f');
                    break;

                case '\f*': // Normal footnote ends
                    $buffer = rtrim($buffer) . '" data-html="true"></span></span>';
                    $next_token = $tokenizer->peek_token();
                    if (!empty($next_token) && IntlChar::isalnum($next_token[0]))
                        $buffer .= ' ';
                    break;

                case '\fe': // Exegetic footnote
                    $buffer = rtrim($buffer) . '<span class="ref refa"><span class="refnum" data-toggle="tooltip" data-let="'
                             . $this->nextletter++
                               . '" data-placement="bottom" title="';
                    if ($tokenizer->get_token()!='+')
                        throw new ParserException('No + after \\fe');
                    break;

                case '\fe*': // Exegetic footnote ends
                    $buffer = rtrim($buffer) . '" data-html="true"></span></span>';
                    $next_token = $tokenizer->peek_token();
                    if (!empty($next_token) && IntlChar::isalnum($next_token[0]))
                        $buffer .= ' ';
                    break;

                case '\fr': // Reference in footnote
                    while (($tok = $tokenizer->peek_token())!='\ft' && $tok!='\fe*' && $tok!='\f*')
                        $tokenizer->get_token(); // Ignored, for now
                    break;

                case '\ft': // Start of footnote text
                    break;

                case '\x': // Start of cross reference
                    if ($tokenizer->get_token()!='+')
                        throw new ParserException('No + after \\x');
                    break;

                case '\xo': // Source for reference
                    $source_verse = preg_replace('/.*,([0-9]+)/','\1',$tokenizer->get_token());
                    break;

                case '\xt':
                    $this->references[$source_verse] = '';
                    while (($tok = $tokenizer->get_token())!='\x*')
                        $this->references[$source_verse] .= $tok . ' ';
                    break;

                    
//    $txt = preg_replace_callback('/(<span class="verseno" data-verse="([^"]+)">)|(\s*{H: *([^}]+)})/',
//                                 function ($matches) {
//                                     global $current_verse, $references;
//                                     if (!empty($matches[2])) {
//                                         $current_verse = $matches[2];
//                                         return $matches[0];
//                                     }
//                                     else {
//                                         if (isset($references[$current_verse]))
//                                             $references[$current_verse] .= ' ' . $matches[4];
//                                         else
//                                             $references[$current_verse] = $matches[4];
//                                         return '';
//                                     }
//                                 }, $txt);


                    
                default:
                    $buffer .= $token . ' ';
                    break;
            }
        }
        $this->finish($building,$buffer);
        return $this->output;
    }
}



//if (false) {
// 
//    
// 
//    
//    // Second collection of substitutions:
// 
//    $from = array();
//    $to = array();
//    
//    $from[] = '/===(.*)===/';  // Titles have been handled above
//    $to[] = '';
// 
//    if ($exegetic_layout) {
//        $from[] = '/==(.*)==/';
//        $to[] = '';
// 
//        $from[] = '/\n/';
//        $to[] = ' ';
//    }
//    else{
//        $from[] = '/^\s*==/m';
//        $to[] = "\n@";
// 
//        $from[] = '/==\s*$/m';
//        $to[] = "@\n";
//    }
// 
//    $from[] = '/\s*{E: *([^}]+)}/';
//    $to[] = '<span class="ref refa"><span class="refnum" data-toggle="tooltip" data-let="REFALET" data-placement="bottom" title="\1" data-html="true"></span></span>';
// 
//    $from[] = '/\s*{T: *([^}]+)}/';
//    $to[] = '<span class="ref ref1"><span class="refnum" data-toggle="tooltip" data-num="REFANUM" data-placement="bottom" title="\1" data-html="true"></span></span>';
// 
//    $from[] = '/\s*{K: *([^}]+)}/';
//    $to[] = '';
// 
//    $from[] = '/\s*{N: *([^}]+)}/';
//    $to[] = '<a class="explain" href="ordforklaring.php?ord=\1">°</a>';
// 
// 
// 
// 
//    $from[] = '/([^a-z])[vV]([0-9]+)[\n ]*/';
//    $to[] = '\1<span class="verseno" data-verse="\2"><span class="chapno">'.$this->chapter.':</span>\2</span>';
// 
//    if ($exegetic_layout) {
//        // Handle indentation
//        //      (Regexp (.*?) matches the minimum number of arbitrary characters
//        //       (?= indicates a lookahead condition).
//        $from[] = '~//(\d+)\s*(.*?)\s*(?=//\d|$)~s'; // A double slash,
//                                                     // digits,
//                                                     // optional spaces,
//                                                     // the minimal possible number of characters,
//                                                     // optional spaces,
//                                                     // "//\d" or end-of-line
//        $to[] = '<div class="indent" data-indent="\1">QWW\2WWQ</div>\3';  // QWW...WWQ is removed below
// 
//        $from[] = '/QWWWWQ/'; // Empty text ...
//        $to[] = '~';          // ... is replaced with tilde
// 
//        $from[] = '/(QWW|WWQ)/'; // Other occurrences of QWW or WWQ ...
//        $to[] = '';              // ... are removed
//    }
//    else {
//        // Fjern indrykningsmarkør
//        $from[] = '~//\d+~';
//        $to[] = '';
// 
//        $from[] = '/(.)\s+:/';  // Remove space in front of colon
//        $to[] = '\1:';
// 
//        $from[] = '/\n\s*\n/';
//        $to[] = 'QQ';
// 
//        $from[] = '/\n/';
//        $to[] = ' ';
// 
//        $from[] = '/QQ/';
//        $to[] = "\n";
// 
//        $from[] = '/^ *([^\n@]+) *$/m';
//        $to[] = '<div class="paragraph">\1</div>';
// 
//        $from[] = '/@([^@]+)@/';
//        $to[] = '<h2>\1</h2>';
//    }
// 
//    $from[] = '/\$([^\$]*)\$/';
//    if ($_SESSION['markadded']=='on')
//        $to[] = '<span class="added">\1</span>';
//    else
//        $to[] = '\1';
// 
// 
//    $txt = preg_replace($from, $to, $txt);
// 
// 
//    // Generate references
// 
//    global $current_verse, $references;
//    $current_verse = 0;
//    $references = [];
// 
//    $txt = preg_replace_callback('/(<span class="verseno" data-verse="([^"]+)">)|(\s*{H: *([^}]+)})/',
//                                 function ($matches) {
//                                     global $current_verse, $references;
//                                     if (!empty($matches[2])) {
//                                         $current_verse = $matches[2];
//                                         return $matches[0];
//                                     }
//                                     else {
//                                         if (isset($references[$current_verse]))
//                                             $references[$current_verse] .= ' ' . $matches[4];
//                                         else
//                                             $references[$current_verse] = $matches[4];
//                                         return '';
//                                     }
//                                 }, $txt);
// 
//    // Generate footnote marks
//    
//    $txt = preg_replace_callback('/REFALET/',
//                                 function ($matches) {
//                                     global $nextletter;
//                                     return $nextletter++;
//                                 }, $txt);
// 
//    $txt = preg_replace_callback('/REFANUM/',
//                                 function ($matches) {
//                                     global $nextnumber;
//                                     return $nextnumber++;
//                                 }, $txt);
// 
//    return  $txt;
//  }


//function replaceit_ordforkl($filename) {
//    $txt = file_get_contents($filename);
// 
//    if (substr($filename, -strlen('.txt'))==='.txt') { // PHP 8: str_ends_with($filename,'.txt')
//        if (strstr($txt,"\"")!==false) {
//            echo "<h1>Fejl</h1>\n";
//            echo "<p>Tekst indeholder dobbelt citationstegn.</p>\n";
//            die;
//        }
// 
//        $from[] = '/>>>/';
//        $to[] = '»›';
// 
//        $from[] = '/<<</';
//        $to[] = '‹«';
// 
//        $from[] = '/>>/';
//        $to[] = '»';
// 
//        $from[] = '/<</';
//        $to[] = '«';
// 
//        $from[] = '/>/';
//        $to[] = '›';
// 
//        $from[] = '/</';
//        $to[] = '‹';
//    }
//    // If file does not end in ".txt", it is assumed to contain HTML, although the following
//    // replacements still take place
// 
//    $from[] = '/\'/';
//    $to[] = '&rsquo;';
// 
//    $from[] = '/^ *\*\*\* *$/m';
//    $to[] = '&nbsp;';
// 
//    $from[] = '/\$([^\*]+)\$/';
//    $to[] = '<b>\1</b>';
// 
//    $from[] = '/\*([^\*]+)\*/';
//    $to[] = '<i>\1</i>';
// 
//    $from[] = '/JHVHs/';
//    if ($_SESSION['godsname']=='HERREN')
//        $to[] = 'H<small>ERRENS</small>';
//    elseif ($_SESSION['godsname']=='Herren')
//        $to[] = 'Herrens';
//    else
//        $to[] = $_SESSION['godsname'].'s';
// 
//    $from[] = '/JHVHvs/';
//    if ($_SESSION['godsname']=='HERREN')
//        $to[] = 'H<small>ERRES</small>';
//    elseif ($_SESSION['godsname']=='Herren')
//        $to[] = 'Herres';
//    else
//        $to[] = $_SESSION['godsname'].'s';
// 
//    $from[] = '/JHVHv/';
//    if ($_SESSION['godsname']=='HERREN')
//        $to[] = 'H<small>ERRE</small>';
//    elseif ($_SESSION['godsname']=='Herren')
//        $to[] = 'Herre';
//    else
//        $to[] = $_SESSION['godsname'];
// 
//    $from[] = '/JHVH/';
//    if ($_SESSION['godsname']=='HERREN')
//        $to[] = 'H<small>ERREN</small>';
//    else
//        $to[] = $_SESSION['godsname'];
// 
// 
//    $from[] = '/HERRENS/';
//    if ($_SESSION['godsname']=='HERREN')
//        $to[] = 'H<small>ERRENS</small>';
//    elseif ($_SESSION['godsname']=='Herren')
//        $to[] = 'Herrens';
//    else
//        $to[] = $_SESSION['godsname'].'s';
// 
//    $from[] = '/HERRES/';
//    if ($_SESSION['godsname']=='HERREN')
//        $to[] = 'H<small>ERRES</small>';
//    elseif ($_SESSION['godsname']=='Herren')
//        $to[] = 'Herres';
//    else
//        $to[] = $_SESSION['godsname'].'s';
// 
//    $from[] = '/HERREN/';
//    if ($_SESSION['godsname']=='HERREN')
//        $to[] = 'H<small>ERREN</small>';
//    elseif ($_SESSION['godsname']=='Herren')
//        $to[] = 'Herren';
//    else
//        $to[] = $_SESSION['godsname'];
// 
//    $from[] = '/HERRE/';
//    if ($_SESSION['godsname']=='HERREN')
//        $to[] = 'H<small>ERRE</small>';
//    elseif ($_SESSION['godsname']=='Herren')
//        $to[] = 'Herre';
//    else
//        $to[] = $_SESSION['godsname'];
// 
// 
//    $from[] = '/--/';
//    $to[] = '&ndash;';
// 
//    $from[] = '/(\s)-(\s)/';
//    $to[] = '\1&ndash;\2';
// 
//    $from[] = '/\.\.\./';
//    $to[] = '…';
// 
//    $txt = preg_replace($from, $to, $txt);
// 
//    $txt = preg_replace_callback('/\s*{H: *([^}]+)}/',
//                                 function ($matches) {
//                                     return formatref($matches[1],'',true);
//                                 }, $txt);
// 
//    return '<div class="explain">' . $txt . '</div>';
//}
//}



