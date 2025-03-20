<?php

namespace Zahzah\ModuleIcd\Resources\ICD10;

use Zahzah\ModuleIcd\Resources\ICD\ViewICD;

class ViewICD10 extends ViewICD
{
    public function toArray(\Illuminate\Http\Request $request) : array{
      $arr = [
        
      ];
      $arr = array_merge(parent::toArray($request),$arr);
      
      return $arr;
  }
}
