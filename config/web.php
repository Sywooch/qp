<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'name' => 'qp',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'ru-RU',
    'timeZone' => 'Asia/Vladivostok',
    'components' => [
        'assetManager' => [
            'appendTimestamp' => true,
            'bundles' => [
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'js' => [],
                    'css' => [],
                ],
                'yii\bootstrap\BootstrapAsset' => [
                    'js' => [],
                    'css' => [],
                ],
            ],
        ],
        'cart' => [
            'class' => 'yz\shoppingcart\ShoppingCart',
            'cartId' => 'my_application_cart',
        ],
        'formatter' => [
            'dateFormat' => 'd.MM.yyyy',
            'timeFormat' => 'H:mm:ss',
            'datetimeFormat' => 'd.MM.yyyy H:mm',
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'KSZvMmDXsxtwTR6LUwK4H8rDz_V6wIar',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
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
        'authManager' => [
            'class' => 'yii\rbac\PhpManager',
            'defaultRoles' => ['guest'],
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'site/index',
                '<action:(reviews|contact|login|reg)>'=>'site/<action>',
                'p/<view:[a-zA-Z0-9-]+>' => 'page/index',
                '<module:backend><controller:default><action:(login|index)>'=>'<module>/default/<action>',
                '<controller:profile>/<action:edit>/phone'=>'profile/phone',
                '<controller:profile>/<action:edit>/password'=>'profile/password',
                '<controller:profile>/<action:confirm>/phone'=>'profile/confirm-phone',
                '<controller:profile>/<action:order>/view'=>'profile/view-order',
                '<controller:(catalog|product)>/<action:[\wd-]+>/<id:\d+>' => '<controller>/<action>',
                '<controller:(catalog|product)>/<action:[\wd-]+>/view/<id:\d+>' => '<controller>/<action>',
                /* Manager */
                'GET <controller:manager>/view-order/<id:\d+>' => '<controller>/view-order',
            ],
        ],
        'i18n' => [
            'translations' => [
                'eauth' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@eauth/messages',
                ],
            ],
        ],
        'eauth' => require('eauth.php'),

    ] + require(__DIR__ . '/db.php'),
    'modules' => [
        'backend' => [
            'class' => 'app\modules\backend\Admin',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.1.*', '212.122.7.*']
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
      'allowedIPs' => ['127.0.0.1']
    ];
}

return $config;
