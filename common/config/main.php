<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => [
                'name' => '_identity',
                'path' => '/',
                // 注意：如果使用子域名（如 admin.yoursite.com 和 www.yoursite.com），
                // 取消下面这行注释并设置为 '.yoursite.com'（注意前面有点）
                // 'domain' => '.yoursite.com',
                'httpOnly' => true,
            ],
        ],
    ],
];
