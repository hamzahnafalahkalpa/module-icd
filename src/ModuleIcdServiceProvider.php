<?php

namespace Hanafalah\ModuleIcd;

use Hanafalah\LaravelSupport\Providers\BaseServiceProvider;

class ModuleIcdServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->registerMainClass(ModuleIcd::class)
            ->registerCommandService(Providers\CommandServiceProvider::class)
            ->registers([
                '*',
                'Services'  => function () {
                    $this->binds([
                        ModuleIcd::class       => ModuleIcd::class,
                        Contracts\ICD10::class => Schemas\ICD10::class
                    ]);
                }
            ]);
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
