<?php

namespace Hanafalah\ModuleIcd\Schemas;

use Hanafalah\ModuleIcd\Contracts\Schemas\ICD10 as ContractsICD10;
use Hanafalah\ModuleIcd\Resources\ICD10\ViewICD10;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModuleIcd\ModuleIcd;

class ICD10 extends ModuleIcd implements ContractsICD10
{
    protected string $__entity = 'ICD';
    public static $icd_model;

    protected array $__resources = [
        'view' => ViewICD10::class
    ];

    public function installICD10(string $year_release_id, ?Model $parent_model = null)
    {
        $this->__year_release = $year_release_id;
        $icd     = $this->getRelease10($year_release_id);
        $this->setIcdModel($this->ICD10Model())->setVersion('ICD10_' . $year_release_id);
        $this->installICD($icd, $parent_model);
    }
}
