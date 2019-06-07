<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'rtx_nT29Ai9rkBwZ-wL_BrLTP56VQSSh',
        ],
        'session' => [
            'class' => 'yii\web\Session',
            'timeout' => 3600 * 3600,
            'cookieParams' => ['lifetime' => 7 * 24 * 60 * 60]
        ],
        'assetManager' => [
            'appendTimestamp' => true,
        ],
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'baseUrl' => 'https://admin.crispdata.co.in/',
            // Hide index.php
            'showScriptName' => false,
            // Use pretty URLs
            'enablePrettyUrl' => true,
            'enableStrictParsing' => false,
            'rules' => [
                [
                    'pattern' => 'site/edit-user/<id:\d+>',
                    'route' => 'site/edit-user'
                ],
                [
                    'pattern' => 'site/change-status/<id:\d+>',
                    'route' => 'site/change-status'
                ],
                [
                    'pattern' => 'site/delete-user/<id:\d+>',
                    'route' => 'site/delete-user'
                ],
                [
                    'pattern' => 'site/edit-client/<id:\d+>',
                    'route' => 'site/edit-client'
                ],
                [
                    'pattern' => 'site/change-status-client/<id:\d+>',
                    'route' => 'site/change-status-client'
                ],
                [
                    'pattern' => 'site/delete-client/<id:\d+>',
                    'route' => 'site/delete-client'
                ],
                [
                    'pattern' => 'contractor/add-contractor/<id:\d+>',
                    'route' => 'contractor/add-contractor'
                ],
                [
                    'pattern' => 'contractor/delete-contractor/<id:\d+>',
                    'route' => 'contractor/delete-contractor'
                ],
                [
                    'pattern' => 'site/create-size/<id:\d+>',
                    'route' => 'site/create-size'
                ],
                [
                    'pattern' => 'site/delete-size/<id:\d+>',
                    'route' => 'site/delete-size'
                ],
                [
                    'pattern' => 'site/create-fitting/<id:\d+>',
                    'route' => 'site/create-fitting'
                ],
                [
                    'pattern' => 'site/delete-fitting/<id:\d+>',
                    'route' => 'site/delete-fitting'
                ],
                [
                    'pattern' => 'site/create-make-em/<id:\d+>',
                    'route' => 'site/create-make-em'
                ],
                [
                    'pattern' => 'site/delete-make/<id:\d+>',
                    'route' => 'site/delete-make'
                ],
                [
                    'pattern' => 'site/create-make-civil/<id:\d+>',
                    'route' => 'site/create-make-civil'
                ],
                [
                    'pattern' => 'site/create-tender/<id:\d+>',
                    'route' => 'site/create-tender'
                ],
                [
                    'pattern' => 'site/create-item/<id:\d+>',
                    'route' => 'site/create-item'
                ],
                [
                    'pattern' => 'site/view-items/<id:\d+>',
                    'route' => 'site/view-items'
                ],
                [
                    'pattern' => 'site/delete-tender/<id:\d+>',
                    'route' => 'site/delete-tender'
                ],
                [
                    'pattern' => 'site/edit-item/<id:\d+>',
                    'route' => 'site/edit-item'
                ],
                [
                    'pattern' => 'site/delete-item/<id:\d+>/<tid:\d+>',
                    'route' => 'site/delete-item'
                ],
                [
                    'pattern' => 'site/aoctenders/<c:\d+>',
                    'route' => 'site/aoctenders'
                ],
                [
                    'pattern' => 'site/lasttenders/<c:\d+>',
                    'route' => 'site/lasttenders'
                ],
                [
                    'pattern' => 'site/archivetenders/<c:\d+>',
                    'route' => 'site/archivetenders'
                ],
                [
                    'pattern' => 'site/atenders/<c:\d+>',
                    'route' => 'site/atenders'
                ],
                [
                    'pattern' => 'products/uploadfile/<id:\d+>',
                    'route' => 'products/uploadfile'
                ],
                [
                    'pattern' => 'products/addaddress/<id:\d+>',
                    'route' => 'products/addaddress'
                ],
                [
                    'pattern' => 'mail/create-excel-items/<id:\d+>',
                    'route' => 'mail/create-excel-items'
                ],
                [
                    'pattern' => 'site/addsubdepartment/<id:\d+>',
                    'route' => 'site/addsubdepartment'
                ],
                [
                    'pattern' => 'site/adddivision/<id:\d+>',
                    'route' => 'site/adddivision'
                ]
            ]
        ],
    ],
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    /* $config['bootstrap'][] = 'debug';
      $config['modules']['debug'] = [
      'class' => 'yii\debug\Module',
      ]; */

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
