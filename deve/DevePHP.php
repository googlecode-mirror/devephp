!<?php
// +----------------------------------------------------------------------
// | DevePHP [ EASIER EFFICIENT SECURE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://devephp.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yearnfar <yearnfar@gmail.com>
// +----------------------------------------------------------------------

error_reporting(E_ALL | E_STRICT);

define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
define('BP', dirname(dirname(__FILE__)));
define('DEVE_PATH', BP . DS . 'deve');
define('APP_PATH', BP . DS . 'app');

defined('APPLICATION_ENV')
	|| define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));
	
if('development' == APPLICATION_ENV){
    $paths[] = DEVE_PATH . DS . 'Source' . DS . 'Core';
}else{
	include DEVE_PATH . DS . 'Code' . DS . '~Core.php';
}

$paths[] = DEVE_PATH . DS . 'Source' . DS . 'Libs';
$paths[] = APP_PATH . DS . 'Libs';

set_include_path(implode(PS, $paths) . PS . get_include_path());

include DEVE_PATH . '/Source/Libs/Zend/Loader.php';
include DEVE_PATH . '/Source/Libs/Zend/Loader/Autoloader.php';

$appliaction = new Zend_Application(APPLICATION_ENV, APP_PATH . DS . 'Config/config.ini');


