<?php

namespace Hanafalah\ModuleIcd\Contracts\Schemas;

use Illuminate\Database\Eloquent\Model;

interface Icd10 extends Icd
{
    public function installIcd10(string $year_release_id, ?Model $parent_model = null);
}
