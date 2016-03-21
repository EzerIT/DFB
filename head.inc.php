<?php
function menuitem($name, $link, $active) {
    echo '          ';
    if ($active)
        echo "<li role=\"presentation\" class=\"active\"><a href=\"#\">$name</a></li>\n";
    else
        echo "<li role=\"presentation\"><a href=\"$link\">$name</a></li>\n";
}

function makeheadstart($title) {
echo <<<END
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>$title</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link type="text/css" href="bootstrap-3.3.6-dist/css/bootstrap.css" rel="stylesheet" />
    <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="bootstrap-3.3.6-dist/js/bootstrap.min.js"></script>

END;
}

function makeheadend() {
echo <<<END
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
        <a class="navbar-brand" href="http://denfriebibel.dk">Den Frie Bibel</a>
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