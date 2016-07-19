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
setifpost('oneline');
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

header('location: ' . $_POST['referer']);