<?php
//header('Location: http://finds.org.uk/downtime.html');
//exit;
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../app/'));
defined('CACHE_PATH')
    || define('CACHE_PATH', realpath(dirname(__FILE__) . '/../cache/'));
// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ?
    getenv('APPLICATION_ENV') : 'production'));
defined('SOLR_PATH')
	|| define('SOLR_PATH', realpath(dirname(__FILE__) . '/../solr/solr/'));
	
ini_set('memory_limit', '64M');
	
// Ensure library/ is on include_path
// directory setup and class loading
set_include_path('.' . PATH_SEPARATOR . '../library/'
. PATH_SEPARATOR . '../library/Pas/'
. PATH_SEPARATOR . '../library/Arc2/'
. PATH_SEPARATOR . '../library/EasyBib/'
. PATH_SEPARATOR . '../library/tcpdf/'
. PATH_SEPARATOR . '../app/models/'
. PATH_SEPARATOR . '../app/forms/'
. PATH_SEPARATOR . get_include_path());

/* include 'Zend/Loader.php';
Zend_Loader::registerAutoload();
 */

include 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setDefaultAutoloader(
create_function('$class',"include str_replace('_', '/', \$class) . '.php';")
);
$autoloader->registerNamespace('Pas_');
$autoloader->registerNamespace('ZendX_');
$autoloader->registerNamespace('EasyBib_');
$autoloader->suppressNotFoundWarnings(false);
$autoloader->setFallbackAutoloader(true);
$autoloader->pushAutoloader('HTMLPurifier_Bootstrap', 'autoload');
/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,array(
        'config' => array(
    APPLICATION_PATH . '/config/config.ini',
	APPLICATION_PATH . '/config/webservices.ini',
	APPLICATION_PATH . '/config/emails.ini'
    ))
);
$application->bootstrap()->run();