<!DOCTYPE html>
<html>
    <head>
        <title>owaka</title>
        <base href="/owaka/">

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

        <link rel="stylesheet" type="text/css" media="screen" href="css/fluid960gs/reset.css" />
        <!--<link rel="stylesheet" type="text/css" media="screen" href="css/fluid960gs/text.css" />-->
        <link rel="stylesheet" type="text/css" media="screen" href="css/fluid960gs/960.css" />
        <!--[if IE 6]><link rel="stylesheet" type="text/css" href="css/fluid960gs/ie6.css" media="screen" /><![endif]-->
        <!--[if IE 7]><link rel="stylesheet" type="text/css" href="css/fluig960gs/ie.css" media="screen" /><![endif]-->
        <link rel="stylesheet" type="text/css" media="screen" href="css/smoothness/jquery-ui-1.10.2.custom.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="css/owaka.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="css/v960.css" />

        <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.10.2.custom.min.js"></script>
        <script type="text/javascript" src="js/owaka.js"></script>

        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
        <link rel="icon" href="favicon.ico" type="image/x-icon">
    </head>
    <body>

        <div class="container container_12 container_v12">
            <?php
            foreach ($widgets as $widget) {
                echo $widget->render();
            }
            ?>
        </div>
    </body>
</html>