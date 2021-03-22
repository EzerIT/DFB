<?php
require_once('head.inc.php');

makeheadstart('Ordforklaring');
makeheadend();
makemenus(-1);

mb_internal_encoding('UTF-8');
?>

    <div class="container">
      <div class="row justify-content-center">

        <div class="col-lg-10 col-xl-9">
          <div class="card mt-4">
            <div class="card-body">
              <h1 class="card-title">Ord­for­kla­ring</h1>

              <?php if (!isset($_GET['ord'])): ?>
                  <p class="card-text bg-warning">Manglende parameter.</p>
              <?php elseif (strstr($_GET['ord'],'/')): ?>
                  <p class="card-text bg-warning">Ulovlig parameter.</p>
              <?php else: ?>
                  <?php $file = 'ordforkl/' . mb_strtolower($_GET['ord']) . '.html'; ?>
                  <?php if (!file_exists($file)): ?>
                      <p class="card-text bg-warning">Ordforklaring findes ikke.</p>
                  <?php else: ?>
                      <?php include($file); ?>
                  <?php endif; ?>
              <?php endif; ?>

              <a class="btn btn-secondary" href="<?= isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php' ?>">Tilbage</a>
            </div>
          </div>
        </div>
   
      </div><!--End of row-->
    </div><!--End of container-->
<?php
endbody();
?>
