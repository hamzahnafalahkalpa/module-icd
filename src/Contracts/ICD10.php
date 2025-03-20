<?php

namespace Zahzah\ModuleIcd\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ICD10 extends ModuleIcd{
    public function installICD10(string $year_release_id,? Model $parent_model = null);
}
