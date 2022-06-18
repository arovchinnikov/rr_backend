<?php

declare(strict_types=1);

const ROOT = __DIR__;

require ROOT . '/vendor/autoload.php';
require ROOT . '/common/bootstrap.php';

$app = new \Core\App();
$app->run();
