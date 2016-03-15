<!DOCTYPE html>
<?php
   require_once('setdefault.inc.php');

   function showchecked($ix) {
       return $_SESSION[$ix]=='on' ? 'checked' : '';
   }

   function showselected($ix,$val) {
       return $_SESSION[$ix]==$val ? 'selected="selected"' : '';
   }
?>
     

<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Den Frie Bibel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link type="text/css" href="bootstrap-3.3.6-dist/css/bootstrap.css" rel="stylesheet" />
    <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="bootstrap-3.3.6-dist/js/bootstrap.min.js"></script>

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
          <li role="presentation" class="active"><a href="#">Læseoplevelse</a></li>
          <li role="presentation"><a href="tekst.php">Bibeltekst</a></li>
        </ul>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-12">

          <h1>Sæt dine læsepreferencer</h1>


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
                <input type="checkbox" name="showfn1" <?= showchecked('showfn1') ?>> Vis forklarende fodnoter (1,2,3,...)
              </label>
            </div>

            <div class="checkbox">
              <label>
                <input type="checkbox" name="showfna" <?= showchecked('showfna') ?>> Vis eksegetiske fodnoter (a,b,c,...)
              </label>
            </div>

            <div class="checkbox">
              <label>
                <input type="checkbox" name="oneline" <?= showchecked('oneline') ?>> Ét vers per linje
              </label>
            </div>

            <div class="form-group">
              <label for="godsname">Guds navn</label>
              <select name="godsname" id="godsname">
                <option <?= showselected('godsname','Herren') ?> >Herren</option>
                <option <?= showselected('godsname','HERREN') ?> >HERREN</option>
                <option <?= showselected('godsname','Jahve') ?>  >Jahve</option>
                <option <?= showselected('godsname','JHVH') ?>   >JHVH</option>
              </select>
            </div>

            <p>&nbsp;</p>

            <p>
              <button type="submit" class="btn btn-primary">OK</button>
              <a class="btn btn-default" href="<?= isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php' ?>">Annulér</a>
            </p>
          </form>

        </div><!--col-->
      </div><!--row-->
    </div><!--container-fluid-->

  </body>
</html>
