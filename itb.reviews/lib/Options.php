<?php

namespace Itb\Reviews;

use Itb\Reviews\Enum\Platforms;
use Itb\Reviews\Exceptions\CatalogIblockIdIsEmpty;

final class Options
{
    const MODULE_ID = 'itb.reviews';

    private $iblockId;
    private $catalogIblockId;
    private $offerIblockId;
    private $product_id;
    private $pagination_limit;
    private $showInfoByProduct = false;

    private $pagination_current = 1;
    private $pagination_page_count = 0;
    private $sorting_field = "ID";
    private $sorting_type = "DESC";
    private $platform;

    private $limitFiles = 20;
    private $showFilesByProduct = false;
    private static $instance = null;

    private function __construct(array $arParams)
    {
        $moduleOptions = \Bitrix\Main\Config\Option::getForModule(self::MODULE_ID);
        $this->iblockId = $arParams['IBLOCK_ID'] ? (int)$arParams['IBLOCK_ID'] : (int)$moduleOptions['reviews_iblock_id'];
        $this->product_id = $arParams['PRODUCT_ID'] ? (int)$arParams['PRODUCT_ID'] : 0;
        $this->pagination_limit = $arParams['PAGINATION_LIMIT'] ? (int)$arParams['PAGINATION_LIMIT'] : 5;
        $this->showInfoByProduct = $arParams['SHOW_INFO_PRODUCT'] ? (bool)$arParams['SHOW_INFO_PRODUCT'] : false;
        $this->platform = $arParams['PLATFORM'] ? Platforms::get($arParams['PLATFORM'] ?? '')->value : '';

        if($this->platform !== Platforms::SITE->value){
            $this->sorting_field = 'EXTERNAL_ID.VALUE';
        }

        if (empty($moduleOptions['catalog_iblock_id'])) throw new CatalogIblockIdIsEmpty("Должна быть заполнена настройка модуля - ID инфоблока каталога");

        $this->catalogIblockId = $moduleOptions['catalog_iblock_id'] ? (int)$moduleOptions['catalog_iblock_id'] : 0;
        $this->offerIblockId = $moduleOptions['offers_iblock_id'] ? (int)$moduleOptions['offers_iblock_id'] : 0;
    }

    public function getIblockId()
    {
        return $this->iblockId;
    }

    /**
     * @throws CatalogIblockIdIsEmpty
     */
    public static function createInstance(array $arParams): self
    {
        self::$instance = new self($arParams);
        return self::$instance;
    }

    public static function getInstance() : self
    {
        if(!self::$instance){
            return self::createInstance([]);
        }
        return self::$instance;
    }

    public function getProductId()
    {
        return $this->product_id;
    }

    public function getPagination()
    {
        return [
            'current' => $this->pagination_current,
            'limit' => $this->pagination_limit,
            'pageCount' => $this->pagination_page_count
        ];
    }

    public function getPaginationCurrent()
    {
        return $this->pagination_current;
    }

    public function getPaginationLimit()
    {
        return $this->pagination_limit;
    }

    public function getPaginationPageCount()
    {
        return $this->pagination_page_count;
    }

    public function getShowInfoProduct()
    {
        return $this->showInfoByProduct;
    }

    public function getSorting()
    {
        return [$this->sorting_field => $this->sorting_type];
    }

    public function getSortingField()
    {
        return $this->sorting_field;
    }

    public function getSortingType()
    {
        return $this->sorting_type;
    }

    public function getLimitFiles()
    {
        return $this->limitFiles;
    }

    public function getShowFilesByProduct()
    {
        return $this->showFilesByProduct;
    }

    public function setProductId(int $id)
    {
        $this->product_id = $id;
        return $this;
    }

    public function setSorting(string $sorting_field, string $sorting_type)
    {
        $this->sorting_field = $sorting_field;
        $this->sorting_type = $sorting_type;
        return $this;
    }

    public function setPaginationLimit(int $pagination_limit)
    {
        $this->pagination_limit = $pagination_limit;
        return $this;
    }

    public function setPaginationCurrent(int $pagination_current)
    {
        $this->pagination_current = $pagination_current;
        return $this;
    }

    public function setPaginationPageCount(float $pagination_page_count)
    {
        $this->pagination_page_count = $pagination_page_count;
        return $this;
    }
    public function getCatalogIblockId()
    {
        return $this->catalogIblockId;
    }
    public function getOffersIblockId()
    {
        return $this->offerIblockId;
    }
    public function getPlatform()
    {
        return $this->platform;
    }

    public function getTwoGisKey(): string
    {
        return \Bitrix\Main\Config\Option::get(self::MODULE_ID, 'two_gis_key');
    }

    public function getTwoGisBranches(): array
    {
        $branches = preg_split("/\r\n|\n|\r/", \Bitrix\Main\Config\Option::get(self::MODULE_ID, 'two_gis_branches')) ?: [];
        $branches = array_unique(array_filter(array_map('trim', $branches)));
        return $branches;
    }
}
