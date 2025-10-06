<?php

namespace Beeralex\Reviews\Resources;

use Beeralex\Core\Http\Resources\Resource;

class ProductInfo extends Resource
{
    public function toArray() : array
    {
        $result = [];
        foreach($this->result as $item){
            $id_image = $item['PREVIEW_PICTURE'] ?? $item['DETAIL_PICTURE'];
            $result[$item['ID']] = [
                // 'id' => $item['ID'],
                // 'code' => $item['code_value'],
                // 'vid_tovara' => $item['vid_tovara_value'],
                // 'brand' => $item['brand'],
                'url' => null,
                'text' =>  $item['NAME'],
                'preview' => $id_image ? \CFile::GetPath($id_image) : null
            ];
            if($item['ACTIVE'] == 'Y'){
                $result[$item['ID']]['url'] = \CIBlock::ReplaceDetailUrl($item['DETAIL_PAGE_URL'],  $item ,  false ,  'E' );
            }
        }
        return $result;
    }
}
