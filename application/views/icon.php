<i class="icon-<?php
   switch ($status) {
       case 'ok':
           echo 'ok';
           break;

       case 'unstable':
           echo 'warning-sign';
           break;

       case 'error':
           echo 'bug';
           break;

       case 'building':
           echo 'beaker';
           break;

       case 'queued':
           echo 'time';
           break;

       case 'nodata':
           echo 'ban-circle';
           break;
   }
   ?>"<?php if (isset($size)) {
       echo 'style="font-size: ' . $size . 'px"';
   } ?>/></i> 