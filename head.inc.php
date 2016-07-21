<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

function menuitem($name, $link, $active) {
    echo '          ';
    if ($active)
        echo "<li role=\"presentation\" class=\"active\"><a href=\"#\">$name</a></li>\n";
    else
        echo "<li role=\"presentation\"><a href=\"$link\">$name</a></li>\n";
}


$allfonts = array(
    'Helvetica'   => "'Helvetica Neue', Helvetica, Arial, sans-serif",
    'Merriweather'=> "'Merriweather', serif");



function makeheadstart($title, $googlefonts=false) {
    echo <<<END
<!DOCTYPE html>
<html lang="da">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>$title</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link type="text/css" href="bootstrap-3.3.6-dist/css/bootstrap.css" rel="stylesheet" />
    <link type="text/css" href="style/dfb.css" rel="stylesheet" />
    <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="bootstrap-3.3.6-dist/js/bootstrap.min.js"></script>

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

    <div class="brand hidden-xs">Den Frie Bibel</div>


    <nav id="myNavbar" class="navbar navbar-default navbar-static-top">
      <div class="navbar-header visible-xs-block">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbarCollapse">
          <span class="sr-only">Toggle navigation</span><!-- For screen reader -->
          <span class="icon-bar"></span><!-- Line on menu toggle button -->
          <span class="icon-bar"></span><!-- Line on menu toggle button -->
          <span class="icon-bar"></span><!-- Line on menu toggle button -->
        </button>
        <a class="navbar-brand" href="index.php">Den Frie Bibel</a>
      </div>

END;
}

function makemenus($thisnum) {
echo <<<END
      <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="nav navbar-nav">

END;
    menuitem('Hjem','index.php',$thisnum===0);
    menuitem('Læseoplevelse','format.php',$thisnum===1);
    menuitem('Bibeltekst','tekst.php',$thisnum===2);

    $active3 = $thisnum===3 ? "active" : "";

echo <<<END

           <li role="presentation" class="dropdown $active3" >
             <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
               Om Den Frie Bibel <span class="caret"></span>
             </a>
             <ul class="dropdown-menu">
               <!--li><a href="faq.php">Hyppigt stillede spørgsmål</a></li-->
               <li><a href="modenhed.php">Tekstens modenhed</a></li>
               <li><a href="vejledning.php">Vejledning for bidragydere</a></li>
             </ul>
           </li>
        </ul>
      </div>
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
      <p class="visible-xs-block">XS</p>
      <p class="visible-sm-block">SM</p>
      <p class="visible-md-block">MD</p>
      <p class="visible-lg-block">LG</p>
END;
}