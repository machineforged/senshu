<?php
declare(strict_types = 1);

use LapisAngularis\Senshu\Board\App\SenshuKernel;

require_once __DIR__ . '/../vendor/autoload.php';

$environment = 'prod';
$senshuApp = new SenshuKernel($environment);

$senshuApp->initialize();
