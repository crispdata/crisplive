<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'rtx_nT29Ai9rkBwZ-wL_BrLTP56VQSSh',
        ],
		'urlManager' => [
			'class' => 'yii\web\UrlManager',
			// Hide index.php
			'showScriptName' => false,
			// Use pretty URLs
			'enablePrettyUrl' => true,
			'rules' => [
			],
		],
    ],
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
