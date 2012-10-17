<?php
/**
 * Rapyd Framework
 *
 * @package  rapyd
 * @version  1.1
 * @author   Felice Ostuni <felice.ostuni@gmail.com>
 * @link     http://rapyd.com
 */

/**
 * define error reporting/level 
 */
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
ini_set( 'display_errors', 1 );
error_reporting( -1 );


/**
 * first of all, bench  
 */
define('RAPYD_BENCH_TIME',   microtime(true));
define('RAPYD_BENCH_MEMORY', memory_get_usage());


/**
 * define main folders path and root 
 */
define('DOC_ROOT',		str_replace('\\','/',dirname(__FILE__)).'/');
define('ROOT',			dirname(DOC_ROOT).'/');

//probabilmente non servono..  esiste un array fisso in rapyd_library ( o nell'autoloader),  
//per quanto riguarda i moduli e i widget viene fatto uno scandir e vengono aggiunti i path (se esistono cartelle)



define('APP_PATH',       ROOT.'application/');
define('MODULES_PATH',   ROOT.'modules/');
define('WIDGETS_PATH',   ROOT.'widgets/');
define('BASE_PATH',		 ROOT.'base/');
define('CORE_PATH',      ROOT.'core/');



/**
 * then change to the current working directory.
 */
chdir(dirname(__FILE__));



/**
 * core class
 */

require ROOT.'core/libraries/loader.php';
require ROOT.'base/libraries/loader.php';


/**
 * autoload system, error and exception handling
 */
spl_autoload_register(      array('loader_library','auto_load'));
set_error_handler(          array('rpd_error_library', 'error_handler'));
set_exception_handler(      array('rpd_error_library', 'exception_handler'));
register_shutdown_function( array('rpd', 'shutdown_handler'));

//set_error_handler( array( 'rpd_error_library', 'captureNormal' ) );
//set_exception_handler( array( 'rpd_error_library', 'captureException' ) );
//register_shutdown_function( array( 'rpd_error_library', 'captureShutdown' ) );



/**
 * configuration file
 */
//include_once(APP_PATH.'config.php');


/**
 * configuration file
 */
include_once(ROOT.'application/config.php');


/**
 * bootstrap
 */

rpd::init($config);
//rpd::connect();
rpd::run();
