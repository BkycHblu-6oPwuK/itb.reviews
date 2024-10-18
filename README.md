## Модуль отзывов
Отзывы к товарам и к сайту

## Установка

1. Установить пакеты из composer.json
2. Для пакета php-ffmpeg/php-ffmpeg требуется чтобы на сервере был установлен пакет - ffmpeg. Пакет нужен для создания превью для загруженного видео
3. Установить модуль itb.core - https://git.itb-dev.ru/ITB-dev/itb.core
4. Для подключения компонента отзывов в catalog.element реализуется через некешируемую область (модули api.core и api.uncachedarea), хотя вероятнее всего шаблоны вы будете переделывать, но стоит учитывать что если компонент имеет script.js который компонент подключает, то подключение js и возможно css компонента в компоненте не работает.

ссылка на api.core - https://github.com/ASDAFF/api.core

ссылка на api.uncachedarea - https://github.com/ASDAFF/api.uncachedarea

## Класс Itb\Reviews\Options

Этот класс используется для получения настроек модуля и компонента, при подключении компонента в шаблоне можно получить объект класса с помощью метода:

```php
Itb\Reviews\Options::getInstance(null);
```

Массив $arParams нужно передовать если вы хотите создать новый объект

## Параметры компонента

Через $arParams можно прокинуть следующие параметры в компонент:

- ```IBLOCK_ID``` - ИД инфоблока отзывов, по умолчанию будет взято значение из настроек модуля.
- ```PRODUCT_ID``` (тип int) - ид продукта для которого будут выбраны отзывы, то есть в catalog.element передаете id
- ```PAGINATION_LIMIT``` (тип int) - количество отзывов на странице
- ```SHOW_INFO_PRODUCT``` (тип bool) - дополнительная информация о товаре на общей странице отзывов (название товара и ссылка на него + картинка товара (PREVIEW_PICTURE либо DETAIL_PICTURE))

## Пример подключения компонента

1. Подключение на общей странице отзывов:
```php
$APPLICATION->IncludeComponent(
	"Itb:reviews", 
	"about", 
	[
		"PAGINATION_LIMIT" => "5",
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
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "86400"
	],
	false
);
```

## Vue компонент reviews-star

компонент reviews-star регистрируется глобально, то есть там где подключаются отзывы - вы можете использовать этот компонент в любом вашем vue приложении

На примере catalog.element на сайте без vue вы так же можете вывести рейтинг

1. В component_epilog компонента catalog.element добавьте следующую запись:
```php
if (Loader::includeModule('itb.reviews')) {

	$reviewsAvg = \Itb\Reviews\Helpers\EvalHelper::getAvg($arResult['ID']);
	?>
	<script>
		window.reviewsAvg = '<?= $reviewsAvg ?>';
	</script>
<?
}
```

2. 
в template.php в месте где вы хотите вывести рейтинг добавьте следующий код:

```php
<div id="app-review">
	<reviews-star :avg="window.reviewsAvg"></reviews-star>
</div>
<script>
	document.addEventListener('DOMContentLoaded', () => {
		new Vue({
			el: '#app-review'
		})
	})
</script>
```

## SignedParameters компонента

Компонент преобразует следующие параметры:
- ```PAGINATION_LIMIT```
- ```PRODUCT_ID```
- ```IBLOCK_ID```
- ```SHOW_INFO_PRODUCT```

Чтобы корректно использовать ajax запросы в шаблоне компонента, передавайте эти параметры в теле запроса под ключом ```params```

```javascript
let formData = new URLSearchParams();
formData.append('params', JSON.stringify(this.params));
```

получить SignedParameters в шаблоне компонента: 

```php
$this->getComponent()->getSignedParameters()
```

## Контроллер для ajax запросов

В контроллере есть 4 маршрута для выполнения ajax запросов:

- ```add``` - Для добавления отзыва
- ```pagination``` - для получения отзывов по переданному массиву ```pagination``` (currentPage нужно изменять в js перед отправкой ajax запроса), так же передается массив ```sorting```
- ```sorting``` - для сортировки отзывов по переданному массиву ```sorting``` (field и type сортировки изменять в js перед отправкой ajax запроса), так же нужно передать массив ```pagination```
- ```get``` - для получения отзывов, можно передать ```product_id``` для получения отзывов только для этого товара

Так же для всех запросов нужно передавать SignedParameters под ключом ```params```

Все ссылки для выполнения запросов доступны в массиве ```actions```

в script.js шаблонов можно найти примеры выполнения ajax запросов