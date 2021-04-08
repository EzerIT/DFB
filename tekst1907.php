<?php
require_once('head.inc.php');
require_once('oversigt_1907.inc.php');

makeheadstart('Bibeltekst');
makeheadend();
makemenus(5);
?>

    <div class="container">
      <div class="row">
        <div class="col-lg-9">
          <h2><?= $description ?></h2>

          <div class="card mt-4">
              <h1 class="card-header bg-info text-light">Det Nye Testamentes bøger</h1>
              <div class="card-body">
                  <?php foreach ($abbrev as $abbr1 => $abbr2): ?>
                      <a href="#<?= $abbr1?>" class="mt-1 mb-0 ml-0 mr-0 btn book-btn btn-danger"><?= $abbr2 ?></a>
                  <?php endforeach; ?>
              </div><!--card-body-->
          </div><!--card-->

          <?php foreach ($title as $book => $tit): ?>
              <?php if ($book=='GT' || $book=='NT'): ?>
                  <h2><?= $tit ?></h2>
                  <?php continue; ?>
              <?php endif; ?>

              <div id="<?= $book ?>" class="card mt-4">
                  <h1 class="card-header bg-info text-light"><?= $tit ?></h1>
                  <div class="card-body">
                      <p>Vælg <?= $chaptype[$book] ?>:</p>
                      <div>
                          <?php $chcount = count($chap[$book]); ?>
                          <?php $chix = -1; ?>
                          <?php for ($i=0; $i<$chcount; ++$i): ?>
                              <?php if (++$chix >= $chcount) break; ?>
                              <?php $chno = $chap[$book][$chix]; ?>
                              <a href="show1907.php?bog=<?= $book ?>&kap=<?= $chno ?>"
                                 class="mt-1 mb-0 ml-0 mr-0 btn chap-btn btn-danger"><?= $chno ?></a>
                          <?php endfor; ?>
                      </div>
                  </div><!--card-body-->
              </div><!--card-->
          <?php endforeach; ?>

        </div><!--col-->

      </div><!--row-->
    </div><!--container-->
<?php
endbody();
?>
