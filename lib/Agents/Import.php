<?php

namespace Beeralex\Reviews\Agents;

use Bitrix\Main\DI\ServiceLocator;
use Beeralex\Reviews\Import\ImportFrom2Gis;

class Import
{
    /**
     * @var \Beeralex\Reviews\Import\BaseImport[]|string[] $importPlatformsMap
     */
    protected static $importPlatformsMap = [
        ImportFrom2Gis::class
    ];
    protected static function import() 
    {
        foreach(static::$importPlatformsMap as $class){
            /** @var \Beeralex\Reviews\Import\BaseImport */
            $object = ServiceLocator::getInstance()->get($class);
            $object->process();
        }
    }
    public static function exec()
    {
        static::import();
        return '\\' . __METHOD__ . '();';
    }
}
