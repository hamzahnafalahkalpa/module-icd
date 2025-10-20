<?php

namespace Hanafalah\ModuleIcd\Contracts;

use Illuminate\Database\Eloquent\Model;
use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;

interface ModuleIcd extends DataManagement
{
    public function oauth(): object;
    public function installIcd(object $icd, ?Model $parent_model = null);
    public function setIcdModel(Model $icd): self;
    public function setVersion(string $version): self;
    public function getRelease10(?string $release_id = null, ?string $code = null): object;
    public function getRelease(?string $end_point = null): object;
    public function setYearReleaseId(string $year): self;
}
