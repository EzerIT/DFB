<?php
require_once('oversigt.inc.php');

class ParserException extends Exception {
    public function __construct($message) {
        parent::__construct($message);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}



class SfmTokenizer {
    public function __construct(string $text) {
        $this->text = $text;
        $this->pos = 0;
    }

    // Get the next token
    public function get_token(): string {
        return $this->get_token2($this->pos);
    }

    // Retrieve the next token, but don't update the current position
    public function peek_token(): string {
        $pos = $this->pos;
        return $this->get_token2($pos);
    }

    private function get_token2(&$pos): string {
        while (true) {
            if ($pos==strlen($this->text))
                return "";
            
            // Use \G instead of ^ because the latter doesn't work with an offset!=0
            // Look for \xx or \+xx or \xx*
            if (preg_match('/\G\\\\\\+?[a-z0-9]+\*?/',$this->text,$matches,0,$pos)) {
                $pos += strlen($matches[0]);
                return $matches[0];
            }

            // Look for sequence of non-backslash and non-space characters
            elseif (preg_match('/\G[^\\\\\s]+/',$this->text,$matches,0,$pos)) {
                $pos += strlen($matches[0]);
                return $matches[0];
            }

            // Look for spaces, but don't return them to caller
            elseif (preg_match('/\G\s+/',$this->text,$matches,0,$pos)) {
                $pos += strlen($matches[0]);
            }
            else
                throw new ParserException("Error in SfmTokenizer");
        }
    }

    private $text;
    private $pos;
}
    


function Xformatref($ref,$endchar,$target_blank) {
    global $deabbrev, $chap;

    $links = '';
    
    $offset = 0;
    while (preg_match('/((([1-5]? )?[A-ZÆØÅ][a-zæøå]+)\s+([0-9]+)(,([0-9]+)(-([0-9]+))?)?)([;\.]\s*)?/',
                      // Matches:
                      // 0: Everything
                      // 1: ((([1-5]? )?[A-ZÆØÅ][a-zæøå]+)\s+([0-9]+),([0-9]+)(-([0-9]+))?)
                      // 2: (([1-5]? )?[A-ZÆØÅ][a-zæøå]+)  - Book
                      // 3: ([1-5]? )?
                      // 4: ([0-9]+)  - Chapter
                      // 5: (,([0-9]+)(-([0-9]+))?)?
                      // 6: ([0-9]+)  - 'From' verse
                      // 7: (-([0-9]+))?
                      // 8: ([0-9]+)  - 'To' verse
                      // 9: ([;\.]\s*)?
                      $ref,
                      $matches,
                      PREG_OFFSET_CAPTURE,
                      $offset)) {

        if (!isset($deabbrev[$matches[2][0]]) || !in_array($matches[4][0],$chap[$deabbrev[$matches[2][0]]]))
            $links .= $matches[0][0];
        else {
            $links .= '<a '
                    . ($target_blank ? 'target="_blank" ' : '')
                    . 'href="show.php?bog='
                    . $deabbrev[$matches[2][0]]
                    . "&kap=" . $matches[4][0];

            if (!empty($matches[6][0])) {
                // 'From' verse is set
                $links .=  "&fra=" . $matches[6][0]
                         . "&til=" . (!empty($matches[8][0]) ? $matches[8][0] : $matches[6][0]);
            }
            
            $links .= '">'
                    . $matches[1][0] . '</a>'
                    . (isset($matches[9]) && !empty($matches[9][0]) ? $matches[9][0] : $endchar);
        }
        $offset = $matches[4][1];
                
    }
    return $links;
}


class FormatSfm {
    private $output = '';       // HTML string is generated here
    public $nextletter = 'a';  // Next footnote letter
    public $nextnumber = 1;    // Next footnote number

    public $title = '';
    public $credit = '';
    public $read_chapter;       // When we're dealing with only one chapter, this is the same as the requested chapter

    public $references = [];
    
    private function finish($building,$buffer) {
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

    public function to_html($filename, $chapter, $from_verse, $to_verse) {
        $txt = file_get_contents($filename);

        if (strstr($txt,"\"")!==false)
            throw new ParserException("Double quotation mark in text");

        $exegetic_layout = preg_match('/\\zei/',$txt) && $_SESSION['exegetic']=='on';


        // Remove following chapters
        $chapter1 = $chapter+1;
        $txt = preg_replace("/\\\\c +${chapter1}[^0-9].*/s",'',$txt);
        
        // Remove preceding chapters, if any
        if ($chapter>1)
            $txt = preg_replace("/\\\\c +1[^0-9].*(\\\\c +${chapter}[^0-9].*)/s",'\1',$txt);

        // TODO:
        //    // Handle verse restriction
        //    if ($from_verse>0)
        //        $txt = preg_replace("/(===[^=]+===).*(v$from_verse )/s",'\1\2',$txt);
        // 
        // 
        //    if ($to_verse>0) {
        //        // Find first verse > $to_verse
        //        $matches=array();
        //        $offset=0;
        //        while ($found = preg_match('/v([0-9]+)/',$txt,$matches,PREG_OFFSET_CAPTURE,$offset)) {
        //            $offset = $matches[1][1];
        //            if (intval($matches[1][0])>intval($to_verse))
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
//    $to[] = '\1<span class="verseno" data-verse="\2"><span class="chapno">'.$chapter.':</span>\2</span>';
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

//    $credit = "";

//$format_sfm = new FormatSfm;
// 
//$format_sfm->to_html($_SERVER['argv'][1], 2, $title, $credit, 0,0);
// 
//echo $format_sfm->output;
