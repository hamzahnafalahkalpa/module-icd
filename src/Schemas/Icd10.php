<?php

namespace Hanafalah\ModuleIcd\Schemas;

use Hanafalah\ModuleIcd\Contracts\Schemas\Icd10 as ContractsIcd10;
use Illuminate\Database\Eloquent\Model;
use Hanafalah\ModuleIcd\ModuleIcd;

class Icd10 extends ModuleIcd implements ContractsIcd10
{
    protected string $__entity = 'Icd10';
    public $icd_10_model;

    public function installIcd10(string $year_release_id, ?Model $parent_model = null)
    {
        $this->__year_release = $year_release_id;
        $icd     = $this->getRelease10($year_release_id);
        $this->setIcdModel($this->Icd10Model())->setVersion('Icd10_' . $year_release_id);
        $this->installIcd($icd, $parent_model);
    }
}
