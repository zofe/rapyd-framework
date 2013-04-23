<?php header("HTTP/1.1 404 Not Found"); ?>
<html>
<head>
    <title>404: Page Not Found</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: Lucida Grande, Verdana, Geneva, Sans-serif;
            font-size: 14px;
            color: #4F5155;
            background-color: #fff;
        }
        a {
            color: #003399;
            background-color: transparent;
            text-decoration: none;
            font-weight: normal;
        }
        a:visited {
            color: #003399;
            background-color: transparent;
            text-decoration: none;
        }
        a:hover {
            color: #000;
            text-decoration: underline;
            background-color: transparent;
        }
        small, .error {background-color: #ececec; padding: 5px; display: inline}
    </style>
</head>
<body>
    <h1>404: Page Not Found</h1>
    <p class="error">The page you are trying to access has either moved or doesn't exist.</p>
    <p><a href="<?php echo rpd::url('/');?>">Click here to return to main</a></p>
</body>
</html>
