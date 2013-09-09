<?php
$title       = 'Login';
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
        <button type="submit" data-icon="icon-signin">Login</button>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        var form = $('.ui-form');
        $.owaka.callbacks.init(form);

        form.submit(function() {
            $.owaka.callbacks.before(form);

            $.post('api/auth/challenge', {user: $("#user").val()}, function(challenge) {
                var response = CryptoJS.HmacSHA256($("#password").val(), challenge.challenge).toString();

                $.post(form.attr('action'), {user: $("#user").val(), response: response}, function(data) {
                    $('#' + form.attr('id') + '-helper').html('');
                    document.location.href = data.goto;
                }, "json").fail(function(jqXHR, textStatus, errorThrown) {
                    $.owaka.callbacks.apifail(form, jqXHR, textStatus, errorThrown);
                });
            }, "json").fail(function(jqXHR, textStatus, errorThrown) {
                $.owaka.callbacks.apifail(form, jqXHR, textStatus, errorThrown);
            });
            return false;
        });
    });
</script>
<?php
echo View::factory('baseEnd')
        ->set('title', $title)
        ->render();