<?php

namespace Hanafalah\ModuleIcd\Concerns\Base;

trait HasEndPoint
{
    use HasEntity, HasRelease;

    protected string $__base_url   = "https://id.who.int/icd/";
    protected string $__url;
    protected array $__end_points = [
        'entity' => 'entity',
        'release' => 'release'
    ];

    protected function getUrl(string $end_point, string $url = ''): string
    {
        $end_point = $this->__end_points[$end_point] ?? $end_point;
        $url = str_replace($this->__base_url, '', $url);
        return $this->__url = $this->__base_url . $url . $end_point;
    }

    protected function getFrom(string $main_point, string $end_point = null): object
    {
        $this->getUrl($main_point);
        if (isset($end_point)) $this->getUrl($end_point, $this->__url);
        return $this->makeRequest();
    }
}
