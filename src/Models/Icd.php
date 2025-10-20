<?php

namespace Hanafalah\ModuleIcd\Models;

use Hanafalah\ModuleDisease\Models\Disease;
use Hanafalah\ModuleIcd\Resources\Icd\ViewIcd;

class Icd extends Disease
{
    protected $table = 'diseases';

    protected $casts = [
        'name' => 'string',
        'code' => 'string'
    ];

    public function getViewResource(){return ViewIcd::class;}
    public function getShowResource(){return ViewIcd::class;}
}
