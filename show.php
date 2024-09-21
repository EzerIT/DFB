<?php
  // TODO: Tjek at $book er lovlig, så det bliver vanskeligere at misbruge fx bog="../../../../etc/passwd"
  // Hacket slipper ikke igennem i øjeblikket, men det er mere held end forstand.


require_once('head.inc.php');
require_once('setdefault.inc.php');
require_once('formatter.inc.php');


// $minchap: Minimum chapter number
// $maxchap: Maximum chapter number
// $nextchap: Next chapter number, or -1
// $prevchap: Previous chapter number, or -1
$minchap = array();
$maxchap = array();
$nextchap = array();
$prevchap = array();

foreach ($chap as $book => $chaps) {
    $minchap[$book] = min($chaps); 
    $maxchap[$book] = max($chaps); 
    $nextchap[$book] = array();
    $prevchap[$book] = array();

    $lastchap = -1;
    foreach ($chaps as $ch) {
        if ($lastchap!=-1)
            $nextchap[$book][$lastchap] = $ch;
        $prevchap[$book][$ch] = $lastchap;
        $lastchap = $ch;
    }
    $nextchap[$book][$lastchap] = -1;
}

function pagination($book, $kapitel) {
    global $chaptype, $chap, $style;

    $outline_style = is_array($style[$book]) ? ($style[$book][$kapitel]??'') : $style[$book];
    $outline_style = str_replace('btn','btn-outline',$outline_style);

    echo "  <div style=\"margin-left: auto; margin-right: auto;\">\n";

    $chcount = count($chap[$book]);
    for ($chix=0; $chix < $chcount; ++$chix) {
        $chno = $chap[$book][$chix];
        echo "    <a href=\"show.php?bog=$book&kap=$chno\" "
            . "class=\"mt-1 mb-1 ml-0 mr-0 btn chap-btn "
            . ($chno==$kapitel ? $outline_style : (is_array($style[$book]) ? $style[$book][$chno] : $style[$book]))
            . "\">$chno</a>\n";
    }
    echo "  </div>\n";
}




if (!isset($_GET['bog']) || !isset($_GET['kap']) || !is_numeric($_GET['kap'])) {
    echo "<pre>Forkerte parametre</pre>";
    die;
}
$kap = intval($_GET['kap']);
$bog = $_GET['bog'];
$fra = isset($_GET['fra']) && is_numeric($_GET['fra']) ?  intval($_GET['fra']) : 0;
$til = isset($_GET['til']) && is_numeric($_GET['til']) ?  intval($_GET['til']) : 0;


makeheadstart($abbrev[$bog] . ' ' . $kap, true);
?>
    <style>
    .bibletext, .biblenotes, .reflinks {
        font-family: <?= $allfonts[$_SESSION['font']] ?>;
        font-size: <?= $_SESSION['fontsize'] ?>%;
    }

    span.verseno {
        vertical-align: super;
        font-size: 0.625em;
    }

    h2 {
        font-size: 1.125em;
        font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
        text-transform: none;
    }

    h2 small { /* Used for "ERREN" in "HERREN" */
      font-size: 85%;
      color: #333333;
      font-weight: 700;
    }


    /* Taken in part from XKCD */
    /* the reference tooltips style starts here */

    .ref {
        position: relative;
        vertical-align: baseline;
    }

    .refnum, .refnumhead {
        position: relative;
        left: 2px;
        bottom: 1ex;
        color: #005994;
        font-size: .7em;
        font-weight: bold;
        text-decoration: underline;
        text-transform: lowercase;
        cursor: pointer;
    }

    .tooltip-inner {
        text-align: left;
        max-width: 70%;
        min-width: 100px;
        font-size: <?= $_SESSION['fontsize'] ?>%;
     }
     
    div.paragraph {
        text-indent: 2em;
        display: block;
    }

    div.poetry1 {
        margin-left: 7em;
        text-indent: -5em;
        display: block;
    }

    div.poetry2 {
        margin-left: 7em;
        text-indent: -3em;
        display: block;
    }
    </style>

    <script>

     let maxindent = 0; // The maximum indentation level of the text
     
     function do_indent() {
         let pixels_per_space = $('#tenspaces').width()/10;

         // Make indentation match maxindent; however, don't indent more than 40 pixels per level.
         // By dividing by maxindent+4 rather than maxindent below, we leave a litte room for the tex.
         let pixels_per_indent = Math.min(Math.round($(".bibletext").width()/(maxindent+4)),40); 
         
         $(".indentspaces").remove(); // Remove old indentation, if any
         $('.indent').each(function(i) {
             let indentpixels = $(this).data('indent')*pixels_per_indent;
             let indentspaces = Math.round(indentpixels/pixels_per_space);
             let nbsps = '';
             while (indentspaces>=10) {
                 nbsps += "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                 indentspaces -= 10;
             }
             // 6 is the length of "&nbsp;"
             nbsps += "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".substring(0,indentspaces*6);

             $(this).prepend('<span class="indentspaces">' + nbsps + '</span>');
             $(this).css('margin-left',indentpixels + 'px')
                    .css('text-indent',(-indentpixels) + 'px');
         })
     }

     
    $(function() {
            <?php if ($_SESSION['showverse']=='on'): ?>
                $('.verseno').show();
            <?php else: ?>
                $('.verseno').hide();
            <?php endif; ?>

            <?php if ($_SESSION['showchap']=='on'): ?>
                $('.chapno').show();
            <?php else: ?>
                $('.chapno').hide();
            <?php endif; ?>

            <?php if ($_SESSION['showh2']=='on'): ?>
                $('h2').show();
                $('.paragraph1').css('text-indent','0');
            <?php else: ?>
                $('h2').hide();
                <?php if ($_SESSION['showh2']=='off'): ?>
                    $('.paragraph1:first-of-type').css('text-indent','0');
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($_SESSION['showfna']=='on'): ?>
                $('.refa').show();
            <?php else: ?>
                $('.refa').hide();
            <?php endif; ?>

            <?php if ($_SESSION['showfn1']=='on'): ?>
                $('.ref1, .explain, .refh').show();
            <?php else: ?>
                $('.ref1, .explain, .refh').hide();
            <?php endif; ?>

            <?php if ($_SESSION['oneline']=='on' && $_SESSION['exegetic']=='off'): ?>
                $('.paragraph').css('display','inline');
                $('.poetry').css('display','inline').css('margin-left','0');
                $('.verseno').before('<br class="versebreak">');
            <?php endif; ?>

            <?php if ($_SESSION['linespace']=='on'): ?>
                $('.paragraph').css('line-height','2');
                $('.poetry').css('line-height','2');
                $('.indent').css('line-height','2');
            <?php endif; ?>

         <?php if ($_SESSION['showfna']=='on'): ?>
         $('[data-let]').each(function( index ) {
             $(this).text("[" + $(this).data('let') + "]");
             <?php if ($_SESSION['showfnblock']=='on'): ?>
             $('#footnotes').append("<b>" + $(this).data('let') + ":</b> " + $(this).attr('title') + "<br>");
             <?php endif; ?>
         });
         <?php endif; ?>
         
         <?php if ($_SESSION['showfn1']=='on'): ?>
         $('[data-num]').each(function( index ) {
             $(this).text("[" + $(this).data('num') + "]");
             <?php if ($_SESSION['showfnblock']=='on'): ?>
             $('#footnotes').append("<b>" + $(this).data('num') + ":</b> " + $(this).attr('title') + "<br>");
             <?php endif; ?>
         });
         <?php endif; ?>
         
         $('[data-toggle="tooltip"]').tooltip({trigger:'hover focus'});

         // Find maxindent
         $(".indent").each(function() {
             thisindent = $(this).data("indent");
             if (thisindent>maxindent)
                 maxindent = thisindent
         });
         do_indent();
         $(window).resize(do_indent);

         // Handler for Bible references
         $('.refh').on('click', function() {
             $('#ref-body').html($(this).data('refs'));
             $('#ref-dialog').modal('show');
         });

         // Decloak email addresses
         $('.mangemail').each(function() {
             email = $(this).html()
                            .replace(/SNABEL/,'@')
                            .replace(/PRIK/g,'.')
                            .replace(/NR13RN/g,'r')
                            .replace(/NR5RN/g,'e');
             $(this).html('<a href="mailto:' + email + '">' + email + '</a>');
         });
     });


    </script>

<?php
makeheadend();
makemenus(null);
?>

    <div class="container">
      <div class="row">
        <div class="col-lg-9 col-xl-8">
            <?php
            $formatter = make_formatter($bog, $kap, $fra, $til);
            $text = $formatter->to_html();
            ?>
          <div class="card mt-4">
              <h1 class="card-header bg-warning"><?= $formatter->title ?></h1>
            <div class="card-body bibletext">
                <?= $text ?>
                <span id="tenspaces">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
            </div>
          </div>
        </div>

        <?php if (isset($chap[$bog])): ?>
            <!-- Chapter chooser displayed at right for size lg and xl -->
            <div class="d-none d-lg-block col-lg-3 col-xl-4">
                <div class="card mt-4">
                    <h1 class="card-header bg-info text-light">Vælg <?= $chaptype[$bog] ?></h1>
                    <div class="card-body pl-xl-4 pl-lg-1 pr-0">
                        <?php pagination($bog,$kap); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
      </div>

      <?php
      $show_note = $_SESSION['showfnblock']=='on'
                && ($_SESSION['showfna']=='on' || $_SESSION['showfn1']=='on')
                && ($formatter->nextnumber>1 || $formatter->nextletter>'a');
      ?>
      
      <div class="row">
          <?php if ($show_note): ?>
              <div class="offset-xl-1 col-xl-6
                          offset-lg-1 col-lg-7
                          offset-md-2 col-md-8
                          offset-sm-1 col-sm-10">
                  <div class="card mt-3">
                      <h1 class="card-header bg-info text-light">Fodnoter</h1>
                      <div class="card-body biblenotes">
                          <small id="footnotes">
                          </small>
                      </div>
                  </div>
              </div>
          <?php endif; ?>
      </div>

      
      <?php if (isset($chap[$bog])): ?>
          <!-- Chapter chooser displayed at bottom for size xs, sm, and md -->
          <div class="row justify-content-center d-flex d-lg-none">
              <div class="col-sm-8 col-md-6">
                  <div class="card mt-3">
                      <h1 class="card-header bg-info text-light">Vælg <?= $chaptype[$bog] ?></h1>
                      <div class="card-body pl-1 pl-sm-3 pr-0">
                          <?php pagination($bog,$kap); ?>
                      </div>
                  </div>
              </div>
          </div>
      <?php endif; ?>

      <div class="row">
        <div class="offset-xl-2 col-xl-4
                    offset-lg-2 col-lg-5
                    offset-md-3 col-md-6
                    offset-sm-2 col-sm-8">
            <div class="card mt-3">
                <h1 class="card-header bg-info text-light">Status for dette kapitel</h1>
                <div class="card-body biblenotes">
                    <?php foreach ($formatter->credit as $c): ?>
                        <small><?= preg_replace('/Modenhed:/', '<a href="modenhed.php">Modenhed</a>:', $c) ?></small><br>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
      </div>

    </div><!--End of container-->

    <!-- For debugging window sizes:
    <div class="container">
        <p class="       d-block     d-sm-none ">Window size: XS</p>
        <p class="d-none d-sm-block  d-md-none ">Window size: SM</p>
        <p class="d-none d-md-block  d-lg-none ">Window size: MD</p>
        <p class="d-none d-lg-block  d-xl-none ">Window size: LG</p>
        <p class="d-none d-xl-block  d-xxl-none">Window size: XL</p>
        <p class="d-none d-xxl-block           ">Window size: XXL</p>
    </div>
    -->

    <!-- Dialog for displaying Bible reference information -->
    <div class="modal fade" id="ref-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header justify-content-between">
                    <div><h4 class="modal-title" id="ref-label">Henvisning</h4></div>
                    <div><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
                </div>
                <div class="modal-body" id="ref-body">
          
                </div>
            </div>
        </div>
    </div>

    
<?php
endbody();
