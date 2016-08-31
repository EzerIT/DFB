<?php
require_once('head.inc.php');
require_once('setdefault.inc.php');
require_once('replaceit.inc.php');
require_once('oversigt.inc.php');

define('CHAP_PER_LINE', 7);


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

function pagination($book, $kapitel, $chap_per_line) {
global $chaptype, $chap, $style;
?>
<nav>
  <table style="margin-left: auto; margin-right: auto;">
    <?php $chcount = count($chap[$book]); ?>
    <?php $chix = -1; ?>
    <?php for ($i=0; $i<$chcount; $i+=$chap_per_line): ?>
      <tr>
        <?php for ($j=0; $j<$chap_per_line; ++$j): ?>
          <?php if (++$chix < $chcount): ?>
            <?php $chno = $chap[$book][$chix]; ?>
              <td><a style="width:100%" href="show.php?bog=<?= $book ?>&kap=<?= $chno ?>"
                     class="btn <?= $chno==$kapitel ? 'btn-default' : (is_array($style[$book]) ? $style[$book][$chno] : $style[$book]) ?>"><?= $chno ?></a></td>
          <?php else: ?>
            <td></td>
          <?php endif; ?>
        <?php endfor; ?>
      </tr>
    <?php endfor; ?>
  </table>
</nav>
 
<?php

}

if (!isset($_GET['bog']) || !isset($minchap[$_GET['bog']]) || !isset($_GET['kap']) || !is_numeric($_GET['kap'])) {
    echo "<pre>Forkerte parametre</pre>";
    die;
}
$kap = intval($_GET['kap']);
$bog = $_GET['bog'];

makeheadstart($title[$bog] . ' &ndash; ' . ucfirst($chaptype[$bog]) . ' ' . $kap, true);
?>
    <style type="text/css">
    .bibletext {
        font-family: <?= $allfonts[$_SESSION['font']] ?>;
        font-size: 110%;
    }

    span.verseno {
        vertical-align: super;
        font-size: x-small;
    }

    h2 {
        font-size: large;
        font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
        text-transform: none;
    }

    h2 small {
      font-size: 75%;
      color: #333333;
      font-weight: 700;
    }


    /* Taken in part from XKCD */
    /* the reference tooltips style starts here */

    .ref {
        position: relative;
        vertical-align: baseline;
    }

    .refnum {
        position: relative;
        left: 2px;
        bottom: 1ex;
        color: #005994;
        font-size: .7em;
        font-weight: bold;
        text-decoration: underline;
        cursor: pointer;
    }

    .refbody {
        text-indent: 0;
        font-size: small;
        font-weight: normal;
        line-height: 1.1;
        display: block;
        border: 1px solid;
        border-radius: 4px;
        padding: 5px;
        background-color: lightblue;
    }

    div.paragraph {
        text-indent: 2em;
        display: block;
    }

    </style>

    <script>
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
            <?php else: ?>
                $('h2').hide();
            <?php endif; ?>

            <?php if ($_SESSION['showfna']=='on'): ?>
                $('.refa').show();
            <?php else: ?>
                $('.refa').hide();
            <?php endif; ?>

            <?php if ($_SESSION['showfn1']=='on'): ?>
                $('.ref1').show();
            <?php else: ?>
                $('.ref1').hide();
            <?php endif; ?>

            <?php if ($_SESSION['oneline']=='on'): ?>
                $('.paragraph').css('display','inline');
                $('.verseno').before('<br class="versebreak">');
            <?php endif; ?>

            <?php if ($_SESSION['godsname']=='HERREN'): ?>
                $('.thename').html('H<small>ERREN</small>');
                $('.thenames').html('H<small>ERRENS</small>');
                $('.thenamev').html('H<small>ERRE</small>');
                $('.thenamevs').html('H<small>ERRES</small>');
            <?php elseif ($_SESSION['godsname']=='Herren'): ?>
              $('.thename').text('Herren');
              $('.thenames').text('Herrens');
              $('.thenamev').text('Herre');
              $('.thenamevs').text('Herres');
            <?php else: ?>
              $('.thename').text('<?= $_SESSION['godsname'] ?>');
              $('.thenames').text('<?= $_SESSION['godsname'].'s' ?>');
              $('.thenamev').text('<?= $_SESSION['godsname'] ?>');
              $('.thenamevs').text('<?= $_SESSION['godsname'].'s' ?>');
            <?php endif; ?>


        $(".refbody").hide();
        $(".refnum").click(function(event) {
            $(this.nextSibling).toggle();
            event.stopPropagation();
        });
        $("body").click(function(event) {
            $(".refbody").hide();
        });



    });
    </script>

<?php
makeheadend();
makemenus(null);
?>

    <div class="container">

      <div class="row">
        <div class="col-md-9 col-lg-8">
          <?php $text = replaceit(sprintf('tekst/%s%03d.txt',$bog,$kap), $kap, $heading, $credit); ?>
          <div class="panel panel-warning">
            <div class="panel-heading">
              <h1 class="panel-title"><?= $heading ?></h1>
            </div>
            <div class="panel-body bibletext">
              <?= $text ?>
            </div>
          </div>
        </div>

        <div class="hidden-md 
                    col-sm-offset-3 col-sm-6
                    col-lg-offset-0 col-lg-4
                    text-center">
          <div class="panel panel-info">
            <div class="panel-heading">
              <h1 class="panel-title">Vælg <?= $chaptype[$bog] ?></h1>
            </div>
            <div class="panel-body">
              <?php pagination($bog,$kap,7); ?>
            </div>
          </div>
        </div>

        <div class="visible-md-block col-md-3 text-center">
          <div class="panel panel-info">
            <div class="panel-heading">
              <h1 class="panel-title">Vælg <?= $chaptype[$bog] ?></h1>
            </div>
            <div class="panel-body">
              <?php pagination($bog,$kap,4); ?>
            </div>
          </div>
        </div>

      </div>

      <div class="row">
        <div class="col-lg-offset-2 col-lg-4
                    col-md-offset-2 col-md-5
                    col-sm-offset-3 col-sm-6">
          <div class="panel panel-info">
            <div class="panel-heading">
              <h3 class="panel-title">Status for dette kapitel</h3>
            </div>
            <div class="panel-body">
              <?php foreach ($credit as $c): ?>
                <small><?= preg_replace('/Modenhed:/', '<a href="modenhed.php">Modenhed</a>:', $c) ?></small><br>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>


    </div><!--End of container-fluid-->

<?php
endbody();
