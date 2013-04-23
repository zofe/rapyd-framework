<?=rpd::run('demo/header')?>

	<link rel="stylesheet" type="text/css" href="<?=rpd::config('core_assets_uri')?>prettify/prettify.css" media="screen" />
	<script type="text/javascript" language="javascript" src="<?=rpd::config('core_assets_uri')?>prettify/prettify.js"></script>


  <div id="right">

    <div class="content">

     <?if(isset($title)):?><h2><?=$title;?></h2><?endif;?>

     <?=$content?>

			<div class="line"></div>
    </div>

  </div>

  <div class="line"></div>

<?if ($code!=''): ?>
<pre class="prettyprint linenums php-css">
<?=htmlspecialchars($code)?>
</pre>
<script type="text/javascript">
	prettyPrint();
</script>
  <div class="line"></div>
<?endif;?>


<?=rpd::run('demo/footer')?>

