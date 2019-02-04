<?php

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=crispdata',
            'username' => 'root',
            'password' => 'onlyican21<>',
            'charset' => 'utf8',
        ],
        'user' => [
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
