<?php

namespace Beeralex\Reviews\Resources;

use Beeralex\Core\Http\Resources\Resource;
use Illuminate\Support\Collection;

class FilesForElements extends Resource
{
    public function toArray() : array
    {
        return (new Collection($this->elements))->groupBy('ID')
        ->map(fn ($element) => $element
            ->map(fn ($item) => FileInfo::make(compact('item'))->toArray()))
        ->toArray();
    }
}