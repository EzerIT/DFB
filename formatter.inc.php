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
    public $read_chapter;       // When we're dealing with only one chapter, this is the same as the requested chapter

    public $references = [];

    abstract public function to_html();
}    


require_once('format_text.inc.php');
require_once('format_sfm.inc.php');


function make_formatter(bool $use_sfm, string $book, int $chapter, int $from_verse, int $to_verse) {
    if ($use_sfm)
        return new FormatSfm(sprintf('ptx/%s.sfm',$book),$chapter,$from_verse,$to_verse);
    else
        return new FormatText(sprintf('tekst/%s%03d.txt',$book,$chapter),$chapter,$from_verse,$to_verse);
}
        
