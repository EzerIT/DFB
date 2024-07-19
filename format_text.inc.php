<?php

class FormatText extends Formatter {
    private $exegetic_layout;  // True if exegetic indentation is available and requested

    public function to_html() {
        $txt = file_get_contents(sprintf('tekst/%s%03d.txt',$this->book,$this->chapter));

        if (preg_match('/"/',$txt)) {
            echo "<h1>Fejl</h1>\n";
            echo "<p>Tekst indeholder dobbelt citationstegn.</p>\n";
            die;
        }

        $this->exegetic_layout = preg_match('~//~',$txt) && $_SESSION['exegetic']=='on';

        // Handle verse restriction
        if ($this->from_verse>0) {
            if (preg_match("/(v$this->from_verse )/s",$txt))
                $txt = preg_replace("/(===[^=]+===).*(v$this->from_verse )/s",'\1\2',$txt);
            else {
                global $title;
                $this->title = $title[$this->book] . ", kapitel " . $this->chapter;
                return "Vers $this->from_verse i dette kapitel findes ikke i Den Frie Bibel.";
            }
        }


        if ($this->to_verse>0) {
            if (!preg_match("/(v$this->to_verse )/s",$txt)) {
                if ($this->exegetic_layout)
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
        
        if ($this->exegetic_layout) {
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
     
        if ($this->exegetic_layout) {
            // Handle indentation
            //      (Regexp (.*?) matches the minimum number of arbitrary characters
            //       (?= indicates a lookahead condition).
            $from[] = '~//(\d+)\s*(.*?)\s*(?=//\d|$)~s'; // A double slash,
            // digits,
            // optional spaces,
            // the minimal possible number of characters,
            // optional spaces,
            // "//\d" or end-of-line
            $to[] = '<div class="indent" data-indent="\1">QWW\2WWQ</div>\3';  // QWW...WWQ is removed below

            $from[] = '/QWWWWQ/'; // Empty text ...
            $to[] = '~';          // ... is replaced with tilde

            $from[] = '/(QWW|WWQ)/'; // Other occurrences of QWW or WWQ ...
            $to[] = '';              // ... are removed
        }
        else {
            // Fjern indrykningsmarkør
            $from[] = '~//\d+~';
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
        if ($_SESSION['markadded']=='on')
            $to[] = '<span class="added">\1</span>';
        else
            $to[] = '\1';
 
        $from[] = '/--/';
        $to[] = '&ndash;';
 
        $from[] = '/(\s)-(\s)/';
        $to[] = '\1&ndash;\2';
 
        $from[] = '/\.\.\./';
        $to[] = '…';

        $txt = preg_replace($from, $to, $txt);


        // Generate references

        global $current_verse;
        $current_verse = 0;

        $txt = preg_replace_callback('/(<span class="verseno" data-verse="([^"]+)">)|(\s*{H: *([^}]+)})/',
                                     function ($matches) {
                                         global $current_verse;
                                         if (!empty($matches[2])) {
                                             $current_verse = $matches[2];
                                             return $matches[0];
                                         }
                                         else {
                                             if (isset($this->references[$current_verse]))
                                                 $this->references[$current_verse] .= ' ' . $matches[4];
                                             else
                                                 $this->references[$current_verse] = $matches[4];
                                             return '';
                                         }
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

        if (substr($filename, -strlen('.txt'))==='.txt') { // PHP 8: str_ends_with($filename,'.txt')
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
     
        $txt = preg_replace_callback('/\s*{H: *([^}]+)}/',
                                     function ($matches) {
                                         return formatref($matches[1],'',true);
                                     }, $txt);
     
        return '<div class="explain">' . $txt . '</div>';
    }
}
