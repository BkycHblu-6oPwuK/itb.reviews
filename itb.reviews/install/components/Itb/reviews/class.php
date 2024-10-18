<?php

use Bitrix\Main\Loader;
use Itb\Reviews\Options;
use Itb\Reviews\Services\ReviewsService;
use \Bitrix\Main\Application;

class Reviews extends \CBitrixComponent
{
    public function executeComponent()
    {
        if(Loader::includeModule('itb.reviews')){
            global $USER;
            $options = Options::createInstance($this->arParams);
            $service = new ReviewsService($options);
            $auth = $USER->IsAuthorized();
            $taggedCache = Application::getInstance()->getTaggedCache();
            $cache_path = 'itb/reviews';
            if ($this->startResultCache(false, [$auth], $cache_path)) {
                $taggedCache->startTagCache($cache_path);
                $this->arResult = $service->getReviews();
                $this->includeComponentTemplate();
                $taggedCache->registerTag('iblock_id_' . $options->getIblockId());
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
            'SHOW_INFO_PRODUCT'
        ];
    }
}
