<i class="icon-<?php
   switch ($status) {
       case Owaka::BUILD_OK:
           echo 'ok';
           break;

       case Owaka::BUILD_UNSTABLE:
           echo 'warning-sign';
           break;

       case Owaka::BUILD_ERROR:
           echo 'bug';
           break;

       case Owaka::BUILD_BUILDING:
           echo 'beaker';
           break;

       case Owaka::BUILD_QUEUED:
           echo 'time';
           break;

       case Owaka::BUILD_NODATA:
           echo 'ban-circle';
           break;
   }
   ?>"<?php if (isset($size)) {
       echo 'style="font-size: ' . $size . 'px"';
   } ?>/></i> 