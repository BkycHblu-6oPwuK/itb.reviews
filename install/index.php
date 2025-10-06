<?php

use Bitrix\Iblock\IblockSiteTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Iblock\TypeTable;
use Bitrix\Iblock\IblockTable;
use Bitrix\Main\Context;
use Beeralex\Reviews\Enum\Platforms;

Loc::loadMessages(__FILE__);

class beeralex_reviews extends CModule
{

    public function __construct()
    {
        if (is_file(__DIR__ . '/version.php')) {
            include_once(__DIR__ . '/version.php');
            $this->MODULE_ID           = 'beeralex.reviews';
            $this->MODULE_VERSION      = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
            $this->MODULE_NAME         = Loc::getMessage('REVIEWS_NAME');
            $this->MODULE_DESCRIPTION  = Loc::getMessage('REVIEWS_DESCRIPTION');
            $this->PARTNER_NAME = 'Beeralex';
            $this->PARTNER_URI = '#';
        } else {
            CAdminMessage::showMessage(
                Loc::getMessage('REVIEWS_FILE_NOT_FOUND') . ' version.php'
            );
        }
    }

    public function doInstall()
    {
        if (CheckVersion(ModuleManager::getVersion('main'), '23.00.00')) {
            $context = Application::getInstance()->getContext();
            $request = $context->getRequest();
            $step = $request->get('step');
            global $APPLICATION;
            if ($step < 2) {
                $APPLICATION->IncludeAdminFile(
                    Loc::getMessage('INSTALL_TITLE_STEP_1'),
                    __DIR__ . '/instalInfo-step1.php'
                );
            }
            if ($step == 2) {
                $this->installFiles();
                ModuleManager::registerModule($this->MODULE_ID);
                $catalog = $request->get('catalog');
                if (!empty($catalog)) {
                    $this->createIblock($catalog, $request->get('offer') ?? 0);
                }
                $APPLICATION->IncludeAdminFile(
                    Loc::getMessage('REVIEWS_INSTALL_TITLE') . ' «' . Loc::getMessage('REVIEWS_NAME') . '»',
                    __DIR__ . '/instalInfo-step2.php'
                );
            }
        } else {
            CAdminMessage::showMessage(
                Loc::getMessage('REVIEWS_INSTALL_ERROR')
            );
            return;
        }
        return true;
    }

    public function installAgents(): void
    {
        CAgent::Add([
           "NAME" => '\Beeralex\Reviews\Agents\Import::exec();',
           "MODULE_ID" => $this->MODULE_ID,
           "IS_PERIOD" => "N",
           "AGENT_INTERVAL" => 86400 * 30,
           "ACTIVE" => "N",
        ]);
    }

    public function installFiles()
    {
        CopyDirFiles(
            __DIR__ . "/components",
            Application::getDocumentRoot() . "/bitrix/components",
            true,
            true
        );
        CopyDirFiles(
            __DIR__ . "/js",
            Application::getDocumentRoot() . "/bitrix/js",
            true,
            true
        );
        CopyDirFiles(
            __DIR__ . "/images",
            Application::getDocumentRoot() . "/images/reviews",
            true,
            true
        );
    }

    public function createIblock($catalog = 0, $offer = 0)
    {
        Loader::includeModule('iblock');
        $iblockCode = 'product_reviews';
        $iblockCodeApi = 'productReviewsApi';
        $iblockId = $this->getIblockId($iblockCode);
        if (!$iblockId) {
            $iblock_type = TypeTable::query()->setSelect(['ID'])->fetch()['ID'];
            $siteId = Context::getCurrent()->getSite() ?: 's1';
            $iblockData = [
                'NAME' => 'Отзывы к товарам',
                'CODE' => $iblockCode,
                'API_CODE' => $iblockCodeApi,
                'ACTIVE' => 'Y',
                'IBLOCK_TYPE_ID' => $iblock_type,
                'LID' => $siteId,
                'WORKFLOW' => 'N',
                'BIZPROC' => 'N',
            ];
            $iblockId = IblockTable::add($iblockData)->getId();
            IblockSiteTable::add(['IBLOCK_ID' => $iblockId, 'SITE_ID' => $siteId]);
            $propertyDataMap = [
                [
                    'IBLOCK_ID' => $iblockId,
                    'NAME' => 'Товар',
                    'CODE' => 'PRODUCT',
                    'PROPERTY_TYPE' => 'E',
                    'MULTIPLE' => 'N',
                    'LINK_IBLOCK_ID' => (int)$catalog,
                ],
                [
                    'IBLOCK_ID' => $iblockId,
                    'NAME' => 'Предложение',
                    'CODE' => 'OFFER',
                    'PROPERTY_TYPE' => 'E',
                    'MULTIPLE' => 'N',
                    'LINK_IBLOCK_ID' => (int)$offer,
                ],
                [
                    'IBLOCK_ID' => $iblockId,
                    'NAME' => 'Пользователь',
                    'CODE' => 'USER',
                    'PROPERTY_TYPE' => 'S',
                    'MULTIPLE' => 'N',
                ],
                [
                    'IBLOCK_ID' => $iblockId,
                    'NAME' => 'Оценка',
                    'CODE' => 'EVAL',
                    'PROPERTY_TYPE' => 'S',
                    'MULTIPLE' => 'N',
                ],
                [
                    'IBLOCK_ID' => $iblockId,
                    'NAME' => 'Имя пользователя',
                    'CODE' => 'USER_NAME',
                    'PROPERTY_TYPE' => 'S',
                    'MULTIPLE' => 'N',
                ],
                [
                    'IBLOCK_ID' => $iblockId,
                    'NAME' => 'Файлы',
                    'CODE' => 'FILES',
                    'PROPERTY_TYPE' => 'F',
                    'MULTIPLE' => 'Y',
                    'FILE_TYPE' => 'mpg, avi, wmv, mpeg, mpe, flv, jpg, gif, bmp, png, jpeg, webp, mp4',
                ],
                [
                    'IBLOCK_ID' => $iblockId,
                    'NAME' => 'Отзыв',
                    'CODE' => 'REVIEW',
                    'PROPERTY_TYPE' => 'S',
                    'MULTIPLE' => 'N',
                    'USER_TYPE' => 'HTML',
                    'DEFAULT_VALUE' => serialize([
                        'TEXT' => '',
                        'TYPE' => 'HTML',
                    ]),
                    'USER_TYPE_SETTINGS' => serialize(['height' => '200'])
                ],
                [
                    'IBLOCK_ID' => $iblockId,
                    'NAME' => 'Ответ на отзыв',
                    'CODE' => 'STORE_RESPONSE',
                    'PROPERTY_TYPE' => 'S',
                    'MULTIPLE' => 'N',
                    'USER_TYPE' => 'HTML',
                    'DEFAULT_VALUE' => serialize([
                        'TEXT' => '',
                        'TYPE' => 'HTML',
                    ]),
                    'USER_TYPE_SETTINGS' => serialize(['height' => '200'])
                ],
                [
                    'IBLOCK_ID' => $iblockId,
                    'NAME' => 'Контактные данные',
                    'CODE' => 'CONTACT_DETAILS',
                    'PROPERTY_TYPE' => 'S',
                    'MULTIPLE' => 'N',
                ],
                [
                    'IBLOCK_ID' => $iblockId,
                    'NAME' => 'Внешний id',
                    'CODE' => 'EXTERNAL_ID',
                    'PROPERTY_TYPE' => 'N',
                    'MULTIPLE' => 'N',
                ],
            ];
            PropertyTable::addMulti($propertyDataMap);
            $propertyId = (new CIBlockProperty)->Add([
                'IBLOCK_ID' => $iblockId,
                'NAME' => 'Платформа отзыва',
                'CODE' => 'REVIEW_PLATFORM',
                'PROPERTY_TYPE' => 'L',
                'MULTIPLE' => 'N',
            ]);
            if ($propertyId) {
                CIBlockPropertyEnum::Add([
                    'PROPERTY_ID' => $propertyId,
                    'XML_ID' => Platforms::SITE->value,
                    'VALUE' => 'Отзыв с сайта',
                ]);

                CIBlockPropertyEnum::Add([
                    'PROPERTY_ID' => $propertyId,
                    'XML_ID' => Platforms::TWO_GIS->value,
                    'VALUE' => 'Отзыв c 2gis',
                ]);
            }
        }
        Option::set($this->MODULE_ID, 'reviews_iblock_id', $iblockId);
        Option::set($this->MODULE_ID, 'catalog_iblock_id', $catalog);
        Option::set($this->MODULE_ID, 'offers_iblock_id', $offer);
    }

    public function getIblockId(string $code)
    {
        $iblock = IblockTable::query()->setSelect(['ID'])->where('CODE', $code)->exec()->fetch();
        return $iblock['ID'] ?? 0;
    }

    public function doUninstall()
    {

        global $APPLICATION;

        $this->uninstallFiles();
        CAgent::RemoveModuleAgents($this->MODULE_ID);
        Option::delete($this->MODULE_ID);
        ModuleManager::unRegisterModule($this->MODULE_ID);

        $APPLICATION->includeAdminFile(
            Loc::getMessage('REVIEWS_UNINSTALL_TITLE') . ' «' . Loc::getMessage('REVIEWS_NAME') . '»',
            __DIR__ . '/unstep.php'
        );
    }

    public function uninstallFiles()
    {
        DeleteDirFilesEx("/bitrix/components/Beeralex/reviews");
        DeleteDirFilesEx("/bitrix/js/beeralex/reviews");
        DeleteDirFilesEx("/images/reviews");
    }
}
