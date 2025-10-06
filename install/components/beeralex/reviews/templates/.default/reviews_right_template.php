<script type="text/x-template" id="vue-reviews-right-template">
    <div class="reviews-right">
        <div v-if="eval_info.avg > 0" class="reviews-grade__body">
            <div class="reviews-eval-header">
                <div class="reviews-eval">{{eval_info.avg}}</div>
                <reviews-star :avg="eval_info.avg" :width="20" :height="20"></reviews-star>
                <div class="reviews-count">{{eval_info.count}}</div>
            </div>

            <div class="reviews-eval-info">
                <div v-for="evalValue in [5, 4, 3, 2, 1]" :key="evalValue" class="reviews-eval_item">
                    <div class="reviews__eval">{{ evalValue }}</div>
                    <div class="reviews__range" :style="`--width: ${eval_info[evalValue].percent}%`"></div>
                    <div class="reviews__count">{{ eval_info[evalValue].count }}</div>
                </div>
            </div>
        </div>
        <div v-else class="reviews-grade__body-empty">
            <p>Оставьте первый отзыв!</p>
            <p>Ваше мнение поможет другим покупателям сделать правильный выбор.</p>
        </div>
        <button v-if="!exits_review" class="reviews-add" :class="{mod__empty: !eval_info.avg}" type="button" data-popup="review_add">
            оставить отзыв
        </button>
    </div>
</script>