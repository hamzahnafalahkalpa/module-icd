<?php

namespace Hanafalah\ModuleIcd;

use Hanafalah\LaravelSupport\Providers\BaseServiceProvider;

class ModuleIcdServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->registerMainClass(ModuleIcd::class)
            ->registerCommandService(Providers\CommandServiceProvider::class)
            ->registers(['*']);
    }

    protected function dir(): string
    {
        return __DIR__ . '/';
    }

    protected function migrationPath(string $path = ''): string
    {
        return database_path($path);
    }
}
