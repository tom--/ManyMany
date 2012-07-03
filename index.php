<?php

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

Yii::createWebApplication(__DIR__ . '/protected/config/main.php')->run();
