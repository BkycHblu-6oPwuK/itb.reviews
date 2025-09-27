<?php

namespace Itb\Reviews\Resources;

use Itb\Core\Http\Resources\Resource;
use Illuminate\Support\Collection;

class Files extends Resource
{
    public function toArray() : array
    {
        return (new Collection($this->files))->map(fn ($item) => FileInfo::make(compact('item'))->toArray())->all();
    }
}