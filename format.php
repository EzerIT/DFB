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

<script>
    $(function() {
        $('#exegetic').change(function () {
            $('#syn-options').prop('hidden',!$(this).is(':checked'));
        });

        $('#exegetic').trigger('change');
    });
</script>


<div class="container">
  <div class="row">
    <div class="col-md-12">

      <div class="card mt-4">
        <h1 class="card-header bg-info text-light">Sæt dine læsepreferencer</h1>
        <div class="card-body">

          <img class="img-fluid float-right d-none d-lg-block" style="margin-top: 60px" src="img/pexels-andrea-piacquadio-920387.jpg" alt="">
          <form action="updatepref.php" method="post" accept-charset="utf-8">
            <input type="hidden" name="referer"
                   value="<?= isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php' ?>">

            <div class="form-check">
              <label>
                <input class="form-check-input" type="checkbox" name="showverse" <?= showchecked('showverse') ?>> Vis versnumre
              </label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="showchap" <?= showchecked('showchap') ?>>
                <label class="form-check-label">Vis kapitelnumre ved hvert vers</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="showh2" <?= showchecked('showh2') ?>>
                <label class="form-check-label">Vis overskrifter</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="showfn1" <?= showchecked('showfn1') ?>>
                <label class="form-check-label">Vis generellefodnoter (1,2,3,...) og ordforklaringsmærket (°).
              </label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="showfna" <?= showchecked('showfna') ?>>
                <label class="form-check-label">Vis faglige fodnoter (a,b,c,...)</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="showfnblock" <?= showchecked('showfnblock') ?>>
                <label class="form-check-label">Vis fodnoter under bibeltekst</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="oneline" <?= showchecked('oneline') ?>>
                <label class="form-check-label">Ét vers per linje</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="linespace" <?= showchecked('linespace') ?>>
                <label class="form-check-label">Ekstra linjeafstand</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="exegetic" name="exegetic" <?= showchecked('exegetic') ?>>
                <label class="form-check-label">Syntaktisk layout (kun visse kapitler)</label>
            </div>

            <div id="syn-options">
                Indrykning: 
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="indent_type" value="none" <?= showradiochecked('indent_type','none') ?>>
                    <label  class="form-check-label">Ingen</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="indent_type" value="number" <?= showradiochecked('indent_type','number') ?>>
                    <label class="form-check-label">Tal</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="indent_type" value="text" <?= showradiochecked('indent_type','text') ?>>
                    <label class="form-check-label">Tekst</label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="include_orig_lang" <?= showchecked('include_orig_lang') ?>>
                    <label class="form-check-label">Medtag hebraisk tekst</label>
                </div>
            </div>
            
            <div hidden class="form-check">
                <input class="form-check-input" type="checkbox" name="markadded" <?= showchecked('markadded') ?>>
                <label class="form-check-label">Markér tilføjet tekst med [kantede parenteser] (kun visse kapitler)</label>
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

            <div class="form-group">
                <label for="fontsize">Skriftstørrelse (10-500% af normal):</label>
                <input type="number" id="fontsize" name="fontsize" value="<?= $_SESSION['fontsize'] ?>" min="10" max="500">%
            </div>
                
            <p>&nbsp;</p>

            <h4>Bevar præferencer:</h4>

            <div class="form-check">
              <label>
                  <input class="form-check-input" type="checkbox" name="usecookie">
                  <label class="form-check-label">Benyt også disse indstillinger ved dit næste besøg. (Gemmes som cookies i din browswer.)</label>
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
