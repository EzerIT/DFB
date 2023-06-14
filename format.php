<?php
require_once('head.inc.php');
require_once('setdefault.inc.php');

function showchecked($ix) {
    return $_SESSION[$ix]=='on' ? 'checked' : '';
}

function showradiochecked($ix,$val) {
    return $_SESSION[$ix]==$val ? 'checked' : '';
}

function showselected($ix,$val) {
    return $_SESSION[$ix]==$val ? 'selected="selected"' : '';
}

makeheadstart('Læseoplevelse',true);
makeheadend();
makemenus(1);
?>

<div class="container">
  <div class="row">
    <div class="col-md-12">

      <div class="card mt-4">
        <h1 class="card-header bg-info text-light">Sæt dine læsepreferencer</h1>
        <div class="card-body">

          <img class="img-fluid float-right d-none d-lg-block" style="margin-top: 60px" src="img/Community5-400.jpg" alt="">
          <form action="updatepref.php" method="post" accept-charset="utf-8">
            <input type="hidden" name="referer"
                   value="<?= isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php' ?>">

            <div class="checkbox">
              <label>
                <input type="checkbox" name="showverse" <?= showchecked('showverse') ?>> Vis versnumre
              </label>
            </div>

            <div class="checkbox">
              <label>
                <input type="checkbox" name="showchap" <?= showchecked('showchap') ?>> Vis kapitelnumre ved hvert vers
              </label>
            </div>

            <div class="checkbox">
              <label>
                <input type="checkbox" name="showh2" <?= showchecked('showh2') ?>> Vis overskrifter
              </label>
            </div>

            <div class="checkbox">
              <label>
                <input type="checkbox" name="showfn1" <?= showchecked('showfn1') ?>> Vis generelle
                fodnoter (1,2,3,...) og ordforklaringsmærket (°).
              </label>
            </div>

            <div class="checkbox">
              <label>
                <input type="checkbox" name="showfna" <?= showchecked('showfna') ?>> Vis faglige fodnoter (a,b,c,...)
              </label>
            </div>

            <div class="checkbox">
              <label>
                <input type="checkbox" name="showfnblock" <?= showchecked('showfnblock') ?>> Vis fodnoter under bibeltekst
              </label>
            </div>

            <div class="checkbox">
              <label>
                <input type="checkbox" name="oneline" <?= showchecked('oneline') ?>> Ét vers per linje
              </label>
            </div>

            <div class="checkbox">
              <label>
                <input type="checkbox" name="linespace" <?= showchecked('linespace') ?>> Ekstra linjeafstand
              </label>
            </div>

            <div class="checkbox">
              <label>
                <input type="checkbox" name="exegetic" <?= showchecked('exegetic') ?>> Eksegetisk layout (kun visse kapitler)
              </label>
            </div>

            <div class="checkbox">
              <label>
                <input type="checkbox" name="markadded" <?= showchecked('markadded') ?>> Markér tilføjet tekst med [kantede parenteser] (kun visse kapitler)
              </label>
            </div>

            <div class="form-group">
              <label for="godsname">Guds navn:</label>
              <select name="godsname" id="godsname">
                <option <?= showselected('godsname','Herren') ?> >Herren</option>
                <option <?= showselected('godsname','HERREN') ?> >HERREN</option>
                <option <?= showselected('godsname','Jahve') ?>  >Jahve</option>
                <option <?= showselected('godsname','JHVH') ?>   >JHVH</option>
              </select>
            </div>

            <p>&nbsp;</p>

            <h4>Skrifttype i bibeltekst:</h4>

            <?php foreach ($allfonts as $val => $fam): ?>
              <div class="radio">
                <label>
                  <input type="radio" name="font" value="<?= $val ?>"  <?= showradiochecked('font',$val) ?>>
                  <i><?= $val ?>:</i> <span style="font-family: <?= $fam ?>">I begyndelsen skabte Gud himlen og jorden.</span>
                </label>
              </div>
            <?php endforeach; ?>

            <p>&nbsp;</p>

            <h4>Bevar præferencer:</h4>

            <div class="checkbox">
              <label>
                <input type="checkbox" name="usecookie"> Benyt også disse indstillinger ved dit næste besøg. (Gemmes som cookies i din browswer.)
              </label>
            </div>

            <p>&nbsp;</p>

            <p>
              <button type="submit" class="btn btn-primary">OK</button>
              <a class="btn btn-secondary" href="<?= isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php' ?>">Annullér</a>
            </p>
          </form>

        </div>
      </div>
    </div><!--col-->
  </div><!--row-->
</div><!--container-->

<?php
endbody();
?>
