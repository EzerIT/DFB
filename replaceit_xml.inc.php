<?php
class replaceit_XML {
    // What to look for:
    static private $book = null;
    static private $chapter = null;
    static private $from_verse = 0;
    static private $to_verse = 0;


    // Collected:
    static private $to_print = '';


    // Auxiliary info:
    static private $collect = '';       // Currently collected text
    static private $in_chapter = false; // True if we are currently in a chapter
    static private $collecting = 0;     // 0 = not collecting, 1 = not collecting but in correct chapter, 2 = collecting
    static private $refnum = 0;         // Footnote number

    
    static private function tagStart($parser, $tagname, $attributes = null) {
        global $title;
        
        switch ($tagname) {
          case 'title':
                self::$collect = '';
                break;

          case 'chapter':
                if (isset($attributes['sID'])) {
                    $c = explode('.',$attributes['sID']);
                    self::$in_chapter = true;

                    if ($c[0]==self::$book && $c[1]==self::$chapter) {
                        self::$collecting = self::$from_verse==0 ? 2 : 1;
                    }
                    else
                        self::$collecting = 0;
                }
                else if (isset($attributes['eID'])) {
                    self::$in_chapter = false;
                }
            
                break;
            
          case 'verse':
                if (isset($attributes['sID'])) {
                    $v = explode('.',$attributes['sID']);
                    if (self::$collecting==1 && $v[2]==self::$from_verse) {
                        self::$collecting = 2;
                        self::$to_print .= "<div class=\"paragraph\">"; // Simulate <p>
                    }

                    if (self::$collecting==2) {
                        self::$to_print .= '<span class="verseno"><span class="chapno">'.$v[1].':</span>'.$v[2].'</span>';
                    }
                    self::$collect = '';
                }
                else if (isset($attributes['eID'])) {
                    $v = explode('.',$attributes['eID']);
                    if (self::$collecting==2) {
                        self::$to_print .= self::$collect."\n";

                        if ($v[2]==self::$to_verse) {
                            self::$collecting = 0;
                            self::$to_print .= "</div>"; // Simulate </p>
                        }
                    }
                }
                break;

          case 'note':
                if (self::$collecting==2) {
                    self::$collect .= "<span class=\"ref ref1\"><span class=\"refnum\">[" . ++self::$refnum . "]</span><span class=\"refbody\">";
                }
                break;

          case 'p':
                if (self::$collecting==2)
                    self::$to_print .= "<div class=\"paragraph\">";
                break;
        }
    }

    static private function tagEnd($parser, $tagname) {
        switch ($tagname) {
          case 'title':
                if (self::$in_chapter) {
                    if (self::$collecting==2)
                        self::$to_print .= "<h2>" . self::$collect . "</h2>\n";
                }
                self::$collect = '';
                break;

          case 'p':
                if (self::$collecting==2)
                    self::$to_print .= "</div>";
                break;

          case 'note':
                if (self::$collecting==2) {
                    self::$collect .= '</span></span>';
                }
                break;
        }
    }

    static private function tagContent($parser, string $content) {


        self::$collect .= $content;
    }


    static public function replaceit($filename, $book, $chapter, $from_verse, $to_verse) {
        self::$book = $book;
        self::$chapter = $chapter;
        self::$from_verse = $from_verse; 
        self::$to_verse = $to_verse;
    
        $parser = xml_parser_create('UTF-8');

//        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 0);
        xml_set_element_handler($parser, 'self::tagStart', 'self::tagEnd');
        xml_set_character_data_handler($parser, 'self::tagContent');

        $xml=file_get_contents($filename);

        xml_parse($parser, $xml);

        xml_parser_free($parser);
        return str_replace("'","&rsquo;",self::$to_print);
    }
  }
