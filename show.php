<?php
require_once('head.inc.php');
require_once('setdefault.inc.php');
require_once('replaceit.inc.php');


function pagination($kapitel) {

?>
<nav>
  <ul class="pagination">
    <li <?= $kapitel==1 ? 'class="disabled"' : ''?>>
      <a href="<?= $kapitel==1 ? '#' : sprintf('show.php?bog=dom&kap=%d',$kapitel-1) ?>" aria-label="Previous">
        <span aria-hidden="true">&laquo;</span>
      </a>
    </li>
    <?php for ($k=1; $k<22; ++$k): ?>
       <?php if ($k==$kapitel): ?>
         <li class="active"><a href="#"><?= $k ?></a></li>
       <?php else: ?>
         <li><a href="<?= sprintf('show.php?bog=dom&kap=%d',$k) ?>"><?= $k ?></a></li>
       <?php endif; ?>
    <?php endfor; ?>
    <li>
    <li <?= $kapitel==21 ? 'class="disabled"' : ''?>>
      <a href="<?= $kapitel==21 ? '#' : sprintf('show.php?bog=dom&kap=%d',$kapitel+1) ?>" aria-label="Next">
        <span aria-hidden="true">&raquo;</span>
      </a>
    </li>
  </ul>
</nav>

<?php

}

if (!isset($_GET['bog']) || $_GET['bog']!='dom' || !isset($_GET['kap']) || !is_numeric($_GET['kap'])) {
    echo "<pre>Forkerte parametre</pre>";
    die;
}
$kap = intval($_GET['kap']);


makeheadstart("Dommerbogen &ndash; Kapitel $kap");
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
          <?php pagination($kap); ?>
        </div>
      </div>

      <div class="row">
        <div class="col-xs-12" style="max-width:700px;">
          <?php echo replaceit(sprintf('tekst/dom%03d.txt',$kap), $kap); ?>
        </div>
      </div>
           
      <div class="row">
        <div class="col-xs-12">
          <?php pagination($kap); ?>
        </div>
      </div><!--End of row-->
    </div><!--End of container-fluid-->

<?php
endbody();
?>
