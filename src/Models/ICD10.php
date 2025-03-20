<?php

namespace Hanafalah\ModuleIcd\Models;

use Hanafalah\ModuleIcd\Resources\ICD10\ViewICD10;

class ICD10 extends ICD
{
    protected $table = 'icds';

    protected static function booted(): void
    {
        parent::booted();
        static::addGlobalScope('icd_10', function ($query) {
            $query->whereLike('version', 'ICD10');
        });
    }

    public function toViewApi()
    {
        return new ViewICD10($this);
    }
}
