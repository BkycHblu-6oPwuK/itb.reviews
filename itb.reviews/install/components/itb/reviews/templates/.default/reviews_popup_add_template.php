<script type="text/x-template" id="popup-reviews-add">
    <div class="popup js-popup" data-popup="review_add" data-type="full">
        <div class="popup__container js-popup-container">
            <span class="form-reviews__title" :class="{review__added: reviewAdded}">{{reviewAdded ? 'Благодарим Вас за отзыв!' : 'Ваш отзыв'}}</span>
            <div v-if="!reviewAdded" class="form">
                <div class="form__section auth__form">
                    <div class="form__row input" :class="validationErrors.eval != null ? 'mod-error' : ''" :data-error="validationErrors.eval" id="eval_review" class=" input__container mod-with-text">
                        <label for="eval_review">Ваша оценка *</label>
                        <div class="input__container-eval input__container mod-with-text">
                            <button v-for="star in 5" :key="star" :class="{'selected': star <= form.eval}" @click="setRating(star)">
                                <img :src="star <= form.eval ? '/images/reviews/Star.svg' : '/images/reviews/Star-no.svg'" alt="Star" />
                            </button>
                        </div>
                    </div>
                    <div v-if="!user_authorize" class="form__row input">
                        <label for="review_name">Ваше имя *</label>
                        <div :class="validationErrors.user_name != null ? 'mod-error' : ''" :data-error="validationErrors.user_name" class="input__container mod-with-text">
                            <input id="review_name" v-model="form.user_name" @focus="validateText(true, 'user_name')" @blur="validateText(false, 'user_name', 'Введите ваше имя')" placeholder="Введите ваше имя"/>
                        </div>
                    </div>
                    <div class="form__row input">
                        <label for="review_text">Комментарий</label>
                        <div class="input__container mod-with-text">
                            <textarea v-model="form.review" cols="30" rows="10" id="review_text" maxlength="500"  class="review_text" placeholder="Расскажите о товаре"></textarea>
                        </div>
                    </div>
                    <div class="form__row input">
                        <label for="review_text">Фото и видео товара</label>
                        <div class="label-file_wrapper">
                        <label for="file_upload" class="custom-file-label">
                          <span class="label-files__input">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24.000000" height="24.000000" viewBox="0 0 24 24" fill="none">
                                <defs>
                                    <clipPath id="clip2137_1660">
                                        <rect id="icon / iconoir / add-media-image" rx="-0.500000" width="23.000000" height="23.000000" transform="translate(0.500000 0.500000)" fill="white" fill-opacity="0"/>
                                    </clipPath>
                                </defs>
                                <g clip-path="url(#clip2137_1660)">
                                    <path id="Vector" d="M13 21L3.59 21C3.26 21 3 20.73 3 20.4L3 3.59C3 3.26 3.26 3 3.59 3L20.4 3C20.73 3 21 3.26 21 3.59L21 13" stroke="#8B8B8B" stroke-opacity="1.000000" stroke-width="1.500000" stroke-linejoin="round" stroke-linecap="round"/>
                                    <path id="Vector" d="M3 16L10 13L15.5 15.5" stroke="#8B8B8B" stroke-opacity="1.000000" stroke-width="1.500000" stroke-linejoin="round" stroke-linecap="round"/>
                                    <path id="Vector" d="M14 8C14 6.89 14.89 6 16 6C17.1 6 18 6.89 18 8C18 9.1 17.1 10 16 10C14.89 10 14 9.1 14 8Z" stroke="#8B8B8B" stroke-opacity="1.000000" stroke-width="1.500000" stroke-linejoin="round"/>
                                    <path id="Vector" d="M19 19L16 19L19 19L22 19L19 19M19 19L19 16L19 19M19 19L19 22" stroke="#8B8B8B" stroke-opacity="1.000000" stroke-width="1.500000" stroke-linejoin="round" stroke-linecap="round"/>
                                </g>
                            </svg>
                            <p class="notice__file-label">Добавьте до 5 шт</p>
                          </span>
                        </label>
                            <div v-if="files && files.length">
                                <div class="files-items">
                                    <div class="file-item" v-for="(file, index) in files" :key="index">
                                        <div class="file__delete" @click.stop="deleteImage(index)">
                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20.000000" height="20.000000" viewBox="0 0 20 20" fill="none">
                                                <defs>
                                                    <clipPath id="clip2137_1885">
                                                        <rect id="/x" rx="-0.500000" width="19.000000" height="19.000000" transform="translate(0.500000 0.500000)" fill="white" fill-opacity="0"/>
                                                    </clipPath>
                                                </defs>
                                                <rect id="/x" rx="-0.500000" width="19.000000" height="19.000000" transform="translate(0.500000 0.500000)" fill="#FFFFFF" fill-opacity="0"/>
                                                <g clip-path="url(#clip2137_1885)">
                                                    <path id="Vector" d="M15 5L5 15" stroke="#000000" stroke-opacity="1.000000" stroke-width="1.500000" stroke-linejoin="round" stroke-linecap="round"/>
                                                    <path id="Vector" d="M5 5L15 15" stroke="#000000" stroke-opacity="1.000000" stroke-width="1.500000" stroke-linejoin="round" stroke-linecap="round"/>
                                                </g>
                                            </svg>
                                        </div>
                                        <img :src="getImageUrl(file)" :alt="file.name" class="preview-image_uploading">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="input__container mod-with-text">
                            <div class="custom-file-upload">
                                <input type="file" @change="handleFileChange" id="file_upload" multiple accept="image/*,video/*">
                            </div>
                        </div>
                    </div>
                    <div class="form__row input">
                        <label for="review_text">Хотите, чтобы мы с вами связались? Оставьте свои контактные данные</label>
                        <div class="input__container mod-with-text">
                            <input type="text" v-model="form.contact" class="review_text" placeholder="Введите телефон или email">
                        </div>
                    </div>
                </div>
                <div class="form__section auth__controls reviews-form__control">
                    <button type="submit" class="button mod-first mod-border btn-reviews__submit" @click.stop="add">Создать отзыв</button>
                    <p class="reviews-accept_licenses">Нажимая на кнопку, вы соглашаетесь с <a href="/client/rules/">правилами обработки информации</a></p>
                </div>
            </div>
            <div v-else class="reviews-grade__body-empty review__added">
               <p>После модерации отзыв будет опубликован на сайте.</p>
            </div>
        </div>
        <button class="popup__close js-popup-close">
            <i class="i-popup-close mod-no-sm"></i>
            <i class="i-notify-close mod-only-sm"></i>
        </button>
    </div>
</script>