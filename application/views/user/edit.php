<?php
$title       = 'edit user ' . $user->username;
$menu        = array();
$menu_bottom = array();
$menu[]      = array(
    'title' => 'cancel',
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
    <form action="api/user/edit/<?php echo $user->id; ?>" method="post" class="ui-form">
        <fieldset>
            <div class="field">
                <label for="password">New password:</label>
                <input type="password" name="password" id="password"/>
            </div>
        </fieldset>
        <button type="submit" data-icon="icon-save">Edit account</button>
    </form>
</div>
<script type="text/javascript">
    $.owaka.formapi($('.ui-form'), function(data) {
        alert('User edited');
        document.location.href = 'dashboard/main';
    });
</script>
<?php
echo View::factory('baseEnd')
        ->set('title', $title)
        ->render();