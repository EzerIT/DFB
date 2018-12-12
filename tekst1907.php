<?php
require_once('head.inc.php');
require_once('oversigt_1907.inc.php');

define('CHAP_PER_LINE', 7);

makeheadstart('Bibeltekst');
makeheadend();
makemenus(5);
?>

    <div class="container">
      <div class="row">

        <div class="col-lg-9">
          <h2><?= $description ?></h2>
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
                        <a href="show1907.php?bog=<?= $book ?>&kap=<?= $chno ?>"
                             class="mt-1 mb-0 ml-0 mr-0 btn chap-btn btn-danger"><?= $chno ?></a>
                      <?php endfor; ?>
                  <?php endfor; ?>
                </div>
              </div><!--panel-body-->
            </div><!--panel-->
          <?php endforeach; ?>

        </div><!--col-->

      </div><!--row-->
    </div><!--container-->
<?php
endbody();
?>
