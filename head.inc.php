<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

function menuitem($name, $link, $active) {
    echo '          ';
    if ($active)
        echo "<li class=\"nav-item active\"><a class=\"nav-link pt-3 pb-3 pl-3 pr-3\" href=\"#\">$name</a></li>\n";
    else
        echo "<li class=\"nav-item\"><a class=\"nav-link pt-3 pb-3 pl-3 pr-3\" href=\"$link\">$name</a></li>\n";
}


$allfonts = array(
    'Helvetica'   => "'Helvetica Neue', Helvetica, Arial, sans-serif",
    'Merriweather'=> "'Merriweather', serif");

function stat_attr($file) {
    $file_stat = stat($file); // We need this to modify the href attribute on $file. Otherwise
                                 // the browser may not reload the file, even if it has changed.

    return "stat=$file_stat[9]";
}


function makeheadstart($title, $googlefonts=false) {
    $stat_dfb_css = stat_attr('style/dfb.css');
    echo <<<END
<!DOCTYPE html>
<html lang="da">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>$title</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="https://denfriebibel.dk/img/icon.png">
    <link type="text/css" href="bootstrap-4.1.1-dist/css/bootstrap.css" rel="stylesheet" />
    <link type="text/css" href="style/dfb.css?$stat_dfb_css" rel="stylesheet" />
    <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="bootstrap-4.1.1-dist/js/bootstrap.min.js"></script>

END;

    if ($googlefonts) {
        global $allfonts;
        $allfonts2 = array_slice(array_keys($allfonts),1); // Omit Helvetica from fonts
        $allfonts2[] = 'Josefin Slab';
    }
    else 
        $allfonts2 = array('Josefin Slab');

    $fontlist = implode('%7C', $allfonts2);
    echo '    <link href="https://fonts.googleapis.com/css?family=',
        str_replace(' ', '+', $fontlist),
        '" rel="stylesheet">',"\n";

}

function makeheadend() {
echo <<<END
  </head>

  <body>
    <div class="brand d-none d-md-block">Den Frie Bibel</div>

    <nav class="navbar navbar-expand-md navbar-light bg-light pt-0 pb-0">
      <div class="mx-md-auto divnavbar">
      <a class="navbar-brand d-md-none" href="index.php">Den Frie Bibel</a>
      <button class="navbar-toggler mt-1 mb-1" type="button" data-toggle="collapse" data-target="#mainMenu" aria-controls="mainMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

END;
}

function makemenus($thisnum) {
echo <<<END
      <div class="collapse navbar-collapse" id="mainMenu">
        <ul class="navbar-nav mr-auto">

END;
    menuitem('Hjem','index.php',$thisnum===0);
    menuitem('Bibeltekst','tekst.php',$thisnum===2);
    menuitem('Læseoplevelse','format.php',$thisnum===1);
    menuitem('Andre formater','andreformater.php',$thisnum===4);

    $active3 = $thisnum===3 ? "active" : "";
    $active5 = $thisnum===5 ? "active" : "";

echo <<<END
          <li class="nav-item dropdown $active3">
            <a class="nav-link dropdown-toggle pt-3 pb-3 pl-3 pr-3" href="#" id="navbarDropdown3" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Om Den Frie Bibel</a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown3">
              <a class="dropdown-item" href="maalsaetning.php">Målsætning med oversættelsen</a>
              <a class="dropdown-item" href="modenhed.php">Tekstens modenhed</a>
              <a class="dropdown-item" href="vejledning.php">Vejledning for bidragydere</a>
              <a class="dropdown-item" href="forprogrammoerer.php">For programmører</a>
            </div>
          </li>

          <li class="nav-item dropdown $active5">
            <a class="nav-link dropdown-toggle pt-3 pb-3 pl-3 pr-3" href="#" id="navbarDropdown5" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Andre oversættelser</a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown5">
              <a class="dropdown-item" href="tekst1871.php">Autoriseret GT 1871</a>
              <a class="dropdown-item" href="tekst1907.php">Autoriseret NT 1907</a>
              <a class="dropdown-item" href="https://www.bibelselskabet.dk/bibelen-online" target="_blank">Autoriseret 1992<br>(hos Bibelselskabet)</a>
            </div>
          </li>

        </ul>
      </div></div>
    </nav>

END;
}

function endbody() {
    echo <<<END
  </body>
</html>

END;
}

function showsize() {
    echo <<<END
      <p class="d-xs-block d-sm-none">XS</p>
      <p class="d-none d-sm-block d-md-none">SM</p>
      <p class="d-none d-md-block d-lg-none">MD</p>
      <p class="d-none d-lg-block d-xl-none">LG</p>
      <p class="d-none d-xl-block">XL</p>
END;
}