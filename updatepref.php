<?php

function setifpost($ix) {
    if (isset($_POST[$ix]))
        $_SESSION[$ix] = $_POST[$ix];
    else
        $_SESSION[$ix] = 'off';
  }

if (!session_id())
    session_start();

setifpost('showverse');
setifpost('showchap');
setifpost('showh2');
setifpost('showfna');
setifpost('showfn1');
setifpost('showfnblock');
setifpost('oneline');
setifpost('linespace');
setifpost('exegetic');
setifpost('font');

if (isset($_POST['godsname'])) {
    switch ($_POST['godsname']) {
      case 'Herren':
      case 'HERREN':
      case 'Jahve':
      case 'JHVH':
            $_SESSION['godsname'] = $_POST['godsname'];
            break;

      default:
            $_SESSION['godsname'] = 'HERREN';
            break;
    }
}

if (isset($_POST['usecookie'])) {
    if ($_SERVER['HTTP_HOST']==='localhost') { // We are testing
        $host = 'localhost';
        $use_https = false;
    }
    else  { // Production system
        $host = 'denfriebibel.dk';
        $use_https = true;
    }

    foreach (['showverse', 'showchap', 'showh2', 'showfna', 'showfn1', 'showfnblock', 'oneline', 'linespace', 'exegetic', 'godsname', 'font'] as $key)
        setcookie($key, $_SESSION[$key], time()+60*60*24*365*2 /* 2 years */, '/', $host, $use_https, true);
}


header('location: ' . $_POST['referer']);
