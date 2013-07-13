<!DOCTYPE html>
<html>
    <head>
        <title>owaka</title>
        <base href="/owaka/">

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

        <link rel="stylesheet" type="text/css" media="screen" href="css/fluid960gs/reset.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="css/smoothness/jquery-ui-1.10.2.custom.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="css/grid.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="css/owaka.css" />

        <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.10.2.custom.min.js"></script>
        <script type="text/javascript" src="js/owaka.js"></script>

        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
        <link rel="icon" href="favicon.ico" type="image/x-icon">
    </head>
    <body>
        <div id="overlay" class="ui-widget-overlay ui-helper-hidden"> </div>
        <div id="left-panel">
            <div id="owaka"><h1>owaka</h1></div>
        </div>
        <div id="top-panel">
            <h2>add a new user</h2>
        </div>
        <div id="grid">
            <form action="api/user/add" method="post">
                <dl>
                    <dt>Email:</dt>
                    <dd><input type="text" name="email"/></dd>
                    <dt>Username:</dt>
                    <dd><input type="text" name="username"/></dd>
                    <dt>Password:</dt>
                    <dd><input type="password" name="password"/></dd>
                    <dt>Admin</dt>
                    <dd><input type="checkbox" name="admin" value="1"/></dd>
                </dl>
                <hr>
                <input type="submit" value="Add user"/>
            </form>
        </div>
    </body>
</html>