<?php

declare(strict_types=1);

use Crmleaf\Payroll\Tools\TdsCalculator\Http\Controllers\TdsCalculatorController;
use Illuminate\Support\Facades\Route;

/*
 * Loaded by TdsCalculatorServiceProvider only when config('tds-calculator.route.enabled')
 * is true, so requiring the package never adds a URL on its own.
 */

/** @var \Illuminate\Contracts\Config\Repository $config */
$config = app('config');

Route::middleware((array) $config->get('tds-calculator.route.middleware', ['web']))
    ->prefix((string) $config->get('tds-calculator.route.prefix', 'tools'))
    ->group(static function () use ($config): void {
        Route::match(['get', 'post'], '/tds-calculator', TdsCalculatorController::class)
            ->name((string) $config->get('tds-calculator.route.name', 'tds-calculator'));
    });
