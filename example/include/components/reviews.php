<?
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