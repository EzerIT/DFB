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

function formatref($ref,$endchar,$target_blank) {
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


abstract class Formatter {
    public $nextletter = 'a';  // Next footnote letter
    public $nextnumber = 1;    // Next footnote number

    public $title = '';
    public $credit = '';
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


require_once('format_text.inc.php');
require_once('format_sfm.inc.php');


function make_formatter(string $book, int $chapter, int $from_verse, int $to_verse) {
    global $filetype;
    
    switch (is_array($filetype[$book]) ? $filetype[$book][$chapter] : $filetype[$book]) {
        case 'sfm':
            return new FormatSfm($book,$chapter,$from_verse,$to_verse);

        case 'txt':
            return new FormatText($book,$chapter,$from_verse,$to_verse);
    }
}
        
