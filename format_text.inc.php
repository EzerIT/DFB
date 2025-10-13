<?php

class FormatText extends Formatter {
    private $fh; // File handle for original language text

    public function to_html() {
        $txt = file_get_contents(sprintf('tekst/%s%03d.txt',$this->book,$this->chapter));

        if (preg_match('/"/',$txt)) {
            echo "<h1>Fejl</h1>\n";
            echo "<p>Tekst indeholder dobbelt citationstegn.</p>\n";
            die;
        }

        // Handle verse restriction
        if ($this->from_verse>0) {
            if (preg_match("/(v$this->from_verse )/s",$txt)) {
                if ($this->syntactic_layout)
                    $txt = preg_replace("~(===[^=]+===)(?:(?!v$this->from_verse ).)*(//[0-9]+).*(v$this->from_verse )~s",
                                        '\1\2\3',$txt);
                else
                    $txt = preg_replace("/(===[^=]+===).*(v$this->from_verse )/s",'\1\2',$txt);
            }
            else {
                global $title;
                $this->title = $title[$this->book] . ", kapitel " . $this->chapter;
                return "Vers $this->from_verse i dette kapitel findes ikke i Den Frie Bibel.";
            }
        }


        if ($this->to_verse>0) {
            if (!preg_match("/(v$this->to_verse )/s",$txt)) {
                if ($this->syntactic_layout)
                    $txt .= "\n//0&nbsp;//0 *[Vers $this->to_verse i dette kapitel findes ikke i Den Frie Bibel.]*\n";
                else
                    $txt .= "\n***\n\n*[Vers $this->to_verse i dette kapitel findes ikke i Den Frie Bibel.]*\n";
            }

            // Find first verse > $this->to_verse
            $matches=array();
            $offset=0;
            while ($found = preg_match('/v([0-9]+)/',$txt,$matches,PREG_OFFSET_CAPTURE,$offset)) {
                $offset = $matches[1][1];
                if (intval($matches[1][0])>intval($this->to_verse))
                    break;
            }
            if ($found) {
                $txt = substr($txt,0,$offset-1);

                // Remove trailing //#
                $txt = preg_replace('|(.*)//[0-9]+\s*$|','\1', $txt);

                
                // Remove a possible final heading
                if (preg_match('/[^=]==[^=]+==\s*$/s',$txt,$matches,PREG_OFFSET_CAPTURE,0)) {
                    $txt = substr($txt,0,$matches[0][1]);
                }
            }
        }
    
        preg_match_all('/!!<(.*)>!!/',$txt,$meta_matches);
        $this->credit = $meta_matches[1];
        $this->format_credits();

        // First collection of substitutions:

        $from[] = "/\u{FEFF}/";  // Byte Order Mark
        $to[] = '';

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
        
        $from[] = '~//([^\d])~';
        $to[] = "//0";
        
        $txt =  preg_replace($from, $to, $txt);


        // Handle footnotes in titles
        if (preg_match('/===(.*)\s*{T: *([^}]+)}===/',$txt,$tit)) {
            $this->title = $tit[1] . '<span class="ref ref1"><span class="refnumhead" data-toggle="tooltip" data-num="1" data-placement="bottom" title="' . $tit[2]. '" data-html="true"></span></span>';
            ++$this->nextnumber;
        }
        elseif (preg_match('/===(.*)\s*{E: *([^}]+)}===/',$txt,$tit)) {
            $this->title = $tit[1] . '<span class="ref refa"><span class="refnumhead" data-toggle="tooltip" data-let="a" data-placement="bottom" title="' . $tit[2] . '" data-html="true">[a]</span></span>';
            ++$this->nextletter;
        }
        else {
            preg_match('/===(.*)===/',$txt,$tit);
            $this->title = $tit[1];
        }


        // Second collection of substitutions:

        $from = array();
        $to = array();
        
        $from[] = '/===(.*)===/';  // Titles have been handled above
        $to[] = '';
        
        if ($this->syntactic_layout) {
            $from[] = '/==(.*)==/';
            $to[] = '';

            $from[] = '/\n/';
            $to[] = ' ';
        }
        else {
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
     
        $from[] = '/JHVHs/';
        if ($_SESSION['godsname']=='HERREN')
            $to[] = 'H<small>ERRENS</small>';
        elseif ($_SESSION['godsname']=='Herren')
            $to[] = 'Herrens';
        else
            $to[] = $_SESSION['godsname'].'s';
     
        $from[] = '/JHVHvs/';
        if ($_SESSION['godsname']=='HERREN')
            $to[] = 'H<small>ERRES</small>';
        elseif ($_SESSION['godsname']=='Herren')
            $to[] = 'Herres';
        else
            $to[] = $_SESSION['godsname'].'s';
     
        $from[] = '/JHVHv/';
        if ($_SESSION['godsname']=='HERREN')
            $to[] = 'H<small>ERRE</small>';
        elseif ($_SESSION['godsname']=='Herren')
            $to[] = 'Herre';
        else
            $to[] = $_SESSION['godsname'];
     
        $from[] = '/JHVH/';
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
        $to[] = '\1<span class="verseno" data-verse="\2"><span class="chapno">'.$this->chapter.':</span>\2</span>';
     
        if (!$this->syntactic_layout) {
            // Remove indentation marker
            $from[] = '~//\d+[\x20\xa0\x09]*\R~m';  // Prevent that marker without text causes subsequent line feed to be removed
            $to[] = '';

            $from[] = '~//\d+\s*~';  // Other markers are simply removed
            $to[] = '';

            $from[] = '/(.)\s+:/';  // Remove space in front of colon
            $to[] = '\1:';

            $from[] = '/\n\s*\n/';
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

        $from[] = '/\$([^\$]*)\$/';
//        if ($_SESSION['markadded']=='on')
//            $to[] = '<span class="added">\1</span>';
//        else
            $to[] = '\1';
 
        $from[] = '/--/';
        $to[] = '&ndash;';
 
        $from[] = '/(\s)-(\s)/';
        $to[] = '\1&ndash;\2';
 
        $from[] = '/\.\.\./';
        $to[] = '…';

        $txt = preg_replace($from, $to, $txt);


        if ($this->syntactic_layout) {
            if ($_SESSION['include_orig_lang']=='on') {
                $orig_file = sprintf('tekst/orig/%s%03d.txt',$this->book,$this->chapter);
                ($this->fh = @fopen($orig_file,'r')) || die("Kan ikke finde filen $orig_file");
                fgets($this->fh); // Skip identification line
                if ($this->from_verse>0) {
                    // Skip lines from original text file
                    do {
                        $pos = ftell($this->fh);
                        $orig_line = fgets($this->fh);
                        $verse = strstr($orig_line,":",true);
                    } while ($verse < $this->from_verse);
                    fseek($this->fh,$pos);
                }
            }
            
            // (Regexp (.*?) matches the minimum number of arbitrary characters
            // (?= indicates a lookahead condition).
            $txt = preg_replace_callback('~//(\d+)\s*(.*?)\s*(?=//\d|$)~s', // A double slash,
                                                                            // digits,
                                                                            // optional spaces,
                                                                            // the minimal possible number of characters,
                                                                            // optional spaces,
                                                                            // "//\d" or end-of-line
                                         function($matches) {
                                             if ($_SESSION['include_orig_lang']=='on') {
                                                 $orig_line = fgets($this->fh);
                                                 $orig_line = substr(strstr($orig_line,' '),1); // Strip verse and indent number and space
                                                 
                                                 return '<div class="textline"><div class="indented-number" data-indent="' . $matches[1] . '">'
                                                      . $matches[1]
                                                      . '<span style="color:transparent;font-size:0pt;">¤</span>'
                                                      . '</div><div class="indented-text" data-indent="' . $matches[1] . '">'

                                                      . $matches[2]
                                                      . '</div></div>'
                                                       .'<div class="textline"><div class="indented-number-blank"></div>'
                                                      . '<div class="indented-text" data-indent="' . $matches[1] . '"><span class="hebrew">'
                                                      . $orig_line
                                                      . '</span></div></div>';
                                             }
                                             else
                                                 return '<div class="textline"><div class="indented-number" data-indent="' . $matches[1] . '">'
                                                      . $matches[1]
                                                      . '<span style="color:transparent;font-size:0pt;">¤</span>'
                                                      . '</div><div class="indented-text" data-indent="' . $matches[1] . '">'
                                                      . $matches[2]
                                                      . '</div></div>';
                                         }, $txt);
        }

        
        // Generate references

        $txt = preg_replace_callback('/\s*{H: *([^}]+)}/',
                                     function ($matches) {
                                         $target = $matches[1];
                                         $dest = htmlspecialchars('<span class="reflinks">'
                                                                . formatref($matches[1])
                                                                . '</span>');
                                         return '<span class="refh" data-refs="' . $dest . '">*</span>';
                                     }, $txt);

        // Generate footnote marks
    
        $txt = preg_replace_callback('/REFALET/',
                                     function ($matches) {
                                         return $this->nextletter++;
                                     }, $txt);

        $txt = preg_replace_callback('/REFANUM/',
                                     function ($matches) {
                                         return $this->nextnumber++;
                                     }, $txt);

        return  $txt;
    }


    public static function replaceit_ordforkl(string $filename) {
        $txt = file_get_contents($filename);

        if (str_ends_with($filename,'.txt')) {
            if (preg_match('/"/',$txt)) {
                echo "<h1>Fejl</h1>\n";
                echo "<p>Tekst indeholder dobbelt citationstegn.</p>\n";
                die;
            }

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
        }
        // If file does not end in ".txt", it is assumed to contain HTML, although the following
        // replacements still take place
     
        $from[] = '/\'/';
        $to[] = '&rsquo;';
     
        $from[] = '/^ *\*\*\* *$/m';
        $to[] = '&nbsp;';
     
        $from[] = '/\$([^\*]+)\$/';
        $to[] = '<b>\1</b>';
     
        $from[] = '/\*([^\*]+)\*/';
        $to[] = '<i>\1</i>';
     
        $from[] = '/JHVHs/';
        if ($_SESSION['godsname']=='HERREN')
            $to[] = 'H<small>ERRENS</small>';
        elseif ($_SESSION['godsname']=='Herren')
            $to[] = 'Herrens';
        else
            $to[] = $_SESSION['godsname'].'s';
     
        $from[] = '/JHVHvs/';
        if ($_SESSION['godsname']=='HERREN')
            $to[] = 'H<small>ERRES</small>';
        elseif ($_SESSION['godsname']=='Herren')
            $to[] = 'Herres';
        else
            $to[] = $_SESSION['godsname'].'s';
     
        $from[] = '/JHVHv/';
        if ($_SESSION['godsname']=='HERREN')
            $to[] = 'H<small>ERRE</small>';
        elseif ($_SESSION['godsname']=='Herren')
            $to[] = 'Herre';
        else
            $to[] = $_SESSION['godsname'];
     
        $from[] = '/JHVH/';
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
     
     
        $from[] = '/--/';
        $to[] = '&ndash;';
     
        $from[] = '/(\s)-(\s)/';
        $to[] = '\1&ndash;\2';
     
        $from[] = '/\.\.\./';
        $to[] = '…';
     
        $txt = preg_replace($from, $to, $txt);
     
        $txt = preg_replace_callback('/{H: *([^}]+)}/',
                                     function ($matches) {
                                         $refs = formatref($matches[1]);
                                         if (str_ends_with($refs,'.')) // Strip final period
                                             $refs = substr($refs,0,-1);
                                         return $refs;
                                     }, $txt);
     
        return '<div class="explain">' . $txt . '</div>';
    }
}
