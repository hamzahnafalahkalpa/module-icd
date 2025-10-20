<?php

namespace Hanafalah\ModuleIcd\Schemas;

use Hanafalah\ModuleIcd\Contracts\Schemas\Icd as ContractsIcd;
use Hanafalah\ModuleIcd\ModuleIcd;

class Icd extends ModuleIcd implements ContractsIcd
{
    protected string $__entity = 'Icd';
    public $icd_model;
}
