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
    <form action="api/auth/login" method="post" class="ui-form">
        <fieldset>
            <div class="field">
                <label for="user">User:</label>
                <input type="text" name="user" id="user"/>
            </div>

            <div class="field">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password"/>
            </div>
        </fieldset>
        <button type="submit">Login</button>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $(".ui-form :submit").button({
            icons: {
                primary: "ui-icon-unlocked"
            }
        });
    });
    $.owaka.formapi($('.ui-form'), function(data) {
        document.location = data.goto;
    });
</script>
<?php
echo View::factory('baseEnd')
        ->set('title', $title)
        ->render();