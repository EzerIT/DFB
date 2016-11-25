<?php
require_once('head.inc.php');
require_once('oversigt.inc.php');

define('CHAP_PER_LINE', 7);

makeheadstart('Bibeltekst');
makeheadend();
makemenus(2);
?>

    <div class="container">
      <div class="row">

        <div class="col-md-3 visible-xs-block visible-sm-block">
          <div class="panel panel-success">
            <div class="panel-heading">
              <h3 class="panel-title">Farvekoden</h3>
            </div>
            <div class="panel-body">
              <p>Farvekoden angiver <a href="modenhed.php">modenheden</a> af teksten i de enkelte kapitler:</p>
              <p class="visible-xs-inline visible-sm-inline visible-md-block visible-lg-inline"><span style="width: 125px;" class="btn btn-warning">Rå oversættelse</span></p>
              <p class="visible-xs-inline visible-sm-inline visible-md-block visible-lg-inline"><span style="width: 125px;" class="btn btn-info">Delvis færdig</span></p>
              <p class="visible-xs-inline visible-sm-inline visible-md-block visible-lg-inline"><span style="width: 125px;" class="btn btn-success">Færdig</span></p>
            </div>
          </div>
        </div>


        <div class="col-md-9">
          <?php foreach ($title as $book => $tit): ?>
            <?php if ($book=='GT' || $book=='NT'): ?>
                 <h2><?= $tit ?></h2>
                 <?php continue; ?>
            <?php endif; ?>

            <div class="panel panel-info">
              <div class="panel-heading">
                <h3 class="panel-title"><?= $tit ?></h3>
              </div>
              <div class="panel-body">
                <p>Vælg <?= $chaptype[$book] ?>:</p>
                <table>
                  <?php $chcount = count($chap[$book]); ?>
                  <?php $chix = -1; ?>
                  <?php for ($i=0; $i<$chcount; $i+=CHAP_PER_LINE): ?>
                    <tr>
                      <?php for ($j=0; $j<CHAP_PER_LINE; ++$j): ?>
                        <?php if (++$chix >= $chcount) break; ?>
                        <?php $chno = $chap[$book][$chix]; ?>
                        <td><a style="width:100%" href="show.php?bog=<?= $book ?>&kap=<?= $chno ?>"
                             class="btn <?= is_array($style[$book]) ? $style[$book][$chno] : $style[$book] ?>"><?= $chno ?></a></td>
                      <?php endfor; ?>
                    </tr>
                  <?php endfor; ?>
                </table>
              </div><!--panel-body-->
            </div><!--panel-->
          <?php endforeach; ?>

        </div><!--col-->

        <div class="col-md-3 visible-md-block visible-lg-block">
          <div class="panel panel-success">
            <div class="panel-heading">
              <h3 class="panel-title">Farvekoden</h3>
            </div>
            <div class="panel-body">
              <p>Farvekoden angiver <a href="modenhed.php">modenheden</a> af teksten i de enkelte kapitler:</p>
              <p class="visible-xs-inline visible-sm-inline visible-md-block visible-lg-inline"><span style="width: 125px;" class="btn btn-warning">Rå oversættelse</span></p>
              <p class="visible-xs-inline visible-sm-inline visible-md-block visible-lg-inline"><span style="width: 125px;" class="btn btn-info">Delvis færdig</span></p>
              <p class="visible-xs-inline visible-sm-inline visible-md-block visible-lg-inline"><span style="width: 125px;" class="btn btn-success">Færdig</span></p>
              <p>&nbsp;</p>
            <img class="img-responsive hidden-xs" src="img/Community1-300.jpg" alt="">
            </div>
          </div>
        </div>


      </div><!--row-->
    </div><!--container-fluid-->
<?php
endbody();
?>

