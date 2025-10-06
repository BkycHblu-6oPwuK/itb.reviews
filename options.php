<?
use Bitrix\Iblock\IblockTable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Beeralex\Core\Config\Fields\Input;
use Beeralex\Core\Config\Fields\Select;
use Beeralex\Core\Config\Fields\TextArea;
use Beeralex\Core\Config\Tab;
use Beeralex\Core\Config\TabsBuilder;

$request = HttpApplication::getInstance()->getContext()->getRequest();
$module_id = htmlspecialcharsbx($request["mid"] != "" ? $request["mid"] : $request["id"]);
$POST_RIGHT = $APPLICATION->GetGroupRight($module_id);

if ($POST_RIGHT < "S") {
    $APPLICATION->AuthForm('Недостаточные права доступа');
}

Loader::includeModule($module_id);
Loader::includeModule('iblock');
$iblocks = IblockTable::query()->where('ACTIVE','Y')->setSelect(['ID','NAME','CODE'])->exec()->fetchAll();
$optionsIblock = [];
$selectedIblock = "";

foreach($iblocks as $iblock){
    if($iblock['CODE'] == 'product_reviews'){
        $selectedIblock = $iblock['ID'];
    }
    $optionsIblock[$iblock['ID']] = $iblock['NAME'];
}

$mainTab = new Tab('edit1', 'Общие настройки', 'Отзывы');
$mainTab->addField((new Select('reviews_iblock_id', 'Инфоблок', $optionsIblock))->setDefaultValue($selectedIblock)->setLabel('Общие'));
$mainTab->addField((new Input('catalog_iblock_id', 'ID инфоблока каталога'))->setDefaultValue(0));
$mainTab->addField((new Input('offers_iblock_id', 'ID инфоблока предложений'))->setDefaultValue(0));
$twoGisTab = new Tab('edit2', 'Импорт из 2гис', 'Импорт из 2гис');
$twoGisTab->addField((new Input('two_gis_key', 'Api ключ 2гис')));
$twoGisTab->addField((new TextArea('two_gis_branches', 'ID филиалов (вводить каждый филиал с новой строки)')));
$accessTab = new Tab("edit3", Loc::getMessage("MAIN_TAB_RIGHTS"), Loc::getMessage("MAIN_TAB_TITLE_RIGHTS"));
$tabsBuilder = (new TabsBuilder())->addTab($mainTab)->addTab($twoGisTab)->addTab($accessTab);

$tabs = $tabsBuilder->getTabs();

if ($request->isPost() && check_bitrix_sessid()) {
    foreach ($tabs as $tab) {
        $fileds = $tab->getFields();
        if (!isset($fileds)) {
            continue;
        }
        foreach ($fileds as $filed) {
            if($name = $filed->getName()){
                if ($request["apply"]) {
                    $optionValue = $request->getPost($name);
                    $optionValue = is_array($optionValue) ? implode(",", $optionValue) : $optionValue;
                    Option::set($module_id, $name, $optionValue);
                }
                if ($request["default"]) {
                    Option::set($module_id, $name, $filed->getDefaultValue());
                }
            }
        }
    }
}
// отрисовываем форму, для этого создаем новый экземпляр класса CAdminTabControl, куда и передаём массив с настройками
$tabControl = new CAdminTabControl(
    "tabControl",
    $tabsBuilder->getTabsFormattedArray()
);

// отображаем заголовки закладок
$tabControl->Begin();
?>

<form action="<?= $APPLICATION->GetCurPage() ?>?mid=<?= $module_id ?>&lang=<?= LANG ?>" method="post">
    <? foreach ($tabs as $tab) {
        if ($options = $tab->getOptionsFormattedArray()) {
            $tabControl->BeginNextTab();
            __AdmSettingsDrawList($module_id, $options);
        }
    }
    $tabControl->BeginNextTab();

    require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/admin/group_rights.php";

    $tabControl->Buttons();
    echo (bitrix_sessid_post());
    ?>
    <input class="adm-btn-save" type="submit" name="apply" value="Применить" />
    <input type="submit" name="default" value="По умолчанию" />
</form>
<?
// обозначаем конец отрисовки формы
$tabControl->End();
