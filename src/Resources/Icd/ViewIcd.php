<?php

namespace Hanafalah\ModuleIcd\Resources\Icd;

use Hanafalah\ModuleDisease\Resources\Disease\ViewDisease;

class ViewIcd extends ViewDisease
{
  public function toArray(\Illuminate\Http\Request $request): array
  {
    $arr = [
      'childs'  => $this->relationValidation('childs', function () {
        return $this->childs->transform(function ($child) {
          return $child->toViewApi()->resolve();
        });
      })
    ];

    $arr = $this->mergeArray(parent::toArray($request), $arr);
    return $arr;
  }
}
