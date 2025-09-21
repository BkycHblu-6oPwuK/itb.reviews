<?php

use Bitrix\Main\Loader;
use Itb\Reviews\Options;
use Itb\Reviews\Services\ReviewsService;
use \Bitrix\Main\Application;
use Itb\Reviews\ComponentParams;

class Reviews extends \CBitrixComponent
{
    public function executeComponent()
    {
        if(Loader::includeModule('itb.reviews')){
            global $USER;
            $componentParams = new ComponentParams($this->arParams);
            $service = new ReviewsService($componentParams);
            $auth = $USER->IsAuthorized();
            $taggedCache = Application::getInstance()->getTaggedCache();
            $cache_path = 'itb/reviews';
            if ($this->startResultCache(false, [$auth], $cache_path)) {
                $taggedCache->startTagCache($cache_path);
                $this->arResult = $service->getReviews();
                $this->includeComponentTemplate();
                $taggedCache->registerTag('iblock_id_' . Options::getInstance()->reviewsIblockId);
                $taggedCache->endTagCache();
            }
        }
    }

    protected function listKeysSignedParameters()
    {				
        return [
            'PAGINATION_LIMIT',
            'PRODUCT_ID',
            'IBLOCK_ID',
            'SHOW_INFO_PRODUCT',
            'PLATFORM'
        ];
    }
}
