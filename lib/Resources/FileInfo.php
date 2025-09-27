<?php

namespace Itb\Reviews\Resources;

use Itb\Core\Http\Resources\Resource;

class FileInfo extends Resource
{
    public function toArray() : array
    {
        $is_video = str_starts_with($this->item['TYPE'], 'video');
        $result = [
            'id' => (int)$this->item['ID_FILE'],
            'type' => $is_video ? 'video' : 'image',
            'src' => $this->item['SRC_FILE'],
        ];
        if ($is_video) {
            $result['thumbail'] = $this->item['SRC_THUMBAIL'];
        }
        return $result;
    }
}