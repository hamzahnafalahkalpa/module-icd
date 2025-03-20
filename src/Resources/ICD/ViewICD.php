<?php

namespace Zahzah\ModuleIcd\Resources\ICD;

use Zahzah\LaravelSupport\Resources\ApiResource;

class ViewICD extends ApiResource
{
    public function toArray(\Illuminate\Http\Request $request) : array{
      $arr = [
        'id'      => $this->id,
        'name'    => $this->name,
        'code'    => $this->code,
        'version' => $this->version,
        'childs'  => $this->relationValidation('childs',function(){
          return $this->childs->transform(function($child){
            return $child->toViewApi();
          });
        })
      ];
      
      return $arr;
  }
}
