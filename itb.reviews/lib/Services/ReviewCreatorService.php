<?php

namespace Itb\Reviews\Services;

use Bitrix\Main\DI\ServiceLocator;
use Itb\Core\Helpers\IblockHelper;
use Itb\Reviews\Contracts\CreatorContract;
use Itb\Reviews\Contracts\FileUploaderContract;
use Itb\Reviews\Enum\Platforms;
use Itb\Reviews\Options;

class ReviewCreatorService implements CreatorContract
{
    protected Options $options;

    public function __construct(Options $options)
    {
        $this->options = $options;
        \Bitrix\Main\Loader::includeModule('iblock');
    }

    public function create(array $form, array $files): int
    {
        global $USER;

        $form['eval'] = $this->sanitizeEval($form['eval'] ?? 1);
        $uploadResult = $this->handleFiles($files);

        $properties = $this->buildProperties($form, $uploadResult, $USER);
        $name = $this->buildName($form, $properties);

        $elementData = [
            'IBLOCK_ID' => $this->options->getIblockId(),
            'NAME' => $name,
            'IBLOCK_SECTION_ID' => false,
            'ACTIVE' => $form['active'] ? 'Y' : 'N',
            'PROPERTY_VALUES' => $properties,
        ];

        if (!empty($uploadResult['preview']['file_array'])) {
            $elementData['PREVIEW_PICTURE'] = $uploadResult['preview']['file_array'];
        }

        $id = (new \CIBlockElement)->Add($elementData);

        if (!empty($uploadResult['preview']['thumbnail_path'])) {
            @unlink($uploadResult['preview']['thumbnail_path']);
        }

        return $id;
    }

    protected function sanitizeEval(int $eval): int
    {
        return max(1, min(5, $eval));
    }

    protected function handleFiles(array $files): array
    {
        $result = ['ids' => [], 'preview' => []];
        if (!empty($files)) {
            /** @var FileUploaderContract  */
            $uploader = ServiceLocator::getInstance()->get(FileUploaderContract::class);
            $result = $uploader->upload($files);
        }
        return $result;
    }

    protected function buildProperties(array $form, array $uploadResult, $USER): array
    {
        $platform = Platforms::get($form['platform'] ?? '');
        $isAuth = $USER?->IsAuthorized() ?? false;
        $isSitePlatform = ($platform === Platforms::SITE);
        $userId = $isAuth ? $USER->GetID() : null;
        $properties = [
            'PRODUCT' => $this->options->getProductId(),
            'EVAL' => $form['eval'],
            'REVIEW' => $form['review'],
            'FILES' => $uploadResult['ids'],
            'OFFER' => $form['offer'],
            'CONTACT_DETAILS' => $form['contact'],
            'STORE_RESPONSE' => $form['answer'] ?? '',
            'REVIEW_PLATFORM' => $this->getPlatformId($platform),
            'EXTERNAL_ID' => $form['external_id'] ?? '',
        ];

        if (!empty($form['user_name'])) {
            $properties['USER_NAME'] = $form['user_name'];
        } elseif ($isSitePlatform && $isAuth) {
            $properties['USER_NAME'] = $this->formatUserName($USER);
        } else {
            $properties['USER_NAME'] = 'Аноним';
        }

        if ($isSitePlatform && $isAuth) {
            $properties['USER'] = $userId;
        }

        return $properties;
    }

    protected function formatUserName($USER): string
    {
        $lastName = trim($USER?->GetLastName() ?? '');
        $initial = $lastName ? mb_strtoupper(mb_substr($lastName, 0, 1)) . '.' : '';
        return trim($USER?->GetFirstName() ?? '' . ' ' . $initial);
    }

    protected function buildName(array $form, array $properties): string
    {
        if (($form['platform'] ?? '') === Platforms::TWO_GIS->value) {
            return 'Отзыв c 2гис - ' . ($form['user_name'] ?? 'Неизвестный');
        }

        $productId = $this->options->getProductId();
        if ($productId) {
            return 'Отзыв на товар - ' . $productId;
        }

        return 'Отзыв без товара от - ' . $properties['USER_NAME'];
    }

    protected function getPlatformId(Platforms $platform): int
    {
        $propId = IblockHelper::getIblockPropIdByCode('REVIEW_PLATFORM', $this->options->getIblockId());
        return IblockHelper::getEnumValues($propId, [$platform->value])[$platform->value]['id'];
    }
}
