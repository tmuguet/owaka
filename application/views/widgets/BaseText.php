<?php
echo View::factory('widgets/BaseStart')->render();
?>
<div style="position: absolute; top: 0px; left: 0px; right: 0px; bottom: 0px; overflow: auto; text-align: left;">
    <?php echo $content; ?>
</div>
<?php
echo View::factory('widgets/BaseEnd')->render();
?>