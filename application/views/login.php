<?php
$title       = 'login';
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
    <form action="api/auth/login" method="post">
        <input type="hidden" name="plain" value="1"/>
        <dl>
            <dt>User:</dt>
            <dd><input type="text" name="user"/></dd>

            <dt>Password:</dt>
            <dd><input type="password" name="password"/></dd>
        </dl>
        <hr>
        <input type="submit" value="Login"/>
    </form>
</div>
<?php
echo View::factory('baseEnd')
        ->set('title', $title)
        ->render();