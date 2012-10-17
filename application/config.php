<?php

/**
 * system configurations
 *
 */
$config['index_page'] = ''; //use 'index.php' if htaccess not allowed
$config['basename'] = '/'; //it correspont to .htaccess RewriteBase  and should be "/foldername/" if you use the cms in subfolder of the docroot
$config['default_controller'] = 'welcome'; //default controller class to instance
$config['default_method']     = 'index'; //default controller method to call
$config['output_compression']  = 0; //output compression gzip level (if zlib is enabled) suggested level is 5 (0 means disabled)
$config['app_assets_uri']    = $config['basename'].'application/assets/';
$config['modules_assets_uri']= $config['basename'].'modules/{module}/assets/';
$config['widgets_assets_uri']= $config['basename'].'widgets/{widget}/assets/';
$config['core_assets_uri']   = $config['basename'].'core/assets/';
$config['cache_path']   = '';//DOC_ROOT.'cache/';

/*
$config['include_paths'][]  = APP_PATH;

//basic cms
$config['include_paths'][]  = MODULES_PATH.'users';
$config['include_paths'][]  = MODULES_PATH.'dbmanager';
$config['include_paths'][]  = MODULES_PATH.'filemanager';

$config['include_paths'][]  = MODULES_PATH.'demo';
$config['include_paths'][]  = MODULES_PATH.'blog';
$config['include_paths'][]  = MODULES_PATH.'forum';
$config['include_paths'][]  = MODULES_PATH.'shop';
$config['include_paths'][]  = MODULES_PATH.'qa';
$config['include_paths'][]  = MODULES_PATH.'livesupport';

$config['include_paths'][]  = WIDGETS_PATH.'component';
$config['include_paths'][]  = WIDGETS_PATH.'dataset';
$config['include_paths'][]  = WIDGETS_PATH.'datagrid';
$config['include_paths'][]  = WIDGETS_PATH.'dataform';
$config['include_paths'][]  = WIDGETS_PATH.'datafilter';
$config['include_paths'][]  = WIDGETS_PATH.'dataedit';
 

$config['include_paths'][]  = EXTENSIONS_PATH;
$config['include_paths'][]  = CORE_PATH;
 */

//default language is the one with segment => ''
//for others, segment is the first uri-segment that set language:  [/it]/controller/method/...
$config['languages'] = array(
	array('index'=>1, 'name'=>'english',    'locale'=>'en_US', 'dateformat'=>'m/d/Y', 'segment'=>''),
	array('index'=>2, 'name'=>'italiano',   'locale'=>'it_IT', 'dateformat'=>'d/m/Y', 'segment'=>'it'),
	array('index'=>3, 'name'=>'française',  'locale'=>'fr_FR', 'dateformat'=>'d/m/Y', 'segment'=>'fr' ),
	array('index'=>4, 'name'=>'česky',      'locale'=>'cs_CZ', 'dateformat'=>'d.m.Y', 'segment'=>'cs'),
);

$config['url_method'] = "uri"; //alternative: "qs"  define if rapyd will use uri or query string for its semantic
$config['timezone'] = 'Europe/Rome';



$config['routes'] = array(
	'page/(:any)' => 'frontend/page/$1',
	'spage/(:any)' => 'frontend/spage/$1',
	//'log/(:any)' => 'frontend/log/$1',
	//'product/(:num)/:str' => 'catalogmodule/product/$1';
);

$config['db']['hostname'] = "127.0.0.1";
$config['db']['username'] = "root";
$config['db']['password'] = "zippo";
$config['db']['database'] = "rapyd2";
$config['db']['dbdriver'] = "mysql";
$config['db']['dbprefix'] = "";
$config['db']['db_debug'] = true;

/**
 * custom configurations
 *
 */


