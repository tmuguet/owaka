<?php
$title         = 'edit account';
$menu          = array();
$menu_bottom   = array();
$menu_bottom[] = array(
    'title' => 'cancel',
    'href'  => 'dashboard/main',
    'img'   => 'off',
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
    <form action="api/account/edit" method="post" class="ui-form">
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
        alert('Account edited');
        document.location = 'dashboard/main';
    });
</script>
<?php
echo View::factory('baseEnd')
        ->set('title', $title)
        ->render();