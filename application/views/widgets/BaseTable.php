<?php
echo View::factory('widgets/BaseStart')->render();
?>
<table width="100%" style="font-size: 60%">
    <thead>
        <tr>
            <?php foreach ($columns as $column): ?>
                <th><?php echo $column; ?></th>
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
            <?php foreach ($rows as $row): ?>
                <tr class="<?php echo $row['class']; ?>">
                    <?php foreach ($row['columns'] as $column): ?>
                        <td><?php echo $column; ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
<?php
echo View::factory('widgets/BaseEnd')->render();
?>