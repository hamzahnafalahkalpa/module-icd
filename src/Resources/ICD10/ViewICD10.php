<?php

namespace Hanafalah\ModuleIcd\Resources\ICD10;

use Hanafalah\ModuleIcd\Resources\ICD\ViewICD;

class ViewICD10 extends ViewICD
{
  public function toArray(\Illuminate\Http\Request $request): array
  {
    $arr = [];
    $arr = array_merge(parent::toArray($request), $arr);

    return $arr;
  }
}
