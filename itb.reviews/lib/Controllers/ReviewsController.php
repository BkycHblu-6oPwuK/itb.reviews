<?php
namespace Itb\Reviews\Controllers;
use Bitrix\Main\Engine\ActionFilter\Csrf;

class ReviewsController extends BaseController
{
    public function configureActions(): array
    {
        return [
            'add' => [
                'prefilters' => [
                    new Csrf,
                ],
            ],
            'pagination' => [
                'prefilters' => [
                    new Csrf,
                ],
            ],
            'sorting' => [
                'prefilters' => [
                    new Csrf,
                ],
            ],
            'get' => [
                'prefilters' => [
                    new Csrf,
                ],
            ],
        ];
    }

    public function addAction($form)
    {
        return $this->service->add($form, $_FILES['files'] ?? []);
    }

    public function paginationAction($pagination, $sorting)
    {
        return $this->service->pagination($pagination, $sorting);
    }

    public function sortingAction($sorting, $pagination)
    {
        return $this->service->sorting($sorting, $pagination);
    }
    
    public function getAction()
    {
        return $this->service->getReviews();
    }
}