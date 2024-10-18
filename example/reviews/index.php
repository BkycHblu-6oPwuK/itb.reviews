<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("reviews");
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
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>