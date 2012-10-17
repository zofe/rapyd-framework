<?php header('HTTP/1.1 500 Internal Server Error'); ?>
<html>
<head>
    <title>Application Error</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: Lucida Grande, Verdana, Geneva, Sans-serif;
            font-size: 14px;
            color: #4F5155;
            background-color: #fff;
        }
        small, .error {background-color: #ececec; padding: 5px; white-space:nowrap}
    </style>
</head>
<body>
    <h1>Application Error</h1>
    <p class="error"><?php echo nl2br(error_library::$error_message)?></p>
</body>
</html>
