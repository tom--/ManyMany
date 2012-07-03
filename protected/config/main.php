<?php

Yii::setPathOfAlias('shared', realpath(__DIR__ . '/../../../shared'));

return call_user_func(function () {
	$cfg = array(
		'name' => 'Many Many CGV',
		'basePath' => __DIR__  . DIRECTORY_SEPARATOR . '..',
		'runtimePath' => __DIR__  . '/../../../runtime',

		'preload' => array('log'),

		'import' => array(
			'application.models.*',
			'application.components.*',
		),

		'modules' => array(
			'gii' => array(
				'class' => 'system.gii.GiiModule',
				'password' => 'ManyMany',
				'ipFilters' => array('127.0.0.1', '::1'),
			),
		),

		'components' => array(
			'user' => array(
				// enable cookie-based authentication
				'allowAutoLogin' => true,
			),
			/*
			'urlManager'=>array(
				'urlFormat'=>'path',
				'rules'=>array(
					'<controller:\w+>/<id:\d+>'=>'<controller>/view',
					'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
					'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
				),
			),
			*/
			'db' => array(
				'connectionString' => 'mysql:host=localhost;dbname=many_many',
				'emulatePrepare' => true,
				'username' => '',
				'password' => '',
				'charset' => 'utf8',
				'enableParamLogging' => true,
			),
			'errorHandler' => array(
				'errorAction' => 'site/error',
			),
			'log' => array(
				'class' => 'CLogRouter',
				'routes' => array(
					'file' => array(
						'class' => 'CFileLogRoute',
						'levels' => 'error, warning',
					),
				),
			),
		),

		'params' => array(
		),
	);
	foreach (array(
		__DIR__ . '/local.php',
		__DIR__ . '/passwords.php'
	) as $file) {
		$moreCfg = @include($file);
		if (is_array($moreCfg) && $moreCfg) {
			$cfg = CMap::mergeArray($cfg, $moreCfg);
		}
	}
	return $cfg;
});
