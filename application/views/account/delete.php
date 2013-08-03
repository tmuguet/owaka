<?php
$title         = 'delete account';
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
    <form action="api/account/delete" method="post" class="ui-form">
        <button type="submit">Delete account</button>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $(".ui-form :submit").button({
            icons: {
                primary: "ui-icon-trash"
            }
        });
    });
    $.owaka.formapi($('.ui-form'), function(data) {
        alert('Account deleted');
        document.location = 'dashboard/main';
    });
</script>
<?php
echo View::factory('baseEnd')
        ->set('title', $title)
        ->render();