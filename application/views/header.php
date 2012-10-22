<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">

<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
	<meta name="author" content="Rapyd Team" />
	<style>
	body {
		padding-top: 60px;
	}
	</style>
	<link rel="stylesheet" type="text/css" href="<?=rpd::config('core_assets_uri')?>bootstrap/css/bootstrap.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?=rpd::config('core_assets_uri')?>bootstrap/css/bootstrap-responsive.css" media="screen" />
	<script type="text/javascript" language="javascript" src="<?=rpd::config('core_assets_uri')?>jquery/jquery.js"></script>
	<script type="text/javascript" language="javascript" src="<?=rpd::config('core_assets_uri')?>jquery/jquery.cookie.js"></script>
	<script type="text/javascript" language="javascript" src="<?=rpd::config('core_assets_uri')?>bootstrap/js/bootstrap.js"></script>

    <rpd run="app/head">


</head>

<body>



	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				<a class="brand" href="<?=rpd::url('');?>"><?=rpd::lang('app.sitename')?></a>
				<div class="nav-collapse">
					<ul class="nav">
						<li<?=rpd::current_page('page',' class="active"');?>><a href="<?=rpd::url('');?>">home</a></li>
						<?if (count(rpd::config('modules'))): ?>
							<?foreach(rpd::config('modules') as $module):?>
							<?if(isset($module["frontend_tab"])):?>
								<li<?=rpd::current_page($module["frontend_tab"],' class="current-tab"');?>><?=rpd::anchor($module["frontend_tab"],$module["label"])?></li>
							<?endif;?>
							<?endforeach;?>
						<?endif;?>
					</ul>
				</div><!--/.nav-collapse -->


				<?if(count(rpd::get_lang('array'))>1):?>
					<div class="btn-group pull-right">
						<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
						<i class="icon-globe"></i> <?=rpd::get_lang('name')?>
						<span class="caret"></span>
						</a>
						<ul class="dropdown-menu">


							<?foreach(rpd::get_lang('array') as $lang):?>

									<?if(isset($lang["is_current"])):?>
										<li class="active"><a href="#"><?=$lang['name']?></a></li>
									<?else:?>
										<li><a href="<?=rpd::url('',$lang['segment'])?>"><?=$lang['name']?></a></li>
									<?endif;?>

							<?endforeach;?>

						</ul>
					</div>
				<?endif;?>
                


                

			</div>
		</div>
	</div>


<div class="container">

	
    <rpd run="users/logged_info">

	<div class="row-fluid">
		<div class="span12">


			<?//=strftime("%A %d %B %Y"); ?>
			<?//$rpd->site_payoff?>


			<?/*<h1><a href="<?=rpd::url('');?>"><?=rpd::lang('cms.site_name')?></a> <?=($rpd->title!='') ? "/ ".$rpd->title : ""?></h1>*/?>


			<ul class="nav nav-tabs">
				<?=rpd::run('{controller}/menu')?>
			</ul>
