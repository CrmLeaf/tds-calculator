<?php

declare(strict_types=1);

namespace Crmleaf\Payroll\Tools\TdsCalculator;

use Crmleaf\Payroll\Calculators\TdsCalculator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

/**
 * Registers TDS Calculator with a Laravel application.
 *
 * Everything this provider adds is either inert or off by default: the
 * calculator binding, one Blade component and a set of publishable paths. The
 * HTTP route is opt-in through `config('tds-calculator.route.enabled')`, because a
 * package that installs a public URL into your application without being asked
 * is a package that has made a routing decision on your behalf.
 */
final class TdsCalculatorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/tds-calculator.php', 'tds-calculator');

        // A singleton because the calculator is stateless and its rate
        // repository parses the statutory tables once per process.
        $this->app->singleton(TdsCalculator::class, static fn (): TdsCalculator => new TdsCalculator());
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'tds-calculator');

        // One component per tool: resources/views/components/tds-calculator.blade.php,
        // written as <x-crmleaf::tds-calculator />. Every tool registers the same
        // 'crmleaf' prefix, so fifteen independently installed packages share one
        // component namespace instead of contributing fifteen aliases.
        Blade::anonymousComponentPath(__DIR__.'/../resources/views/components', 'crmleaf');

        if ($this->routeEnabled()) {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/tds-calculator.php' => config_path('tds-calculator.php'),
            ], 'tds-calculator-config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/tds-calculator'),
            ], 'tds-calculator-views');

            $this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/tds-calculator'),
            ], 'tds-calculator-assets');
        }
    }

    /**
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [
            TdsCalculator::class,
        ];
    }

    private function routeEnabled(): bool
    {
        /** @var \Illuminate\Contracts\Config\Repository $config */
        $config = $this->app->make('config');

        return (bool) $config->get('tds-calculator.route.enabled', false);
    }
}
