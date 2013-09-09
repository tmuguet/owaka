<?php
$title       = 'Delete account';
$menu        = array();
$menu_bottom = array();
$menu[]      = array(
    'title' => 'Cancel',
    'href'  => 'dashboard/main',
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
    <form action="api/account/delete" method="post" class="ui-form">
        <button type="submit" data-icon="icon-trash">Delete account</button>
    </form>
</div>
<script type="text/javascript">
    $.owaka.formapi($('.ui-form'), function(data) {
        alert('Account deleted');
        document.location.href = 'dashboard/main';
    });
</script>
<?php
echo View::factory('baseEnd')
        ->set('title', $title)
        ->render();