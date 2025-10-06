<?php

namespace Beeralex\Reviews;

use Beeralex\Core\Config\AbstractOptions;
use Beeralex\Reviews\Exceptions\CatalogIblockIdIsEmpty;

final class Options extends AbstractOptions
{
    public readonly int $reviewsIblockId;
    public readonly int $catalogIblockId;
    public readonly int $offersIblockId;
    public readonly string $twoGisKey;
    /** @var string[] */
    public readonly array $twoGisBranches;

    protected function mapOptions(array $options): void
    {
        $this->reviewsIblockId = (int)($options['reviews_iblock_id'] ?? 0);
        $this->catalogIblockId = (int)($options['catalog_iblock_id'] ?? 0);
        $this->offersIblockId  = (int)($options['offers_iblock_id'] ?? 0);

        $this->twoGisKey = (string)($options['two_gis_key'] ?? '');

        $branches = preg_split("/\r\n|\n|\r/", $options['two_gis_branches'] ?? '') ?: [];
        $this->twoGisBranches = array_unique(array_filter(array_map('trim', $branches)));
    }

    protected function validateOptions(): void
    {
        if ($this->catalogIblockId <= 0) {
            throw new CatalogIblockIdIsEmpty(
                "Должна быть заполнена настройка модуля - ID инфоблока каталога"
            );
        }
    }

    public function getModuleId(): string
    {
        return 'beeralex.reviews';
    }
}
