<?php
//module config vars

$module = array();
$module['name'] = 'demo';
$module['label'] = 'demo';
//$module['admin_tab'] = 'admin_demo';
$module['frontend_tab'] = 'demo';
$module['assets_path'] = str_replace('{module}', $module['name'], $config['modules_assets_uri']);



