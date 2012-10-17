<?=rpd::view('demo/header')?>



  <div id="right">

    <div class="content">

     <?php if(isset($title)):?><h2><?php echo $title;?></h2><?php endif;?>

     <?php echo $content?>

			<div class="line"></div>
    </div>

  </div>

  <div class="line"></div>

<?php if ($code!=''): ?>
  <div class="code">
CONTROLLER <br />
    <?php echo $code?>
  </div>
  <div class="line"></div>
<?php endif;?>


<?=rpd::view('demo/footer')?>

