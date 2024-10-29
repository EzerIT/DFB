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

function formatref_simple_aux($book,$chapter,$fromverse,$toverse) {
    global $deabbrev, $chap, $title;

    $lowbook = mb_strtolower($book);
        
    if (!isset($deabbrev[$lowbook]))
        throw new ParserException("Den Frie Bibel kender ikke bogen »{$book}«");
    elseif (!isset($chap[$deabbrev[$lowbook]]) || !in_array($chapter,$chap[$deabbrev[$lowbook]]))
        throw new ParserException("Den Frie Bibel har ikke kapitel $chapter i " . $title[$deabbrev[$lowbook]]);
    else {
        $links = 'show.php?bog='
               . $deabbrev[$lowbook]
               . '&kap=' . $chapter;

        if (!is_null($fromverse)) {
            $links .=  "&fra=" . $fromverse
                     . "&til=" . ($toverse ?? $fromverse);
        }
    }

    return $links;
}

function formatref_simple($ref) {
    $ref = trim($ref);

    // Check for books with one chapter
    if (preg_match('/^((Obad|2\s+Joh|3\s+Joh|Jud)'    // Book (mandatory)
                    . '(\s+([0-9]+)(-([0-9]+))?)?)'   // 'From' and 'to' verse (optional)
                    . '$/ui',                         // Terminator
                      // Matches:
                      //  0: Everything
                      //  1: Book and verses
                      //  2: Book
                      //  3: 'From' and 'to' verse
                      //  4: 'From' verse
                      //  5: Hyphen and 'to' verse
                      //  6: 'To' verse
                      $ref,
                      $matches)) {
        return formatref_simple_aux($matches[2],1,$matches[4] ?? null,$matches[6] ?? null);
    }
    elseif (preg_match('/^([1-5] +)?[a-zæøå]+$/ui',$ref))
        throw new ParserException("Kapitel skal angives");
    elseif (preg_match('/^((([1-5] +)?[a-zæøå]+)'        // Boook (mandatory)
                    . '\s+([0-9]+)'                      // Chapter (mandatory)
                    . '([\s,:]+([0-9]+)(-([0-9]+))?)?)'  // 'From' and 'to' verse (optional)
                    . '$/ui',                            // Terminator
                      // Matches:
                      //  0: Everything
                      //  1: Book and chapter and verses
                      //  2: Book
                      //  3: Book number (e.g. "2" in 2 Sam)
                      //  4: Chapter
                      //  5: 'From' and 'to' verse
                      //  6: 'From' verse
                      //  7: Hyphen and 'to' verse
                      //  8: 'To' verse
                      $ref,
                      $matches)) {
        return formatref_simple_aux($matches[2],$matches[4],$matches[6] ?? null, $matches[8] ?? null);
    }
    else
        throw new ParserException('»' . $ref . '« er i et ulovligt format.');
}

mb_internal_encoding('UTF-8');

foreach ($abbrev as $k => $v)
    $deabbrev[mb_strtolower($v)] = $k;



try {
    $l = formatref_simple($_GET['find']);
    header("Location: $l");
}
catch (ParserException $e) {
    require_once('head.inc.php');

    makeheadstart('Fejl i søgning');
    makeheadend();
    makemenus(-1);
?>
    <script>
         $(function() {
             $('#bible-find-form').on('submit', function() {
                 window.location.href="lookup.php?find="+encodeURIComponent($('#bibleref').val());
                 return false;
             });
         });
    </script>
    
    <div class="container">
        <div class="row justify-content-center">

            <div class="col">
                <div class="card mt-4">
                    <h1 class="card-header bg-danger text-light">Fejl i søgning</h1>
                    <div class="card-body">
                        <p><?= $e->getMessage() ?></p>
                    </div>
                </div>
                <div class="card mt-4">
                    <h1 class="card-header bg-success text-light">Find bibelsted</h1>
                    <div class="card-body">
                        <form id="bible-find-form">
                            <label for="bibleref">Indtast et bibelsted som du gerne finde:</label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="bibleref" placeholder="Fx: 2 Mos 3,5-6">
                                <div class="input-group-append">
                                    <input class="btn btn-outline-secondary" type="submit" value="Find" id="bible-find">
                                </div>
                            </div>
                        </form>
                        <p>En liste over forkortelser findes <a href="#" onclick="$('#bibleBooksModal').modal()">her</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
 <?php
}

require_once('booklist.inc.php');
