<?php

declare(strict_types=1);

namespace App\Tools\Controllers;

use Core\Base\Controller;

class ToolsController extends Controller
{
    public function healthCheck(): array
    {
        return ['success' => true];
    }
}
