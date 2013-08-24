<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?>/owaka</title>
        <base href="<?php echo URL::base(); ?>">

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
#ifdef DEVELOPMENT
        <link rel="stylesheet" type="text/css" media="screen" href="css/fluid960gs/reset.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="css/custom-theme/jquery-ui-1.10.2.custom.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="css/font-awesome.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="css/grid.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="css/owaka.css" />
#else
        <link rel="stylesheet" type="text/css" media="screen" href="css/all-min.css" />
#endif

#ifdef DEVELOPMENT
        <script type="text/javascript" src="js/jquery-1.9.1.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.10.2.custom.js"></script>
        <script type="text/javascript" src="js/owaka.js"></script>
        <script type="text/javascript" src="js/owaka.designer.js"></script>
        <script type="text/javascript" src="js/jquery.sparkline.min.js"></script>
        <script type="text/javascript" src="js/crypto-js/hmac-sha256.js"></script>
#else
        <script type="text/javascript" src="js/all-min.js"></script>
#endif

        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
        <link rel="icon" href="favicon.ico" type="image/x-icon">
    </head>
    <body>
        <div id="overlay" class="ui-widget-overlay ui-helper-hidden"> </div>
