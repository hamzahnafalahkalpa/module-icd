<?php

namespace Hanafalah\ModuleIcd\Concerns\Base;

trait HasEntity
{
    public function getEntityAutocode(
        string $search_text,
        ?string $release_id = null,
        ?string $subtrees_filter = null,
        ?float $match_threshold = null
    ): object {
        $this->setQueries(get_defined_vars());
        return $this->getEntity('/autocode');
    }

    public function getEntityBySearch(
        string $q,
        ?string $subtrees_filter = null,
        ?string $chapter_filter = null,
        bool $use_flexisearch = false,
        bool $flat_results = true,
        ?string $properties_to_be_searched = null,
        ?string $release_id = null,
        bool $highlighting_enabled = true
    ): object {
        $this->setQueries(get_defined_vars());
        return $this->getEntity('/search');
    }

    private function scrapEntityId(string $id): string
    {
        return array_slice(explode('/', $id), -1)[0];
    }

    public function getEntityById(string $id): object
    {
        return $this->getEntity('/' . $this->scrapEntityId($id));
    }

    public function getEntity(?string $end_point = null): object
    {
        return $this->getFrom('entity', $end_point);
    }
}
