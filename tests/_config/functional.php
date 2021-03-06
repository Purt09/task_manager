<?php
return yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../config/common.php'),
    require(__DIR__ . '/../../config/common-local.php'),
    require(__DIR__ . '/../../config/web.php'),
    require(__DIR__ . '/../../config/web-local.php'),
    require(__DIR__ . '/../../config/test.php'),
    require(__DIR__ . '/../../config/test-local.php'),
    [
        'components' => [
            'request' => [
                'scriptFile' => dirname(dirname(__DIR__)) . '/web/index-test.php',
            ],
        ],
    ]
);