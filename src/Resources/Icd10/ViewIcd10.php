<?php

namespace Hanafalah\ModuleIcd\Resources\Icd10;

use Hanafalah\ModuleIcd\Resources\Icd\ViewIcd;

class ViewIcd10 extends ViewIcd
{
  public function toArray(\Illuminate\Http\Request $request): array
  {
    $arr = [];
    $arr = $this->mergeArray(parent::toArray($request), $arr);
    return $arr;
  }
}
