<script type="text/x-template" id="vue-reviews-items-template">
    <div class="reviews-items">
        <template v-for="(review, index_rev) in reviews">
            <reviews-item
                    :key="review.id"
                    :params="{'review':review,'index_rev':index_rev}"
                    :openPopup="openPopup">
            </reviews-item>
        </template>
    </div>
</script>
<script type="text/x-template" id="vue-reviews-item-template">
    <div class="reviews-item">
        <div class="revies_body">
            <div class="revies_body-header">
                <div class="body-header_left">
                    <div class="body_user-name" v-if="review.user_name">{{review.user_name}}</div>
                    <div class="body_date">{{review.date}} г.</div>
                    <div class="body_offer" v-if="review.offer_size">Размер {{review.offer_size}}</div>
                    <div class="body_offer" v-if="review.product_info">
                        <a v-if="review.product_info.url" :href="review.product_info.url">{{review.product_info.text}}</a>
                        <span v-else>{{review.product_info.text}}</span>
                    </div>
                </div>
                <div class="body-header_right">
                    <reviews-star :avg="review.eval" :width="20" :height="20"></reviews-star>
                </div>
            </div>
            <div class="reviews-content">
                <div v-if="review.product_info && review.product_info.preview" class="preview-product">
                    <a v-if="review.product_info.url" :href="review.product_info.url">
                        <img :src="review.product_info.preview">
                    </a>
                    <img v-else :src="review.product_info.preview">
                </div>
                <div>
                    <div v-if="review.review" ref="review_text" class="revies_body-text"
                    :class="{'body-text__hidden':!showText}">{{review.review}}</div>
                    <div class="reviews_body-text_button" v-if="showButton" @click="showTextMethod">{{!showText ? 'Читать весь отзыв' : 'Скрыть'}}</div>
                    <div v-if="review.files.length" class="revies_body-images">
                        <div v-for="(file, index) in review.files" :key="file.id" class="body-images_item">
                            <a :href="file.src"
                               :class="[
                                  `zoom-${index_rev}`,
                                  { 'img-container__type-video': file.type === 'video' }
                                ]" 
                                :data-fancybox="`gallery-reviews-${index_rev}`"
                                @click="openPopup(`zoom-${index_rev}`)" 
                            >
                                <img :src="file.type == 'video' ? file.thumbail : file.src">
                            </a>
                        </div>
                    </div>
                    <div v-if="review.store_response" class="store-response-btn" :class="{active:showResponse}" @click="showResponse = !showResponse">
                        <span>Ответ магазина</span>
                        <img class="no__active" src="/images/reviews/arrow_rev.svg" alt="">
                    </div>
                    <div v-if="showResponse" class="store-response" v-html="review.store_response"></div>
                </div>
            </div>
        </div>
    </div>
</script>