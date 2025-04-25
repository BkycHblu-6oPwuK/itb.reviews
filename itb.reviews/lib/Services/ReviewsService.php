<?php

namespace Itb\Reviews\Services;

use Bitrix\Main\DI\ServiceLocator;
use Itb\Core\Helpers\PaginationHelper;
use Itb\Reviews\Contracts\CreatorContract;
use Itb\Reviews\Helpers\EvalHelper;
use Itb\Reviews\Models\ReviewsTable;
use Itb\Reviews\Options;

class ReviewsService
{
    protected Options $options;
    protected bool $isImport;

    public function __construct(Options $options, bool $isImport = false)
    {
        $this->options = $options;
        $this->isImport = $isImport;
    }

    public function getReviews(): array
    {
        $productId = $this->options->getProductId();

        $this->updatePaginationCount($productId);

        if ($productId > 0) {
            return $this->getReviewsByProduct($productId);
        }

        return $this->buildReviewsData(0, false);
    }

    public function getReviewsByProduct(int $productId): array
    {
        return $this->buildReviewsData($productId, true);
    }

    protected function buildReviewsData(int $productId, bool $withEvalAndFiles): array
    {
        global $USER;

        $elements = $this->getElements($productId);
        $elements['isset_items'] = !empty($elements['items']);
        $elements['actions'] = $this->getActions();
        $elements['exits_review'] = false;
        $elements['user_authorize'] = $USER?->IsAuthorized() ?? false;
        $elements['pagination'] = $this->getPagination();
        $elements['sorting'] = $this->getSorting();

        if ($withEvalAndFiles) {
            $elements['eval_info'] = EvalHelper::getEvalInfo($productId);
            $elements['files'] = $this->options->getShowFilesByProduct()
                ? ReviewsTable::getFiles($productId, $this->options->getLimitFiles())
                : [];
        }

        return $elements;
    }

    protected function updatePaginationCount(int $productId): void
    {
        $count = ReviewsTable::getCountReviews($productId);
        $limit = $this->options->getPaginationLimit();
        $this->options->setPaginationPageCount(ceil($count / $limit));
    }

    public function getElements(int $productId)
    {
        return ReviewsTable::getElements($productId, $this->options->getSorting(), $this->options->getPagination(), $this->options->getShowInfoProduct(), $this->options->getPlatform());
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

    public function add(array $form, array $files)
    {
        /** @var CreatorContract */
        $creator = ServiceLocator::getInstance()->get(CreatorContract::class);
        return $creator->create($form, $files);
    }

    public function sorting(array $sorting, array $pagination): array
    {
        $pagination['currentPage'] = 1;
        return $this->loadElements($pagination, $sorting);
    }

    public function pagination(array $pagination, array $sorting): array
    {
        return $this->loadElements($pagination, $sorting);
    }

    public function loadElements(array $pagination, array $sorting): array
    {
        $this->options
            ->setPaginationCurrent($pagination['currentPage'] ?? 1)
            ->setPaginationPageCount($pagination['pageCount'] ?? 1)
            ->setSorting($sorting['field'], $sorting['type']);

        $result = $this->getElements($this->options->getProductId());
        $result['pagination'] = $this->getPagination();

        return $result;
    }
}
