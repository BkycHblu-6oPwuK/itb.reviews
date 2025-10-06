<?php
namespace Beeralex\Reviews\Enum;

enum Platforms : string
{
    case SITE = 'site';
    case TWO_GIS = '2gis';

    public static function get(string $code) : static
    {
        return match($code){
            static::SITE->value => static::SITE,
            static::TWO_GIS->value => static::TWO_GIS,
            default => static::SITE
        };
    }
}