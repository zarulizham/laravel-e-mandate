<?php

namespace ZarulIzham\EMandate;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use ZarulIzham\EMandate\Commands\EMandateCommand;

class EMandateServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-e-mandate')
            ->hasConfigFile()
            ->hasViews()
            ->hasRoutes('web', 'api')
            ->hasMigration('create_e_mandate_transactions_table')
            ->hasCommand(EMandateCommand::class);
    }
}
