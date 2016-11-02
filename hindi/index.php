<?php
ini_set('max_execution_time', 6);
error_reporting( E_ALL & ~E_NOTICE & ~E_WARNING );
// Define base path obtainable throughout the whole application
defined('BASE_PATH') || define('BASE_PATH', realpath(dirname(__FILE__)));
// Define path to application directory
defined('APPLICATION_PATH')|| define('APPLICATION_PATH', BASE_PATH . '/application');
// Define application environment
defined('APPLICATION_ENV')|| define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

set_include_path(implode(PATH_SEPARATOR, array(realpath(APPLICATION_PATH . '/../library'), get_include_path(), )));
/** Zend_Application */
require_once 'Zend/Application.php';
// Create application, bootstrap, and run
$application = new Zend_Application( APPLICATION_ENV,APPLICATION_PATH . '/configs/application.ini');
Zend_Registry::set("db", null);
// Get user agent.
preg_match("/iPhone|Android|iPad|iPod|webOS/", $_SERVER['HTTP_USER_AGENT'], $matches);
$os = current($matches);

switch($os){
   case 'iPhone': $GLOBALS['user_agent'] = 'ios'; break;
   case 'Android': $GLOBALS['user_agent'] = 'andriod'; break;
   case 'iPad': $GLOBALS['user_agent'] = 'ios'; break;
   case 'iPod': $GLOBALS['user_agent'] = 'ios'; break;
   case 'webOS': $GLOBALS['user_agent'] = 'web'; break;
}
$application->bootstrap()->run();
Zend_Registry::$_db->closeConnection();
echo '<!-- '. $GLOBALS['user_agent'] . ' -->';
exit(0);
?>
