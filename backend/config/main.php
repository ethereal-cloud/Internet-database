<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'on beforeRequest' => function ($event) {
        // 允许 admin 和 employee 访问 backend
        if (!Yii::$app->user->isGuest) {
            $role = Yii::$app->user->identity->role ?? null;
            // 只有 admin 和 employee 可以访问 backend
            if (!in_array($role, ['admin', 'employee'])) {
                // customer 或其他角色访问 backend，重定向到 frontend
                Yii::$app->session->setFlash('error', '您没有权限访问管理后台。');
                Yii::$app->response->redirect(['/frontend/web/index.php'])->send();
                Yii::$app->end();
            }
        }
    },
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
    ],
    'params' => $params,
];
