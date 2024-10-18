<?php
namespace Itb\Reviews\Services;

use Bitrix\Main\Loader;
use Bitrix\Main\Web\Json;
use Itb\Core\Helpers\FilesHelper;
use Itb\Core\Helpers\PaginationHelper;
use Itb\Reviews\Helpers\EvalHelper;
use Itb\Reviews\Models\ReviewsTable;
use Itb\Reviews\Options;

class ReviewsService 
{
    protected Options $options;

    public function __construct(Options $options)
    {
        if(!Loader::includeModule('itb.core')) throw new \Exception("Должен быть установлен модуль itb.core");
        $this->options = $options;
    }

    public function getReviews()
    {
        $productId = $this->options->getProductId();
        $this->options->setPaginationPageCount(ceil(ReviewsTable::getCountReviews($productId) / $this->options->getPaginationLimit()));
        if($productId){
            return $this->getReviewsByProduct($productId);
        }
        global $USER;
        $elements = $this->getElements($productId);
        $elements['isset_items'] = !empty($elements['items']);
        $elements['actions'] = $this->getActions();
        $elements['exits_review'] = false;

        $elements['user_authorize'] = $USER->IsAuthorized();
        $elements['pagination'] = $this->getPagination();
        $elements['sorting'] = $this->getSorting();

        return $elements;
    }

    protected function getActions()
    {
        $urlManager = \Bitrix\Main\Engine\UrlManager::getInstance();
        return [
            'pagination' => $urlManager->create('itb:reviews.ReviewsController.pagination'),
            'add' => $urlManager->create('itb:reviews.ReviewsController.add'),
            'sorting' => $urlManager->create('itb:reviews.ReviewsController.sorting'),
            'get' => $urlManager->create('itb:reviews.ReviewsController.get'),
        ];
    }

    public function getReviewsByProduct(string|int $productId)
    {
        global $USER;
        $elements = $this->getElements($productId);
        $elements['isset_items'] = !empty($elements['items']);
        $elements['eval_info'] = EvalHelper::getEvalInfo($productId);
        $elements['files'] = $this->options->getShowFilesByProduct() ? ReviewsTable::getFiles($productId, $this->options->getLimitFiles()) : [];

        $elements['actions'] = $this->getActions();

        $elements['exits_review'] = false;
        $elements['user_authorize'] = $USER->IsAuthorized();
        
        $elements['pagination'] = $this->getPagination();
        $elements['sorting'] = $this->getSorting();

        return $elements;
    }

    public function getElements(string|int $productId)
    {
        return ReviewsTable::getElements($productId, $this->options->getSorting(), $this->options->getPagination(), $this->options->getShowInfoProduct());
    }

    protected function getPagination()
    {
        $current = $this->options->getPaginationCurrent();
        $count =  $this->options->getPaginationPageCount();
        return [
            'currentPage' => $current,
            'limit' => $this->options->getPaginationLimit(),
            'pageCount' => $count,
            'pages' => PaginationHelper::getPages($current, $count),
        ];
    }

    protected function getSorting()
    {
        return [
            'field' => $this->options->getSortingField(),
            'type' => $this->options->getSortingType(),
        ];
    }

    public function uploadFiles(array $files)
    {
        $toSavefiles = FilesHelper::getFormattedToSafe($files);
        $arSaveFiles = [];
        if(!empty($toSavefiles)){
            foreach($toSavefiles as $file){
                $id_file = \CFile::SaveFile($file,'reviews');
                if(str_starts_with($file['type'], 'video')){
                    $thumbnail = $this->addThumbnail($id_file);
                    $arSaveFiles['preview'] = [
                        'file_array' => \CFile::MakeFileArray($thumbnail),
                        'thumbnail_path' => $thumbnail
                    ];
                }
                $arSaveFiles['ids'][] = $id_file;
            }
        }
        return $arSaveFiles;
    }

    private function addThumbnail(string|int $id_file)
    {
        $path = tempnam(sys_get_temp_dir(), "img") . '.jpg';
        $videoPath = $_SERVER['DOCUMENT_ROOT']. \CFile::GetPath($id_file);
        $ffmpeg = \FFMpeg\FFMpeg::create();
        $video = $ffmpeg->open($videoPath);
        $frame = $video->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds(1));
        $frame->save($path);
        return $path;
    }

    public function add($form)
    {
        global $USER;
        Loader::includeModule('iblock');
        $form = Json::decode($form);
        $files = [];

        if(isset($_FILES['files'])) $files = $this->uploadFiles($_FILES['files']);

        $property = [
            'PRODUCT' => $this->options->getProductId(),
            'EVAL' => $form['eval'],
            'REVIEW' => $form['review'],
            'FILES' => $files['ids'],
            'OFFER' => $form['offer'],
            'CONTACT_DETAILS' => $form['contact']
        ];

        if($USER->IsAuthorized()){
            $property['USER'] = $USER->GetID();
            $lastName = !empty($USER->GetLastName())
            ? mb_strtoupper(mb_strlen($USER->GetLastName()) === 1
                ? $USER->GetLastName()
                : mb_substr($USER->GetLastName(), 0, 1))
            : '';

            $property['USER_NAME'] = $USER->GetFirstName() . ' ' . $lastName . '.';
        } else {
            $property['USER_NAME'] = $form['user_name'];
        }

        $name = $this->options->getProductId() ? "Отзыв на товар - " . $this->options->getProductId() : 'Отзыв без товара от - ' . $property['USER_NAME'];

        $data = [
            "IBLOCK_ID" => $this->options->getIblockId(),
            'NAME' => $name,
            'IBLOCK_SECTION_ID' => false,
            "ACTIVE" => 'N',
            'PROPERTY_VALUES' => $property
        ];

        if($files['preview']) $data['PREVIEW_PICTURE'] = $files['preview']['file_array'];

        $id = (new \CIBlockElement)->Add($data);

        if($files['preview']['thumbnail_path']) unlink($files['preview']['thumbnail_path']);

        return $id;
    }

    public function sorting($sorting, $pagination): array
    {
        $result = [];
        $pagination = Json::decode($pagination);
        $sorting = Json::decode($sorting);

        $this->options->setPaginationCurrent(1)
                ->setPaginationPageCount($pagination['pageCount'])
                ->setSorting($sorting['field'],$sorting['type']);

        $result = $this->getElements($this->options->getProductId());
        $result['pagination'] = $this->getPagination();
        return $result;
    }

    public function pagination($pagination, $sorting): array
    {
        $result = [];
        $pagination = Json::decode($pagination);
        $sorting = Json::decode($sorting);

        $this->options->setPaginationCurrent($pagination['currentPage'])
                ->setPaginationPageCount($pagination['pageCount'])
                ->setSorting($sorting['field'], $sorting['type']);

        $result = $this->getElements($this->options->getProductId());
        $result['pagination'] = $this->getPagination();
        return $result;
    }
}