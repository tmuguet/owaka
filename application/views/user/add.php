<?php
$title         = 'add user';
$menu          = array();
$menu_bottom   = array();
$menu_bottom[] = array(
    'title' => 'cancel',
    'href'  => 'dashboard/main',
    'img'   => 'freepik/powerbutton',
    'alt'   => 'Cancel'
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
        <button type="submit">Add user</button>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $(".ui-form :submit").button({
            icons: {
                primary: "ui-icon-disk"
            }
        });
    });
</script>
<?php
echo View::factory('baseEnd')
        ->set('title', $title)
        ->render();