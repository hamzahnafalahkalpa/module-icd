<?php

namespace Hanafalah\ModuleIcd\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\ModuleIcd\Resources\ICD\ViewICD;

class ICD extends BaseModel
{
    use HasProps, SoftDeletes;

    protected $table    = 'icds';
    protected $fillable = [
        'id',
        'parent_id',
        'name',
        'local_name',
        'code',
        'version',
        'props'
    ];

    protected $casts = [
        'name' => 'string',
        'code' => 'string'
    ];

    public function toViewApi()
    {
        return new ViewICD($this);
    }
}
