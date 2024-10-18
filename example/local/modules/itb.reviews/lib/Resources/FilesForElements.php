<?php

namespace Itb\Reviews\Resources;

use Itb\Core\Http\Resources\Resource;
use Tightenco\Collect\Support\Collection;

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