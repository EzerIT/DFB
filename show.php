<?php
require_once('head.inc.php');
require_once('setdefault.inc.php');
require_once('replaceit.inc.php');

// $minkap: Minimum chapter number
$minkap = array();
$minkap['dom'] = 1;
$minkap['sl'] = 1;

// $maxkap: Maximum chapter number
$maxkap = array();
$maxkap['dom'] = 21;
$maxkap['sl'] = 24;


// $nextkap: Next chapter number, or -1
$nextkap = array();
$nextkap['dom'] = array();
$nextkap['sl'] = array();

for ($k=1; $k<$maxkap['dom']; ++$k)
    $nextkap['dom'][$k] = $k+1;
$nextkap['dom'][$maxkap['dom']] = -1;

$nextkap['sl'][1] = 2;
$nextkap['sl'][2] = 8;
$nextkap['sl'][8] = 23;
$nextkap['sl'][23] = 24;
$nextkap['sl'][24] = -1;


// $prevkap: Previous chapter number, or -1

$prevkap = array();
$prevkap['dom'] = array();
$prevkap['sl'] = array();

for ($k=2; $k<=$maxkap['dom']; ++$k)
    $prevkap['dom'][$k] = $k-1;
$prevkap['dom'][$minkap['dom']] = -1;

$prevkap['sl'][1] = -1;
$prevkap['sl'][2] = 1;
$prevkap['sl'][8] = 2;
$prevkap['sl'][23] = 8;
$prevkap['sl'][24] = 23;


function pagination($bog, $kapitel) {
global $minkap, $maxkap, $prevkap, $nextkap;

?>
<nav>
  <ul class="pagination">
    <li <?= $kapitel==$minkap[$bog] ? 'class="disabled"' : ''?>>
      <a href="<?= $prevkap[$bog][$kapitel]!=-1 ? sprintf('show.php?bog=%s&kap=%d',$bog,$prevkap[$bog][$kapitel]) : '#' ?>" aria-label="Previous">
        <span aria-hidden="true">&laquo;</span>
      </a>
    </li>
    <?php for ($k=$minkap[$bog]; $k!=-1; $k=$nextkap[$bog][$k]): ?>
       <?php if ($k==$kapitel): ?>
         <li class="active"><a href="#"><?= $k ?></a></li>
       <?php else: ?>
         <li><a href="<?= sprintf('show.php?bog=%s&kap=%d',$bog,$k) ?>"><?= $k ?></a></li>
       <?php endif; ?>
    <?php endfor; ?>
    <li>
    <li <?= $kapitel==$maxkap[$bog] ? 'class="disabled"' : ''?>>
      <a href="<?= $nextkap[$bog][$kapitel]!=-1 ? sprintf('show.php?bog=%s&kap=%d',$bog,$nextkap[$bog][$kapitel]) : '#' ?>" aria-label="Next">
        <span aria-hidden="true">&raquo;</span>
      </a>
    </li>
  </ul>
</nav>

<?php

}

if (!isset($_GET['bog']) || !isset($minkap[$_GET['bog']]) || !isset($_GET['kap']) || !is_numeric($_GET['kap'])) {
    echo "<pre>Forkerte parametre</pre>";
    die;
}
$kap = intval($_GET['kap']);
$bog = $_GET['bog'];

switch ($_GET['bog']) {
  case 'dom':
        makeheadstart("Dommerbogen &ndash; Kapitel $kap");
        break;

  case 'sl':
        makeheadstart("Salmernes Bog &ndash; Salme $kap");
        break;
}
?>

    <style type="text/css">
    span.verseno {
        vertical-align: super;
        font-size: x-small;
    }

    h2 {
    font-size: large;
    }

    .smallcap {
        font-variant:small-caps;
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
        font-family: Verdana, sans-serif;
        color: #005994;
        font-size: .7em;
        font-weight: bold;
        text-decoration: underline;
        cursor: pointer;
    }

    .refbody {
        text-indent: 0;
        font-family: Verdana, sans-serif;
        font-size: .7em;
        font-weight: normal;
        line-height: 1.1;
        display: block;
        border: 1px solid;
        padding: 5px;
        background-color: lightgray;
    }

    div.paragraph {
        text-indent: 2em;
        display: block;
    }

    h2 small {
      font-size: 85%;
      color: #333333;
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
            <?php elseif ($_SESSION['godsname']=='Herren'): ?>
              $('.thename').text('Herren');
              $('.thenames').text('Herrens');
              $('.thenamev').text('Herre');
            <?php else: ?>
              $('.thename').text('<?= $_SESSION['godsname'] ?>');
              $('.thenames').text('<?= $_SESSION['godsname'].'s' ?>');
              $('.thenamev').text('<?= $_SESSION['godsname'] ?>');
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

    <div class="container-fluid">
      <div class="row">
        <div class="col-xs-12">
          <?php pagination($bog,$kap); ?>
        </div>
      </div>

      <div class="row">
        <div class="col-xs-12" style="max-width:700px;">
          <?php echo replaceit(sprintf('tekst/%s%03d.txt',$bog,$kap), $kap, $credit); ?>
        </div>
      </div>

      <p>&nbsp;</p>
      <p>&nbsp;</p>

      <div class="row">
        <div class="col-sm-4">
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

      <div class="row">
        <div class="col-xs-12">
          <?php pagination($bog,$kap); ?>
        </div>
      </div><!--End of row-->

    </div><!--End of container-fluid-->

<?php
endbody();
