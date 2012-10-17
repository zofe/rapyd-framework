<?php header('HTTP/1.1 500 Internal Server Error'); ?>
<html>
<head>
    <title>rapyd framework</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: Lucida Grande, Verdana, Geneva, Sans-serif;
            font-size: 14px;
            color: #4F5155;
            background-color: #fff;
        }
    </style>
</head>
<body>
    <h1><?php echo $heading?></h1>
	<h3><?=rpd::lang('app.payoff')?></h3>

    <p><?php echo $message?></p>
	
<hr />
<small>
	time: {time} <br />
	memory: {memory} <br />
	included_files: {included_files} <br />
	cached_files: {cached_files} <br />
</small>

</body>
</html>