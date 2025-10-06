<?php

// Convert old markup format to USFM

require_once('../oversigt.inc.php');

function replaceitsfm(array $filenames) {
    global $ignore_zei;
    
    $txt = '';

    foreach ($filenames as $file)
        $txt .= file_get_contents($file);

    if (preg_match('/"/',$txt))
        die("FEJL: Tekst indeholder dobbelt citationstegn.\n");

    if (preg_match('/===.*(apitel|alme).*{[ET]:\s*[^}]+}===/', $txt)) {
        echo "FEJL: Tekst indeholder fodnoter i kapiteloverskrifter.\n";
        echo "      Fodnoterne er fjernet.\n";

        $txt = preg_replace('/(===.*(apitel|alme).*){[ET]:\s*[^}]+}(===)/',
                            '\1\3',
                            $txt);
    }
    
//    if (preg_match('/^\s*==[^=]*{[ET]:[^=]*==\s*$/m', $txt))
//        die("FEJL: Tekst indeholder fodnoter i afsnitsoverskrifter.\n");

    if (preg_match('/{([^ETHN]|.[^:])[^}]*/', $txt,$matches)) {
        print_r($matches);
        die("FEJL: Tekst indeholder ulovlig {...}\n");
    }

    // Handling of asterisks in the input must come before any replacement generating asterisks
    $txt = preg_replace('/^ *\*\*\* *$/m', '\\b', $txt);
    $txt = preg_replace('/\*([^\*}]+)\*/', '\\+tl \1\\+tl*', $txt);

    // Generate \toc and \mt1 only for the first chapter
    $txt = preg_replace('/===(.*),.*(apitel|alme) (.*)===/',
                        '\\toc1 \1' . PHP_EOL . '\\mt1 \1' . PHP_EOL . '\\c \3',
                        $txt,
                        1 /*limit*/);

    global $thischapter;
    $thischapter = 1;
    
    $txt = preg_replace_callback(
        // Handle chapter and verse numbers and footnotes and references
        '/' .
        '===.*(apitel|alme) (.*)===' . // Chapter number (match 1 and 2)
        '|'.
        '([^a-z])[vV]([0-9]+)[\n\r ]*' .  // Verse number (match 3 and 4)
        '|'.
        '\s*{(E|T|H):\s*([^}]+)}'. // Footnote or reference (match 5 and 6)
        '/',
        function ($match) {
            global $thisverse, $thischapter;
            if ($match[2]) { // We have a chapter number
                $thischapter = $match[2];
                return '\\c ' . $match[2] . PHP_EOL . '\\p ';
            }
            elseif ($match[4]) { // We have a verse number
                $thisverse = $match[4];
                return $match[3] . ' \\v ' . $match[4] . ' ';
            }
            elseif ($match[5]) { // We have a footnote or reference
                switch ($match[5]) {
                    case 'E':
                        return '\\fe + \\fr ' . "$thischapter,$thisverse" . ' \\ft ' . $match[6] . '\\fe* ';
                    case 'T':
                        return '\\f + \\fr '  . "$thischapter,$thisverse" . ' \\ft ' . $match[6] . '\\f* ';
                    case 'H':
                        return '\\x + \\xo '  . "$thischapter,$thisverse" . ' \\xt ' . $match[6] . '\\x* ';
                }
            }
            else
                return "ERROR";
        },
        $txt
        );
        
    $from[] = '/(!!<.*>!!)/';
    $to[] = '';
    
//    $from[] = '/===.*(apitel|alme) (.*){E:\s*([^}]+)}===/';
//    $to[] = '\\c \2 \\p\\fe + \\ft \3\\fe*' . PHP_EOL . '\\p ';
//    
//    $from[] = '/===.*(apitel|alme) (.*){T:\s*([^}]+)}===/';
//    $to[] = '\\c \2 \\p\\f + \\ft \3\\f*' . PHP_EOL . '\\p ';
//    
//    $from[] = '/===.*(apitel|alme) (.*)===/';
//    $to[] = '\\c \2' . PHP_EOL . '\\p ';

    $from[] = '/==(.*)==/';
    $to[] = PHP_EOL . '\\s \1' . PHP_EOL . '\\p ';

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
 
    $from[] = '/(\w+[,.:;!?]?)\s*{N:\s*([^}]+)}/u';
    $to[] = '\\w \1|\2\\w*';

    $from[] = '/JHVHs/';
    $to[] = 'HERRENS';
 
    $from[] = '/JHVHvs/';
    $to[] = 'HERRES';
 
    $from[] = '/JHVHv/';
    $to[] = 'HERRE';
 
    $from[] = '/JHVH/';
    $to[] = 'HERREN';
    
    $from[] = '/\$([^\$]*)\$/';
    $to[] = '\\add \1\add* ';
 
    $from[] = '~//(\d*) *~';
    $to[] = $ignore_zei ? '' : ' \\zei \1\\zei* '; // We add a space before \\zei because Paratext's punctuation test does not like
                                                   // a \\zei immediately following a punctuation mark
 
    $from[] = '/\R *\R/';
    $to[] = "\n\\\\p ";
 
    $from[] = '/\*\*\*/';
    $to[] = '\\b ';

    $from[] = '/(\\\\p[ \n\r]*)+(\\\\[spc])/';  // Replace multiple \p followed by \p, \s, or \c with just the final marker
    $to[] = "\\2";

    $from[] = '/(\\\\p[ \n\r]*)+(\\\\toc)/';  // Replace multiple \p followed by \toc with just the final marker
    $to[] = "\\2";

    $from[] = '/\\\\p[ \n\r]*\\\\p/';  // Replace \p\p with \p
    $to[] = '\\p';

    $from[] = '/\\\\p[ \n\r]*\\\\s/';  // Replace \p\s with \s
    $to[] = "\\s";

    $from[] = '/\\\\p[ \n\r]*\\\\b/';  // Replace \p\b with \b
    $to[] = '\\b';
    
    $txt = preg_replace($from, $to, $txt);

    // Remove duplicate and trailing spaces

    $txt = preg_replace(['/  +/','/ +\R/'],[' ',"\n"],$txt);
    
    return  $txt;
  }

if ($_SERVER['argc']<=3) {
    fwrite(STDERR,"brug: php replaceitsfm.php [-n] BOOK <outputfile> <inputfile>...\n");
    exit(1);
}

if ($_SERVER['argv'][1] === '-n') {
    $ignore_zei = true;
    $aix = 2;
}
else {
    $ignore_zei = false;
    $aix = 1;
}

$outputfile = fopen($_SERVER['argv'][$aix+1], "w") or die("Unable to open output file!");

fwrite($outputfile, '\id ' . $_SERVER['argv'][$aix] . " - Den Frie Bibel (version: 2025-09-11)\n");

$text = replaceitsfm(array_slice($_SERVER['argv'],$aix+2));

fwrite($outputfile, $text);
fclose($outputfile);
