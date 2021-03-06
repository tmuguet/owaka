<?php
$title       = 'Add user';
$menu        = array();
$menu_bottom = array();
$menu[]      = array(
    'title' => 'Cancel',
    'href'  => 'user/list',
    'img'   => 'off',
);

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
    <form action="api/user/add" method="post" class="ui-form">
        <fieldset>
            <div class="field">
                <label for="email">Email:</label>
                <input type="text" name="email" id="email"/>
            </div>
            <div class="field">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username"/>
            </div>
            <div class="field">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password"/>
            </div>
            <div class="field">
                <label for="admin">Admin</label>
                <input type="checkbox" name="admin" id="admin" value="1"/>
            </div>
        </fieldset>
        <button type="submit" data-icon="icon-save">Add user</button>
    </form>
</div>
<script type="text/javascript">
    $.owaka.formapi($('.ui-form'), function(data) {
        alert('User added');
        document.location.href = 'user/list';
    });
</script>
<?php
echo View::factory('baseEnd')
        ->set('title', $title)
        ->render();