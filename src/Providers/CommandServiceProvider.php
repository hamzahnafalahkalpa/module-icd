<?php

declare(strict_types=1);

namespace Hanafalah\ModuleIcd\Providers;

use Illuminate\Support\ServiceProvider;
use Hanafalah\ModuleIcd\Commands as Commands;

class CommandServiceProvider extends ServiceProvider
{
    private $commands = [
        Commands\InstallMakeCommand::class,
        Commands\ScrappingMakeCommand::class,
        Commands\ICDTranslateCommand::class
    ];


    public function register()
    {
        $this->commands(config('module-icd.commands', $this->commands));
    }
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */

    public function provides()
    {
        return $this->commands;
    }
}
