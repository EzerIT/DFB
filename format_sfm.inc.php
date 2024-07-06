<?php

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

    // Get the token at position $pos. Update $pos to next token.
    private function get_token2(&$pos): string {
        while (true) {
            if ($pos==strlen($this->text))
                return "";

            // Use \G instead of ^ because the latter doesn't work with an offset!=0
            // Look for \xx or \+xx or \xx* (we include \Z, which is a pseudo code added by this class
            if (preg_match('/\G\\\\\\+?[Za-z0-9]+\*?/',$this->text,$matches,0,$pos)) {
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
                throw new ParserException("Error in SfmTokenizer. Text is '" . substr($this->text,$this->pos) . "'");
        }
    }

    private $text;
    private $pos;
}


class FormatSfm extends Formatter {
    private $output = '';      // HTML string is generated here
    private $exegetic_layout;  // True if exegetic indentation is available and requested
    private $exindent;         // Exegetic indentation value
    private $read_chapter;     // Chapter number read from file

    private function finish($building, string $buffer) {
        switch ($building) {
            case 'MT1':
                $this->title = rtrim($buffer) . ", kapitel $this->read_chapter";
                break;
            case 'HEADER':
                if (!$this->exegetic_layout)
                    $this->output .= "<h2>$buffer</h2>\n";
                break;
            case 'BLANK':
                $this->output .= "<div class=\"paragraph\">&nbsp;</div>\n";
                break;
            case 'PARAGRAPH':
                $this->output .= "<div class=\"paragraph\">$buffer</div>\n";
                break;
            case 'PARAGRAPH1':
                $this->output .= "<div class=\"paragraph paragraph1\">$buffer</div>\n";
                break;
            case 'POETRY1':
                $this->output .= "<div class=\"poetry poetry1\">$buffer</div>\n";
                break;
            case 'POETRY2':
                $this->output .= "<div class=\"poetry poetry2\">$buffer</div>\n";
                break;
            case 'EXINDENT':
                if (empty($buffer))
                    $buffer = '~';
                $this->output .= "<div class=\"indent\" data-indent=\"$this->exindent\">$buffer</div>";
                break;
        }
    }

    public function to_html() {
        $txt = file_get_contents(sprintf('tekst/%s.sfm',$this->book));

        if (strstr($txt,"\"")!==false)
            throw new ParserException("Double quotation mark in text");

        $this->exegetic_layout = preg_match('/\\\\zei/',$txt) && $_SESSION['exegetic']=='on';

        // Remove following chapters
        $chapter1 = $this->chapter+1;
        $txt = preg_replace("/\\\\c +${chapter1}[^0-9].*/s",'',$txt);

        // Remove preceding chapters, if any
        if ($this->chapter>1)
            $txt = preg_replace("/\\\\c +1[^0-9].*(\\\\c +{$this->chapter}[^0-9].*)/s",'\1',$txt);

        // Handle verse restriction
        if ($this->from_verse>0) {
            if ($this->exegetic_layout)
                $txt = preg_replace("/(\\\\c +{$this->chapter})\s.*(\\\\v +{$this->from_verse} )/s",'\1 \Z 0 \2',$txt);
            else
                $txt = preg_replace("/(\\\\c +{$this->chapter})\s.*(\\\\v +{$this->from_verse} )/s",'\1 \m \2',$txt);
        }

        if ($this->to_verse>0) {
            // Find first verse > $this->to_verse
            $matches=array();
            $offset=0;
            while ($found = preg_match('/\\\\v +([0-9]+)/',$txt,$matches,PREG_OFFSET_CAPTURE,$offset)) {
                    $offset = $matches[1][1];
                    if (intval($matches[1][0])>intval($this->to_verse))
                        break;
                }
                if ($found) {
                    $txt = substr($txt,0,$matches[0][1]);

                    // Remove trailing \zei...\zei*, \m, \p, and \q
                    $txt = preg_replace(['/(.*)(\\\\zei\s+[0-9]+\s*\\\\zei\\*)\s*$/s',
                                         '/(.*)(\\\\[mpq][0-9]?)\s*$/s'],
                                        ['\1','\1'],
                                        $txt);
                    
                    // Remove a possible final heading
                    // Search for last \s or \m, \p, or \q. If it is \s, the previous verse ends in a heading.
                    if (preg_match('/.*\\\\([smpq])/s',$txt,$matches,PREG_OFFSET_CAPTURE,0) && $matches[1][0]=='s')
                        $txt = substr($txt,0,$matches[1][1]-1);
                }
        }

        // Read credits
        if (file_exists(sprintf('tekst/%s%03d.cre',$this->book,$this->chapter))) {
            preg_match_all('/!!<(.*)>!!/',
                           file_get_contents(sprintf('tekst/%s%03d.cre',$this->book,$this->chapter)),
                           $meta_matches);
            $this->credit = $meta_matches[1];
        }
        elseif (file_exists(sprintf('tekst/%s.cre',$this->book))) {
            preg_match_all('/!!<(.*)>!!/',
                           file_get_contents(sprintf('tekst/%s.cre',$this->book)),
                           $meta_matches);
            $this->credit = $meta_matches[1];
        }
        else
            $this->credit = ["Ingen statusoplysninger."];

        // Substitutions:

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


        if ($this->exegetic_layout) {
            $from[] = '/\\\\[pmq][0-9]*\s+/';  // Remove \p, \m, \q, and \q1, \q2...
            $to[] = '';

            $from[] = '/\\\\zei\s*([0-9]+)\s*\\\\zei\\*/'; // Remove \zei...\zei*
            $to[] = '\\Z \1';
        }
        else {
            $from[] = '/\\\\zei[ 0-9]+\\\\zei\\*/';
            $to[] = '';

            $from[] = '/(.)\s+:/';  // Remove space in front of colon
            $to[] = '\1:';
        }

        $txt =  preg_replace($from, $to, $txt);


        // A state-event machine handles further processing

        $tokenizer = new SfmTokenizer($txt);

        $buffer = '';
        $building = null;

        // Markers:

        while (($token = $tokenizer->get_token())!=='') {
            switch ($token) {
                    // Paragraph starters
                case '\id':
                    $this->finish($building,$buffer);
                    $building = 'ID';
                    $buffer = '';
                    break;

                case '\toc':
                case '\toc1':
                    $this->finish($building,$buffer);
                    $building = 'TOC1';
                    $buffer = '';
                    break;

                case '\mt':
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

                case '\b':
                    $this->finish($building,$buffer);
                    $building = 'BLANK';
                    $buffer = '';
                    break;

                case '\q':
                case '\q1':
                    $this->finish($building,$buffer);
                    $building = 'POETRY1';
                    $buffer = '';
                    break;

                case '\q2':
                    $this->finish($building,$buffer);
                    $building = 'POETRY2';
                    $buffer = '';
                    break;

                case '\Z': // Exegetic indent
                    $this->finish($building,$buffer);
                    $this->exindent = $tokenizer->get_token();
                    $building = 'EXINDENT';
                    $buffer = '';
                    break;

                case '\c':
                    $this->read_chapter = $tokenizer->get_token();
                    break;

                    // Modifiers
                case '\v':
                    $verseno = $tokenizer->get_token();
                    $buffer .= "<span class=\"verseno\" data-verse=\"$verseno\">"
                             . "<span class=\"chapno\">$this->read_chapter:</span>$verseno</span>";
                    break;

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

                case '\add':
                    if ($_SESSION['markadded']=='on')
                        $buffer .= '<span class="added">';
                    break;

                case '\add*':
                    if ($_SESSION['markadded']=='on') {
                        if ($buffer[-1]==' ')
                            $buffer = rtrim($buffer) . '</span> ';
                        else
                            $buffer .= '</span>';
                    }
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

                default:
                    $buffer .= $token . ' ';
                    break;
            }
        }
        $this->finish($building,$buffer);
        return $this->output;
    }
}
