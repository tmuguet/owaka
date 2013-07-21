<?php
$title         = 'edit account';
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
    <form action="api/account/edit" method="post" class="ui-form">
        <fieldset>
            <div class="field">
                <label for="password">New password:</label>
                <input type="password" name="password" id="password"/>
            </div>
        </fieldset>
        <button type="submit">Edit account</button>
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