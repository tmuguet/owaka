<?php
if (!isset($menu)) {
    $menu = array();
}
if (!isset($menu_bottom)) {
    $menu_bottom = array();
}
if (!isset($menu_bottom_advanced)) {
    $menu_bottom_advanced = array();
}
if (Auth::instance()->logged_in()) {
    array_unshift(
            $menu, array(
        'title' => 'Home',
        'href'  => 'dashboard/main',
        'img'   => 'home',
            )
    );
    $menu_bottom_advanced[] = array(
        'title'   => 'account',
        'submenu' => array(
            array(
                'title' => 'edit',
                'href'  => 'account/edit'
            ),
            array(
                'title' => 'delete',
                'href'  => 'account/delete'
            )
        )
    );
    if (Auth::instance()->logged_in(Owaka::AUTH_ROLE_ADMIN)) {
        $menu_bottom_advanced[] = array(
            'title'   => 'users',
            'submenu' => array(
                array(
                    'title' => 'list',
                    'href'  => 'user/list'
                ),
                array(
                    'title' => 'add',
                    'href'  => 'user/add'
                )
            )
        );
    }
    $menu_bottom[] = array(
        'title' => 'logout',
        'href'  => 'logout',
        'img'   => 'signout',
    );
}
?>
<div id="left-panel">
    <div id="owaka"><h1>owaka</h1></div>
    <div id="menu">
        <?php echo Helper_View::processMenu($menu) ?>
    </div>
    <div id="menu-bottom">
        <?php echo Helper_View::processMenu($menu_bottom) ?>
        <?php
        if (sizeof($menu_bottom_advanced) > 0):
            ?>
            <a href="javascript:void(0)" onclick="$('#menu-bottom-advanced').slideToggle();">more...</a>
            <div id="menu-bottom-advanced">
                <?php echo Helper_View::processMenu($menu_bottom_advanced) ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<div id="top-panel">
    <h2><?php echo $title; ?></h2>
</div>
