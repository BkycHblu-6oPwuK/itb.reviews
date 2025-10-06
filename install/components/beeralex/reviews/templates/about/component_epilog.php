<script type="text/x-template" id="vue-reviews-template">
    <div class="reviews-block">
        <div class="reviews-header" :class="{mod__empty:!isset_items}">
            <div><p id="reviews-title" class="item-slider__title">{{isset_items ? 'Отзывы' : 'Отзывов пока нет'}}</p></div>
            <div>
                <div v-if="!isset_items">
                    <div class="reviews-grade__body-empty">
                        <p>Оставьте первый отзыв!</p>
                        <p>Ваше мнение поможет другим покупателям сделать правильный выбор.</p>
                    </div>
                </div>
                <button v-if="!exits_review" class="reviews-add" type="button" data-popup="review_add">
                    оставить отзыв
                </button>
            </div>
        </div>
        <div v-if="isset_items" class="reviews-left">

            <div class="sort-reviews">
                <div>Сортировка по:</div>
                <div class="sort-button sort__date" :class="{active:this.sorting_map.field == 'ID'}" @click="selectSorting('ID')">
                    <span>Дата</span>
                    <img class="no__active" v-if="this.sorting_map.field != 'ID'" src="/images/reviews/arrow_rev.svg" alt="">
                    <img class="active" :class="{sort_asc:this.sorting_map.field == 'ID' && this.sorting_map.type == 'ASC'}" v-if="this.sorting_map.field == 'ID'" src="/images/reviews/arrow_rev_active.svg" alt="">
                </div>
                <div class="sort-button sort__eval" :class="{active:this.sorting_map.field == 'EVAL_VALUE'}" @click="selectSorting('EVAL_VALUE')">
                    <span>Оценка</span>
                    <img class="no__active" v-if="this.sorting_map.field != 'EVAL_VALUE'" src="/images/reviews/arrow_rev.svg" alt="">
                    <img class="active" :class="{sort_asc:this.sorting_map.field == 'EVAL_VALUE' && this.sorting_map.type == 'ASC'}" v-if="this.sorting_map.field == 'EVAL_VALUE'" src="/images/reviews/arrow_rev_active.svg" alt="">
                </div>
            </div>

            <reviews-items :reviews="reviews" :openPopup="openPopup"></reviews-items>
            <reviews-pagination 
                v-if="pagination.pageCount > 1" 
                :pagination='pagination' 
                :changePage="changePage">
            </reviews-pagination>
        </div>
        <review-add 
            :params="params"
            :actionAdd="actions.add"
            :setExists="setExists"
            :user_authorize="user_authorize">
        </review-add>
    </div>
</script>

<?
use Bitrix\Main\Page\Asset;
use Bitrix\Main\UI\Extension;
Extension::load([
    'beeralex.reviews'
]);
Asset::getInstance()->addString('<script type="module" src="/bitrix/js/beeralex/reviews/dist/export_swiper.js"></script>');
include_once 'reviews_items_template.php';
include_once 'reviews_right_template.php';
include_once 'reviews_pagination_template.php';
include_once 'reviews_popup_add_template.php';
include_once $_SERVER['DOCUMENT_ROOT'] . $this->GetPath() . '/vueComponents/reviews_star.php';
?>