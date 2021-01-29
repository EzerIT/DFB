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

        <div class="col-lg-3 d-block d-lg-none">
          <div class="card mt-4">
            <h1 class="card-header bg-success text-light">Farvekoden</h1>
            <div class="card-body">
              <p>Farvekoden angiver <a href="modenhed.php">modenheden</a> af teksten i de enkelte kapitler:</p>
              <p class="d-inline"><span style="width: 130px;" class="btn btn-sm btn-secondary">Ufuldstændigt</span></p>
              <p class="d-inline"><span style="width: 130px;" class="btn btn-sm btn-warning">Rå oversættelse</span></p>
              <p class="d-inline"><span style="width: 130px;" class="btn btn-sm btn-info">Delvis færdig</span></p>
              <p class="d-inline"><span style="width: 130px;" class="btn btn-sm btn-success">Færdig</span></p>
            </div>
          </div>
        </div>


        <div class="col-lg-9">
          <?php foreach ($title as $book => $tit): ?>
            <?php if ($book=='GT' || $book=='NT'): ?>
                 <h2><?= $tit ?></h2>
                 <?php continue; ?>
            <?php endif; ?>

            <div class="card mt-4">
              <h1 class="card-header bg-info text-light"><?= $tit ?></h1>
              <div class="card-body">
                <p>Vælg <?= $chaptype[$book] ?>:</p>
                <div>
                  <?php $chcount = count($chap[$book]); ?>
                  <?php $chix = -1; ?>
                  <?php for ($i=0; $i<$chcount; $i+=CHAP_PER_LINE): ?>
                      <?php for ($j=0; $j<CHAP_PER_LINE; ++$j): ?>
                        <?php if (++$chix >= $chcount) break; ?>
                        <?php $chno = $chap[$book][$chix]; ?>
                        <a href="show.php?bog=<?= $book ?>&kap=<?= $chno ?>"
                             class="mt-1 mb-0 ml-0 mr-0 btn chap-btn <?= is_array($style[$book]) ? $style[$book][$chno] : $style[$book] ?>"><?= $chno ?></a>
                      <?php endfor; ?>
                  <?php endfor; ?>
                </div>
              </div><!--panel-body-->
            </div><!--panel-->
          <?php endforeach; ?>

        </div><!--col-->

        <div class="col-lg-3 d-none d-lg-block">
          <div class="card mt-4">
            <h1 class="card-header bg-success text-light">Farvekoden</h1>
            <div class="card-body">
              <p>Farvekoden angiver <a href="modenhed.php">modenheden</a> af teksten i de enkelte kapitler:</p>
              <p><span style="width: 130px;" class="btn btn-sm btn-secondary">Ufuldstændigt</span></p>
              <p><span style="width: 130px;" class="btn btn-sm btn-warning">Rå oversættelse</span></p>
              <p><span style="width: 130px;" class="btn btn-sm btn-info">Delvis færdig</span></p>
              <p><span style="width: 130px;" class="btn btn-sm btn-success">Færdig</span></p>
              <p>&nbsp;</p>
            <img class="img-fluid" src="img/Community1-300.jpg" alt="">
            </div>
          </div>
        </div>


      </div><!--row-->
    </div><!--container-->
<?php
endbody();
?>
