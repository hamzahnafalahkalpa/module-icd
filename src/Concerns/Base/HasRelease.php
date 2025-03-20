<?php

namespace Zahzah\ModuleIcd\Concerns\Base;

trait HasRelease {
    public function getRelease10(
        ? string $release_id = null,
        ? string $code = null
    ): object{
        $url = '/10';
        if (isset($release_id)) {
            $url .= '/'.$release_id;
            if (isset($code)) $url .= '/'.$code;
        }else{
            if (isset($code)) $url .= '/'.$code;
        }
        return $this->getRelease($url);
    }

    public function getRelease(string $end_point = null): object{
        return $this->getFrom('release',$end_point);
    }
}