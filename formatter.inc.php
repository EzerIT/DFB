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

function emailcloak($email) {
    $em2 = str_replace(['@','.','e','r'],['SNABEL','PRIK','NR5RN','NR13RN'],$email);
    return "<span class=\"mangemail\">$em2</span>";
}



function formatref($ref) {
    global $deabbrev, $chap;

    $links = '';
    $book = '';
    $chapter = 0;
    $offset = 0;
    $ref .= '.'; // Make sure string is properly termiated

    while (preg_match('/((([1-5] )?[A-ZÆØÅ][a-zæøå]+)?'  // Boook (optional)
                    . '\s+([0-9]+)'                      // Chapter (mandatory)
                    . '(,([0-9]+)(-([0-9]+))?)?)'        // 'From' and 'to' verse (optional)
                    . '(\.([0-9]+)(-([0-9]+))?)?'        // Additional 'from' and 'to' verse (optional)
                    . '(\.([0-9]+)(-([0-9]+))?)?'        // Additional 'from' and 'to' verse (optional)
                    . '([;\.]\s*)/u',                    // Terminator (mandatory)
                      // Matches:
                      //  0: Everything
                      //  1: Book and chapter and first set of verses
                      //  2: Book
                      //  3: Book number (e.g. "2" in 2 Sam)
                      //  4: Chapter
                      //  5: 'From' and 'to' verse
                      //  6: 'From' verse
                      //  7: Hyphen and 'to' verse
                      //  8: 'To' verse
                      //  9: Period and second 'from' and 'to' verse
                      // 10: Second 'from' verse
                      // 11: Hyphen and second 'to' verse
                      // 12: Second 'to' verse
                      // 13: Period and third 'from' and 'to' verse
                      // 14: Third 'from' verse
                      // 15: Hyphen and third 'to' verse
                      // 16: Third 'to' verse
                      // 17: Terminator
                      $ref,
                      $matches,
                      PREG_OFFSET_CAPTURE,
                      $offset)) {

        if ($matches[2][1]!=-1)
            $book = trim($matches[2][0]);

        if (!isset($deabbrev[$book]) || !isset($chap[$deabbrev[$book]]) || !in_array($matches[4][0],$chap[$deabbrev[$book]]))
            $links .= $matches[0][0];
        else {
            $chapter = $matches[4][0];
            $links .= '<a target="_blank" '
                    . 'href="show.php?bog='
                    . $deabbrev[$book]
                    . "&kap=" . $chapter;

            if (!empty($matches[6][0])) {
                // 'From' verse is set
                $links .=  "&fra=" . $matches[6][0]
                         . "&til=" . (!empty($matches[8][0]) ? $matches[8][0] : $matches[6][0]);
            }
            
            $links .= '">'
                    . $matches[1][0] . '</a>';

            foreach ([10,14] as $extra) {
                if (!empty($matches[$extra][0])) {
                    // Second or third' from' verse is set
                    $links .= '.<a target="_blank" '
                            . 'href="show.php?bog='
                            . $deabbrev[$book]
                            . "&kap=" . $chapter
                            . "&fra=" . $matches[$extra][0]
                            . "&til=" . (!empty($matches[$extra+2][0]) ? $matches[$extra+2][0] : $matches[$extra][0])
                            . '">'
                            . $matches[$extra][0]
                            . $matches[$extra+1][0]
                            . '</a>';
                }
            }

            $links .= $matches[17][0];
        }
        $offset = $matches[17][1];
    }
    return $links;
}


abstract class Formatter {
    public $nextletter = 'a';  // Next footnote letter
    public $nextnumber = 1;    // Next footnote number

    public $title = '';
    public $credit = [];
    public $references = [];

    protected $book;
    protected $chapter;
    protected $from_verse;
    protected $to_verse;

    public function __construct(string $book, int $chapter, int $from_verse, int $to_verse) {
        $this->book = $book;
        $this->chapter = $chapter;
        $this->from_verse = $from_verse;
        $this->to_verse = $to_verse;
    }

    abstract public function to_html();
}    

// Formatter for non-exitant chapter
class FormatNull extends Formatter {
    public function to_html() {
        $this->title = "Fejl";
        $this->credit = ["Kapitel findes ikke"];
        return "Den angivne bibeltekst findes ikke i Den Frie Bibel.";
    }
}

require_once('format_text.inc.php');
require_once('format_sfm.inc.php');


function make_formatter(string $book, int $chapter, int $from_verse, int $to_verse) {
    global $filetype, $chap;

    if (!isset($chap[$book]) || !in_array($chapter,$chap[$book]))
        return new FormatNull($book,$chapter,$from_verse,$to_verse);
    
    switch (is_array($filetype[$book]) ? $filetype[$book][$chapter] : $filetype[$book]) {
        case 'sfm':
            return new FormatSfm($book,$chapter,$from_verse,$to_verse);

        case 'txt':
            return new FormatText($book,$chapter,$from_verse,$to_verse);
    }
}
        
