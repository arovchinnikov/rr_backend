<?php

declare(strict_types=1);

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;

function dd_var(...$var): void
{
    $debugData = [];
    foreach (func_get_args() as $arg) {
        $cloner = new VarCloner();
        $dumper = new CliDumper();
        $debugData[] =  $dumper->dump($cloner->cloneVar($arg), true);
    }

    $worker = \Core\App::getWorker();
    $worker->respondString(implode(PHP_EOL, $debugData));
    $worker->getWorker()->stop();
    die();
}
