<img src="img/freepik/<?php
switch ($status) {
    case 'ok':
        echo 'right';
        break;

    case 'unstable':
        echo 'warning';
        break;

    case 'error':
        echo 'radiation';
        break;

    case 'building':
        echo 'beaker';
        break;

    case 'queued':
        echo 'sandclock';
        break;

    case 'nodata':
        echo 'blocked';
        break;
}
?>.png" width="<?php echo $size; ?>" class="ico"/>