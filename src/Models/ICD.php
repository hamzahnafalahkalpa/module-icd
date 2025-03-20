<?php

namespace Zahzah\ModuleIcd\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Zahzah\LaravelHasProps\Concerns\HasProps;
use Zahzah\LaravelSupport\Models\BaseModel;
use Zahzah\ModuleIcd\Resources\ICD\ViewICD;

class ICD extends BaseModel {
    use HasProps, SoftDeletes;

    protected $table    = 'icds'; 
    protected $fillable = [
        'id', 'parent_id', 'name' , 'local_name', 'code', 'version', 'props'
    ];

    protected $casts = [
        'name' => 'string',
        'code' => 'string'
    ];

    public function toViewApi(){
        return new ViewICD($this);
    }
}
