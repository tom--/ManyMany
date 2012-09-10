<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors',1);
defined('YII_DEBUG') or define('YII_DEBUG',true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);


// local.php is not version controlled. It has things such as YII_DEBUG
@include 'local.php';

// absolte path to framework so all apps and versions use the same yii
/** @noinspection PhpIncludeInspection */
require_once(__DIR__ . '/../yii/framework/YiiBase.php');

class Yii extends YiiBase {
	/**
	 * @return \CApplication|\CWebApplication
	 */
	public static function app() {
		return parent::app();
	}
}

// Ignore this function for the exercise, its my trace shortcut function. :)
function tr($tracevar, $description='', $exit=0)
{
	Yii::trace(CVarDumper::dumpAsString($tracevar),'<b>DebugTrace: '.$description.'</b>');
	if($exit)
		Yii::app()->end();
}

Yii::$classMap=array(
    'CActiveFinder' => 'protected/extensions/classMap/CActiveFinder.php',
);

Yii::createWebApplication(__DIR__ . '/protected/config/main.php')->run();
