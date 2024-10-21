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
              <p>Farvekoden angiver modenheden af teksten i de enkelte kapitler:</p>
              <p class="d-inline"><span style="width: 130px;" class="btn btn-sm <?=$modenhed['ufuldstændigt']?>">Ufuldstændigt</span></p>
              <p class="d-inline"><span style="width: 130px;" class="btn btn-sm <?=$modenhed['delvis færdig']?>">Delvis færdig</span></p>
              <p class="d-inline"><span style="width: 130px;" class="btn btn-sm <?=$modenhed['færdig']?>">Færdig</span></p>
            </div>
          </div>
        </div>


        <div class="col-lg-9">
            <div class="card mt-4">
                <h1 class="card-header bg-info text-light">Vi har (dele af) disse bøger</h1>
                <div class="card-body">
                    <?php foreach ($abbrev as $abbr1 => $abbr2): ?>
                        <?php if ($abbr1=='matt'): ?>
                            <hr>
                        <?php endif; ?>
                        <?php if (!empty($chap[$abbr1])): ?>
                            <a href="#<?= $abbr1?>" class="mt-1 mb-0 ml-0 mr-0 btn book-btn btn-danger"><?= $abbr2 ?></a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div><!--card-body-->
            </div><!--card-->

            <?php foreach ($title as $book => $tit): ?>
                <?php if ($book=='GT' || $book=='NT'): ?>
                    <h2><?= $tit ?></h2>
                    <?php continue; ?>
                <?php endif; ?>

                <?php if (empty($chap[$book])): ?>
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
                                <a href="show.php?bog=<?= $book ?>&kap=<?= $chno ?>"
                                   class="mt-1 mb-0 ml-0 mr-0 btn chap-btn <?= is_array($style[$book]) ? $style[$book][$chno] : $style[$book] ?>"><?= $chno ?></a>
                            <?php endfor; ?>
                        </div>
                    </div><!--card-body-->
                </div><!--card-->
            <?php endforeach; ?>
        </div><!--col-->

        <div class="col-lg-3 d-none d-lg-block">
          <div class="card mt-4">
            <h1 class="card-header bg-success text-light">Farvekoden</h1>
            <div class="card-body">
              <p>Farvekoden angiver modenheden af teksten i de enkelte kapitler:</p>
              <p><span style="width: 130px;" class="btn btn-sm btn-secondary">Ufuldstændigt</span></p>
              <p><span style="width: 130px;" class="btn btn-sm btn-info">Delvis færdig</span></p>
              <p><span style="width: 130px;" class="btn btn-sm btn-success">Færdig</span></p>
              <p>&nbsp;</p>
            <img class="img-fluid" src="img/pexels-jessika-arraes-19561462.jpg" alt="">
            </div>
          </div>

          <div class="card mt-4">
            <h1 class="card-header bg-success text-light">Ord&shy;for&shy;kla&shy;ringer</h1>
            <div class="card-body">
                <p>Der findes for tiden følgende ord&shy;for&shy;kla&shy;ringer:</p>

                <ul>
                <?php
                $d = dir("ordforkl");
                $files = [];
                while (false !== ($entry = $d->read())) {
                    if (substr($entry,-5)==='.html')
                        $files[] = substr($entry,0,-5);
                    elseif (substr($entry,-4)==='.txt')
                        $files[] = substr($entry,0,-4);
                }
                $d->close();

                sort($files);

                foreach ($files as $f) {
                    $link = mb_strtoupper(mb_substr($f,0,1)) . mb_substr($f,1);
                    echo "<li><a href=\"ordforklaring.php?ord=$link\">$link</a></li>";
                }
                ?>
                </ul>
            </div>
          </div>
        </div>

        <div class="col-lg-3 d-block d-lg-none">
            <div class="card mt-4">
                <h1 class="card-header bg-success text-light">Ord&shy;for&shy;kla&shy;ringer</h1>
                <div class="card-body">
                    <p>Der findes for tiden følgende ord&shy;for&shy;kla&shy;ringer:</p>

                    <ul>
                        <?php
                        $d = dir("ordforkl");
                        $files = [];
                        while (false !== ($entry = $d->read())) {
                            if (substr($entry,-5)==='.html')
                                $files[] = substr($entry,0,-5);
                            elseif (substr($entry,-4)==='.txt')
                                $files[] = substr($entry,0,-4);
                        }
                        $d->close();

                        sort($files);

                        foreach ($files as $f) {
                            $link = strtoupper($f[0]) . substr($f,1);
                            echo "<li><a href=\"ordforklaring.php?ord=$link\">$link</a></li>";
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>

      </div><!--row-->
    </div><!--container-->
<?php
endbody();
?>
