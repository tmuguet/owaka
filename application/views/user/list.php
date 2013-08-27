<?php
$title       = 'users';
$menu        = array();
$menu_bottom = array();

echo View::factory('baseStart')
        ->set('title', $title)
        ->render();
echo View::factory('baseMenu')
        ->set('title', $title)
        ->set('menu', $menu)
        ->set('menu_bottom', $menu_bottom)
        ->render();
?>
<div id="grid">
    <table class="ui-table">
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Logins</th>
                <th>Last login</th>
                <th>Enabled</th>
                <th>Admin</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $_user): ?>
                <tr>
                    <td><?php echo $_user['username']; ?></td>
                    <td><?php echo $_user['email']; ?></td>
                    <td><?php echo $_user['logins']; ?></td>
                    <td><?php echo Date::loose_span($_user['last_login']); ?></td>
                    <td><?php
                        if ($_user['enabled']) {
                            echo '<i class="icon-check-sign"></i>';
                        }
                        ?></td>
                    <td><?php
                        if ($_user['admin']) {
                            echo '<i class="icon-check-sign"></i>';
                        }
                        ?></td>
                    <td>
                        <button class="disable ui-button-info ui-btn-mini" <?php
                        echo ($_user['enabled'] ? '' : 'style="display: none"');
                        ?>data-user-id="<?php echo $_user['id']; ?>">Disable</button>
                        <button class="enable ui-button-info ui-btn-mini" <?php
                        echo ($_user['enabled'] ? ' style="display: none"' : '');
                        ?>data-user-id="<?php echo $_user['id']; ?>">Enable</button>
                        <a href="user/edit/<?php echo $_user['id']; ?>" class="edit ui-button-primary ui-btn-mini" data-user-id="<?php echo $_user['id']; ?>">Edit</a>
                        <button class="delete ui-button-inverse ui-btn-mini" data-user-id="<?php echo $_user['id']; ?>">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="7" style="text-align: right"><a href="user/add" class="add ui-button-primary">Add user</a></th>
            </tr>
        </tfoot>
    </table>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $("button.disable").button({
            icons: {
                primary: 'icon-lock'
            }
        }).click(function() {
            var o = $(this);
            o.button('disable');
            $.owaka.api('api/user/disable/' + $(this).attr('data-user-id'), {}, function() {
                o.closest('td').siblings().eq(4).empty();
                o.closest('tr').find('button.disable').hide();
                o.closest('tr').find('button.enable').fadeIn();
                o.button('enable');
            });
        });
        $("button.enable").button({
            icons: {
                primary: 'icon-unlock'
            }
        }).click(function() {
            var o = $(this);
            o.button('disable');
            $.owaka.api('api/user/enable/' + $(this).attr('data-user-id'), {}, function() {
                o.closest('td').siblings().eq(4).html('<i class="icon-check-sign"></i>');
                o.closest('tr').find('button.enable').hide();
                o.closest('tr').find('button.disable').fadeIn();
                o.button('enable');
            });
        });
        $("a.edit").button({
            icons: {
                primary: 'icon-pencil'
            }
        });
        $("a.add").button({
            icons: {
                primary: 'icon-plus'
            }
        });
        $("button.delete").button({
            icons: {
                primary: 'icon-trash'
            }
        }).click(function() {
            var o = $(this);
            o.button('disable');
            $.owaka.api('api/user/delete/' + $(this).attr('data-user-id'), {}, function() {
                o.closest('tr').remove();
            });
        });
    });
</script>
<?php
echo View::factory('baseEnd')
        ->set('title', $title)
        ->render();