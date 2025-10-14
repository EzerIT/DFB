<?php

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


class FormatSfmTex {
    private $output = '';      // LaTeX string is generated here
    private $read_chapter;     // Chapter number read from file
    public $title = '';
    public $chapter;
    public $book;
    public $chtype;

    public function __construct(string $book, int $chapter, string $chtype) {
        $this->book = $book;
        $this->chapter = $chapter;
        $this->chtype = $chtype;
    }

    
    private function finish($building, string $buffer) {
        switch ($building) {
            case 'MT1':
                $this->title = rtrim($buffer) . ", kapitel $this->read_chapter";
                $this->output .= '\needspace{6\baselineskip}\bchap{' . $this->chapter . '}'
                               . '\section{' . $this->chtype . ' ' . $this->chapter . '}';
                break;
            case 'HEADER':
                $this->output .= '\needspace{1\baselineskip}\subsection{' . $buffer . '}';
                break;
            case 'BLANK':
                $this->output .= '\pfbreak';
                break;
            case 'PARAGRAPH':
            case 'POETRY1':
            case 'POETRY2':
                $this->output .= "\n\n$buffer\n";
                break;
            case 'PARAGRAPH1':
                $this->output .= "\n\\noindent\n$buffer\n";
                break;
        }
    }

    public function to_latex() {
        $txt = file_get_contents(sprintf('../sfm/%s.sfm',$this->book));

        if (strstr($txt,"\"")!==false)
            throw new ParserException("Double quotation mark in text");

        // Remove following chapters
        $chapter1 = $this->chapter+1;
        $txt = preg_replace("/\\\\c +{$chapter1}[^0-9].*/s",'',$txt);

        // Remove preceding chapters, if any
        if ($this->chapter>1)
            $txt = preg_replace("/\\\\c +1[^0-9].*(\\\\c +{$this->chapter}[^0-9].*)/s",'\1',$txt);


        // Substitutions:

        $from[] = "/\u{FEFF}/";  // Byte Order Mark
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

        $from[] = '/\\\\zei[ 0-9]+\\\\zei\\*/';
        $to[] = '';

        $from[] = '/(.)\s+:/';  // Remove space in front of colon
        $to[] = '\1:';

        $txt =  preg_replace($from, $to, $txt);


        // A state-event machine handles further processing

        $tokenizer = new SfmTokenizer($txt);

        $buffer = '';
        $building = null;

        $in_footnote = false;
        
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

                case '\Z': // Syntactic indent
                    $this->finish($building,$buffer);
                    $this->synindent = $tokenizer->get_token();
                    $building = 'SYNINDENT';
                    $buffer = '';
                    break;

                case '\c':
                    $this->read_chapter = $tokenizer->get_token();
                    break;

                case '\v':
                    $verseno = $tokenizer->get_token();
                    $buffer .= '\bverse{' . $verseno . '}';
                    break;

                case '\w': // Glossary
                    $word = $tokenizer->get_token();
                    if (!preg_match('/(.*)\|(.*)/',$word,$mat))
                        throw new ParserException("Badly formed glossary string");

                    $buffer .= $mat[1] . ' '; // The explanation is in $mat[2] which is ignored for now

                    if ($tokenizer->get_token()!='\w*')
                        throw new ParserException("\w* not found");
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

                case '\em': // Emphasis text
                case '\+tl': // Transliterated text
                    $buffer .= '\emph{';
                    break;

                case '\em*': // Emphasis text
                case '\+tl*': // Transliterated text ends
                    $buffer .= '}';
                    break;

                case '\f': // Normal footnote
                    if ($in_footnote)
                        throw new ParserException('Footnote within footnote');
                    $buffer = rtrim($buffer) . '\footnote{';
                    if ($tokenizer->get_token()!='+')
                        throw new ParserException('No + after \f');
                    $in_footnote = true;
                    $in_fq = false;
                    break;

                case '\f*': // Normal footnote ends
                    if (!$in_footnote)
                        throw new ParserException('Misplaced \f*');
                    if ($in_fq) {
                        $buffer .= '}';
                        $in_fq = false;
                    }
                    $buffer = rtrim($buffer) . '}';
                    $next_token = $tokenizer->peek_token();
                    if ($next_token!='\f' && $next_token!='\fe')
                        $buffer .= ' ';
                    $in_footnote = false;
                    break;

                case '\fe': // Exegetic footnote
                    if ($in_footnote)
                        throw new ParserException('Footnote within footnote');
                    $buffer = rtrim($buffer) . '\pnote{';
                    if ($tokenizer->get_token()!='+')
                        throw new ParserException('No + after \\fe');
                    $in_footnote = true;
                    $in_fq = false;
                    break;

                case '\fe*': // Exegetic footnote ends
                    if (!$in_footnote)
                        throw new ParserException('Misplaced \fe*');
                    if ($in_fq) {
                        $buffer .= '}';
                        $in_fq = false;
                    }
                    $buffer = rtrim($buffer) . '}';
                    $next_token = $tokenizer->peek_token();
                    if ($next_token!='\f' && $next_token!='\fe')
                        $buffer .= ' ';
                    $in_footnote = false;
                    break;

                case '\fr': // Reference in footnote
                    if (!$in_footnote)
                        throw new ParserException('Misplaced \fr');
                    while (($tok = $tokenizer->peek_token())[0]!='\\')
                        $tokenizer->get_token(); // Ignored, for now
                    break;

                case '\ft': // Start of footnote text
                    if (!$in_footnote)
                        throw new ParserException('Misplaced \ft');
                    if ($in_fq) {
                        $buffer = rtrim($buffer) . ':} ';
                        $in_fq = false;
                    }
                    break;

                case '\fq': // Start footnote quote
                    if (!$in_footnote)
                        throw new ParserException('Misplaced \fq');
                    $in_fq = true;
                    $buffer .= '\emph{';
                    break;
                    
                case '\x': // Start of cross reference
                    if ($tokenizer->get_token()!='+')
                        throw new ParserException('No + after \\x');
                    break;

                case '\xo': // Source for reference
                    while (($tok = $tokenizer->peek_token())!='\xt' && $tok!='\x*')
                        $tokenizer->get_token(); // Ignored, for now
                    break;

                case '\xt':
                    while (($tok = $tokenizer->peek_token())!='\xt' && $tok!='\x*')
                        $tokenizer->get_token(); // Ignored, for now
                    break;

                case '\x*':
                    // Ignored, for now
                    break;

                default:
                    if ($token[0]=='\\') {
                        throw new ParserException("Unknown token '$token'");
                        die;
                    }
                    $buffer .= $token . ' ';
                    break;
            }
        }
        $this->finish($building,$buffer);

        $from = [];
        $to = [];

        $from[] = '/\.\.\./';
        $to[] = '\ldots{}';

        $from[] = '/HERRENS/';
        $to[] = '\\JHVHs{}';

        $from[] = '/HERRES/';
        $to[] = '\\JHVHvs{}';

        $from[] = '/HERREN/';
        $to[] = '\\JHVH{}';
    
        $from[] = '/HERRE/';
        $to[] = '\\JHVHv{}';

        return  preg_replace($from, $to, $this->output);
    }
}
