<?php

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=crispdata',
            'username' => 'root',
            'password' => 'WRcqP^UiFk0#k0L',
            'charset' => 'utf8',
            'enableSchemaCache' => false,
        ],
        'sphinx' => [
            'class' => 'yii\sphinx\Connection',
            'dsn' => 'mysql:host=localhost;port=9306;',
            'username' => 'root',
            'password' => 'WRcqP^UiFk0#k0L',
        ],
        'assetManager' => [
            'appendTimestamp' => true,
        ],
        'mail' => [
            'class' => 'yashop\ses\Mailer',
            'viewPath' => '@common/mail',
            'access_key' => 'AKIAJOYFSXBHL7QCKZ4Q',
            'secret_key' => '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',
            'host' => 'email.us-east-1.amazonaws.com' // not required
        ],
        'awssdk' => [
            'class' => 'fedemotta\awssdk\AwsSdk',
            'credentials' => [
                //you can use a different method to grant access
                'key' => 'AKIAJGW5E2T6RA2WJDSA',
                'secret' => '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',
            ],
            'region' => 'ap-south-1', //i.e.: 'us-east-1'
            'version' => 'latest', //i.e.: 'latest'
        ],
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        /* 'mailer' => [
          'class' => 'yii\swiftmailer\Mailer',
          'viewPath' => '@common/mail',
          // send all mails to a file by default. You have to set
          // 'useFileTransport' to false and configure a transport
          // for the mailer to send real emails.
          'useFileTransport' => true,
          ], */
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => 'sajstyles21@gmail.com',
                'password' => 'mczqoglwsmtsiguc',
                'port' => '587',
                'encryption' => 'tls',
            ],
        ],
    ],
];
