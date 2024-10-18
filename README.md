## Модуль отзывов
Отзывы к товарам и к сайту

## Установка

1. Установить пакеты из composer.json
2. Для пакета php-ffmpeg/php-ffmpeg требуется чтобы на сервере был установлен пакет - ffmpeg
3. Установить модуль itb.core - https://git.itb-dev.ru/ITB-dev/itb.core
4. Для подключения компонента отзывов в catalog.element реализуется через некешируемую область (модули api.core и api.uncachedarea), хотя вероятнее всего шаблоны вы будете переделывать, но стоит учитывать что если компонент имеет script.js который компонент подключает, то подключение jsи возможно css компонента в компоненте не работает!!

## Класс Itb\Reviews\Options

Этот класс используется для хранения настроек модуля и компонента, при подключении компонента в шаблоне можно получить объект класса с помощью метода:

```php
Itb\Reviews\Options::getInstance(null);
```

Массив $arParams нужно передовать если вы хотите создать новый объект

## Параметры компонента

Через $arParams можно прокинуть следующие параметры:

- 'IBLOCK_ID' - ИД инфоблока отзывов, по умолчанию будет взято значение из настроек модуля, но лучше явно прокидывать с помощью
```php
\Bitrix\Main\Config\Option::get("itb.reviews","reviews_iblock_id")
```
- 'PRODUCT_ID' (тип int) - ид продукта для которого будут выбраны отзывы, то есть в catalog.element передаете id
- 'PAGINATION_LIMIT' (тип int) - количество отзывов на странице
- 'SHOW_INFO_PRODUCT' (тип bool) - дополнительная информация о товаре на общей странице отзывов

## Пример подключения компонента

1. Подключение на общей странице отзывов:
```php
$APPLICATION->IncludeComponent(
	"Itb:reviews", 
	"about", 
	[
		"PAGINATION_LIMIT" => "5",
		"IBLOCK_ID" => (int)\Bitrix\Main\Config\Option::get("itb.reviews","reviews_iblock_id"),
		"SHOW_INFO_PRODUCT" => true,
		"COMPONENT_TEMPLATE" => "about",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "86400"
	],
	false
);
```

2. Подключение на странице товара
```php
if (Loader::includeModule('api.uncachedarea') && Loader::includeModule('itb.reviews')) {
	CAPIUncachedArea::includeFile(
		"/include/components/reviews.php",
		["PRODUCT_ID" => $arResult['ID']]
	);
}
```
файл ```/include/components/reviews.php```:
```php
$APPLICATION->IncludeComponent(
	"Itb:reviews", 
	".default", 
	[
		"PRODUCT_ID" => $arParams["PRODUCT_ID"],
		"PAGINATION_LIMIT" => "3",
		"IBLOCK_ID" => (int)\Bitrix\Main\Config\Option::get("itb.reviews","reviews_iblock_id"),
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "86400"
	],
	false
);
```

