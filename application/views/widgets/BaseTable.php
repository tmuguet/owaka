<?php
echo View::factory('widgets/BaseStart')->render();
?>
<table width="100%" style="font-size: 60%">
    <thead>
        <tr>
            <?php foreach ($columns as $_column): ?>
                <th><?php echo $_column; ?></th>
            <?php endforeach; ?>
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
                <tr class="<?php echo (!empty($_row['class']) ? $_row['class'] : ''); ?>">
                    <?php foreach ($_row['columns'] as $_column): ?>
                        <td><?php echo $_column; ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
<?php
echo View::factory('widgets/BaseEnd')->render();
?>