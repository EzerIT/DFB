<?php
require_once('head.inc.php');
require_once('oversigt.inc.php');

define('CHAP_PER_LINE', 7);

makeheadstart('Den Frie Bibel');
makeheadend();
makemenus(2);
?>

    <div class="container-fluid">
      <div class="row">
        <div class="col-xs-12">

          <p>Farvekoden angiver <a href="modenhed.php">modenheden</a> af teksten i de enkelte kapitler:</p>
          <p><span style="width: 100px;" class="btn btn-warning">Ikke færdig</span>
            <span style="width: 100px;" class="btn btn-success">Færdig</span></p>

          <?php foreach ($title as $book => $tit): ?>
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
                        <td><a style="width:100%" href="show.php?bog=<?= $book ?>&kap=<?= $chno ?>" class="btn <?= $style[$book] ?>"><?= $chno ?></a></td>
                      <?php endfor; ?>
                    </tr>
                  <?php endfor; ?>
                </table>
              </div><!--panel-body-->
            </div><!--panel-->
          <?php endforeach; ?>

        </div><!--col-->
      </div><!--row-->
    </div><!--container-fluid-->
<?php
endbody();
?>

