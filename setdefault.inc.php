<?php
   if (!session_id())
       session_start();

   function setdefault($ix,$val) {
       if (!isset($_SESSION[$ix]))
           $_SESSION[$ix] = isset($_COOKIE[$ix]) ? $_COOKIE[$ix] : $val;
   }

   setdefault('showverse',  'on');
   setdefault('showchap',   'off');
   setdefault('showh2',     'on');
   setdefault('showfna',    'on');
   setdefault('showfn1',    'on');
   setdefault('showfnblock','off');
   setdefault('oneline',    'off');
   setdefault('linespace',  'off');
   setdefault('exegetic',   'off');
   setdefault('markadded',  'off');
   setdefault('godsname',   'HERREN');
   setdefault('font',       'Helvetica');
   setdefault('fontsize',   '100');
