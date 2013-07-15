<?php
echo View::factory('widgets/BaseStart')->render();
?>
<table width="100%" style="font-size: 60%">
    <thead>
        <tr>
            <?php for ($i=0; $i < sizeof($columns); $i++): ?>
                <th<?php echo ($columns[$i][0] == '_' ? ' class="widget-detailed"' : ''); ?>><?php echo ($columns[$i][0] == '_' ? substr($columns[$i], 1) : $columns[$i]); ?></th>
            <?php endfor; ?>
        </tr>
    </thead>
    <tbody>
        <?php
        if (sizeof($rows) == 0):
            ?>
            <tr><td colspan="<?php echo sizeof($columns); ?>"><?php
                    echo View::factory('icon')->set('status', 'nodata')->set('size', 32)->render();
                    ?><br/>no data</td></tr>
        <?php else: ?>
            <?php foreach ($rows as $_row): ?>
                <tr class="<?php
                echo (!empty($_row['class']) ? $_row['class'] : '');
                ?>"<?php
                if (!empty($_row['link'])) {
                    echo ' onclick="' . Owaka::processLink($from, $_row['link'], 'js') . '"';
                }
                ?>>
                        <?php for ($i=0; $i < sizeof($_row['columns']); $i++): ?>
                        <td<?php echo ($columns[$i][0] == '_' ? ' class="widget-detailed"' : ''); ?>><?php echo $_row['columns'][$i]; ?></td>
                    <?php endfor; ?>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
<?php
echo View::factory('widgets/BaseEnd')->render();
?>