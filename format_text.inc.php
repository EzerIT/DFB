<?php

class FormatText extends Formatter {
    private $syntactic_layout;  // True if syntactic indentation is available and requested

    public function to_html() {
        $txt = file_get_contents(sprintf('tekst/%s%03d.txt',$this->book,$this->chapter));

        if (preg_match('/"/',$txt)) {
            echo "<h1>Fejl</h1>\n";
            echo "<p>Tekst indeholder dobbelt citationstegn.</p>\n";
            die;
        }

        $this->syntactic_layout = preg_match('~//~',$txt) && $_SESSION['exegetic']=='on';

        // Handle verse restriction
        if ($this->from_verse>0) {
            if (preg_match("/(v$this->from_verse )/s",$txt)) {
                if ($this->syntactic_layout)
                    $txt = preg_replace("|(===[^=]+===).*(//[0-9]+).*(v$this->from_verse )|s",'\1\2\3',$txt);
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

        // Find and cloak emails
        foreach ($this->credit as &$cr)
            if (preg_match('/Oversættelse:\s*(.*)/u',$cr,$name_matches)) {
                $translators = explode(" og ",$name_matches[1]);
                $translator_file = fopen("tekst/translators.txt","r");
                $tr_text = '';

                foreach ($translators as $tr)
                    $has_email[$tr] = false;

                while ($line=fgets($translator_file)) {
                    foreach ($translators as $tr) {
                        if (preg_match("#$tr:(.*)#u",$line,$email_matches)) {
                            $has_email[$tr] = true;
                            if (empty($tr_text))
                                $tr_text = "$tr (" . emailcloak($email_matches[1]) . ')';
                            else
                                $tr_text .= " og $tr (" . emailcloak($email_matches[1]) . ')';
                        }
                    }
                }

                foreach ($translators as $tr) {
                    if (!$has_email[$tr]) {
                        if (empty($tr_text))
                            $tr_text = "$tr ";
                        else
                            $tr_text .= " og $tr ";
                    }
                }

                $cr = "Oversættelse: $tr_text";
            }

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
     
        if ($this->syntactic_layout) {
            // Handle indentation
            //      (Regexp (.*?) matches the minimum number of arbitrary characters
            //       (?= indicates a lookahead condition).
            $from[] = '~//(\d+)\s*(.*?)\s*(?=//\d|$)~s'; // A double slash,
            // digits,
            // optional spaces,
            // the minimal possible number of characters,
            // optional spaces,
            // "//\d" or end-of-line
            $to[] = '<div class="textline"><div class="indented-number" data-indent="\1">\1</div><div class="indented-text" data-indent="\1">\2</div></div>\3';
//            $to[] = '<div class="noindent"><div class="indentno" data-indent="\1">|\1</div>\2</div>\3';
//            $to[] = '<div class="indent" data-indent="\1">QWW\2WWQ</div>\3';  // QWW...WWQ is removed below

            $from[] = '/SHS/';
            $to[] = '<span class="hebrew">';

            $from[] = '/SHE/';
            $to[] = '</span>';
        }
        else {
            // Remove indentation marker
            $from[] = '~//\d+[\x20\xa0\x09]*\R~m';  // Prevent that marker without text causes subsequent line feed to be removed
            $to[] = '';

            $from[] = '~//\d+~';  // Other markers are simply removed
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
