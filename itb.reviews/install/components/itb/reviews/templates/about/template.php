<?php
use Bitrix\Main\Web\Json;
?>

<div class="reviews-block" id="vue-reviews">
    <div class="reviews-header">
        <p id="reviews-title" class="item-slider__title"><?= $arResult['isset_items'] ? 'Отзывы' : 'Отзывов пока нет' ?></p>
    </div>
    <? if ($arResult['isset_items']) : ?>
        <div class="reviews-left">
            <div class="sort-reviews">
                <div>Сортировка по:</div>
                <div class="sort-date">Дата</div>
                <div class="sort-eval">Оценка</div>
            </div>
            <div class="reviews-items">
                <? foreach ($arResult['items'] as $item) : ?>
                    <div class="reviews-item">
                        <div class="revies_body">
                            <div class="revies_body-header">
                                <div class="body-header_left">
                                    <div class="body_user-name"><?= $item['user_name'] ?></div>
                                    <div class="body_date"><?= $item['date'] ?> г.</div>
                                    <? if (!empty($item['offer_size'])) : ?>
                                        <div class="body_offer">Размер <?= $item['offer_size'] ?></div>
                                    <? endif; ?>
                                    <? if ($item['product_info']): ?>
                                        <div class="body_offer">
                                            <? if ($item['product_info']['url']): ?>
                                                <a href="<?= $item['product_info']['url'] ?>"><?= $item['product_info']['text'] ?></a>
                                            <? else: ?>
                                                <span><?= $item['product_info']['text'] ?></span>
                                            <? endif; ?>
                                        </div>
                                    <? endif; ?>
                                </div>
                                <div class="body-header_right">
                                    <div class="stars-items">
                                        <?
                                        for ($i = 1; $i <= 5; $i++) :
                                            if ($item['eval'] >= $i) {
                                                echo '<img src="/images/reviews/Star.svg">';
                                            } else {
                                                echo '<img src="/images/reviews/Star-no.svg">';
                                            }
                                        endfor;
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="reviews-content">
                                <? if ($item['product_info'] && $item['product_info']['preview']): ?>
                                    <div class="preview-product">
                                        <? if ($item['product_info']['url']): ?>
                                            <a href="<?= $item['product_info']['url'] ?>">
                                                <img src="<?= $item['product_info']['preview'] ?>">
                                            </a>
                                        <? else: ?>
                                            <img src="<?= $item['product_info']['preview'] ?>">
                                        <? endif; ?>
                                    </div>
                                <? endif; ?>
                                <div>
                                    <? if ($item['review']) : ?>
                                        <div class="revies_body-text"><? echo strip_tags($item['review']) ?></div>
                                    <? endif; ?>
                                    <? if (!empty($item['files'])) : ?>
                                        <div class="revies_body-images">
                                            <? foreach ($item['files'] as $picture) : ?>
                                                <div class="body-images_item"><img src="<?= $picture['thumbail'] ?? $picture['src'] ?>"></div>
                                            <? endforeach; ?>
                                        </div>
                                    <? endif; ?>
                                    <? if($item['store_response']): ?>
                                        <div class="store-response-btn" class="active">
                                            <span>Ответ магазина</span>
                                        </div>
                                    <? endif; ?>
                                    <div class="store-response"><?=$item['store_response']?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <? endforeach; ?>
            </div>
        </div>
    <? endif; ?>
</div>


<script>
    let data = {
        reviews: JSON.parse(<?= var_export(Json::encode($arResult['items'])) ?>),
        pagination: JSON.parse(<?= var_export(Json::encode($arResult['pagination'])) ?>),
        user_authorize: <?= var_export($arResult['user_authorize']) ?>,
        isset_items: <?= var_export($arResult['isset_items']) ?>,
        exits_review: <?= var_export($arResult['exits_review']) ?>,
        actions: JSON.parse(<?= var_export(Json::encode($arResult['actions'])) ?>),
        sorting_map: JSON.parse(<?= var_export(Json::encode($arResult['sorting'])) ?>),
        params: '<?= $this->getComponent()->getSignedParameters() ?>',
    }
    window.ITB = window.ITB || {};
    window.ITB.Reviews = data
</script>