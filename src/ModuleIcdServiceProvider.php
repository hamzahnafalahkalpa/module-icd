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
}
