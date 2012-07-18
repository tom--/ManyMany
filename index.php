<?php

// local.php is not version controlled. It has things such as YII_DEBUG
@include 'local.php';

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

Yii::$classMap=array(
    'CActiveFinder' => 'protected/extensions/classMap/CActiveFinder.php',
);

Yii::createWebApplication(__DIR__ . '/protected/config/main.php')->run();
