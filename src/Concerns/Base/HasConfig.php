<?php

namespace Hanafalah\ModuleIcd\Concerns\Base;

trait HasConfig
{
    protected array $__module_icd_config = [];

    public function getConfigLang(): string
    {
        return $this->__module_icd_config['lang'];
    }

    public function getConfigApiVersion(): string
    {
        return $this->__module_icd_config['api_version'];
    }

    public function getConfigAuthentication(): array
    {
        return $this->__module_icd_config['authentication'];
    }

    protected function setIcdConfig(): self
    {
        $this->__module_icd_config = config('module-icd');
        return $this;
    }
}
