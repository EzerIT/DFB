<?php
   if (!session_id())
       session_start();

   function setdefault($ix,$val) {
       if (!isset($_SESSION[$ix]))
           $_SESSION[$ix] = $val;
   }

   setdefault('showverse', 'on');
   setdefault('showh2',    'on');
   setdefault('showfna',   'on');
   setdefault('showfn1',   'on');
   setdefault('oneline',   'off');
   setdefault('godsname',  'HERREN');
