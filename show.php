<?php
if (!isset($_GET['bog']) || $_GET['bog']!='dom' || !isset($_GET['kap']) || !is_numeric($_GET['kap'])) {
    echo "<pre>Forkerte parametre</pre>";
    die;
}
$kap = intval($_GET['kap']);


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
?>


<!DOCTYPE html>
<?php
   require_once('setdefault.inc.php');
   require_once('replaceit.inc.php');
?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
       <title>Dommerbogen &ndash; Kapitel <?= $kap ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link type="text/css" href="bootstrap-3.3.6-dist/css/bootstrap.css" rel="stylesheet" />
    <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="bootstrap-3.3.6-dist/js/bootstrap.min.js"></script>

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

    /* Taken from XKCD */
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
              $('.thename').text('Herren').addClass('smallcap');
              $('.thenames').text('Herrens').addClass('smallcap');
              $('.thenamev').text('Herre').addClass('smallcap');
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
  </head>

  <body>

    <nav id="myNavbar" class="navbar navbar-default navbar-static-top">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbarCollapse">
          <span class="sr-only">Toggle navigation</span><!-- For screen reader -->
          <span class="icon-bar"></span><!-- Line on menu toggle button -->
          <span class="icon-bar"></span><!-- Line on menu toggle button -->
          <span class="icon-bar"></span><!-- Line on menu toggle button -->
        </button>
        <a class="navbar-brand" href="http://dfb.ezer.dk">Den Frie Bibel</a>
      </div>
      
      <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="nav nav-pills">
          <li role="presentation"><a href="index.php">Hjem</a></li>
          <li role="presentation"><a href="format.php">Læseoplevelse</a></li>
          <li role="presentation"><a href="tekst.php">Bibeltekst</a></li>
        </ul>
      </div>
    </nav>

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

</body>
</html>
