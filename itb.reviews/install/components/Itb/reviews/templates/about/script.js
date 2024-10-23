window.addEventListener('DOMContentLoaded', () => {
    const reviews = window.ITB.Reviews;
    const reviewsApp = new Vue({
        el: "#vue-reviews",
        template: '#vue-reviews-template',
        data() {
            return reviews
        },
        mounted() {},
        methods: {
            async sorting() {
                try {
                    let formData = new URLSearchParams();
                    formData.append('params', JSON.stringify(this.params));
                    formData.append('sorting', JSON.stringify(this.sorting_map));
                    formData.append('pagination', JSON.stringify(this.pagination));
                    formData.append('sessid', BX.message('bitrix_sessid'));
                    const response = await fetch(this.actions.sorting, {
                        method: "POST",
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
                        },
                        body: formData,
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }

                    const data = await response.json();
                    if (data.data.items) {
                        this.reviews = data.data.items;
                        this.pagination = data.data.pagination;
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            },
            selectSorting(field) {
                if (field == this.sorting_map.field) {
                    if (this.sorting_map.type == 'DESC') {
                        this.sorting_map.type = 'ASC'
                    } else {
                        this.sorting_map.type = 'DESC'
                    }
                } else {
                    this.sorting_map.field = field
                    this.sorting_map.type = 'DESC'
                }
                this.sorting()
            },
            async changePage(page, isShowMore = false) {
                try {

                    if (isShowMore) {
                        this.pagination.currentPage++;
                    } else {
                        this.pagination.currentPage = page
                    }

                    let formData = new URLSearchParams();
                    formData.append('params', JSON.stringify(this.params));
                    formData.append('pagination', JSON.stringify(this.pagination));
                    formData.append('sorting', JSON.stringify(this.sorting_map));
                    formData.append('sessid', BX.message('bitrix_sessid'));
                    const response = await fetch(this.actions.pagination, {
                        method: "POST",
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
                        },
                        body: formData,
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }

                    const data = await response.json();

                    if (data.data.items) {
                        if (isShowMore) {
                            data.data.items.forEach(item => {
                                this.reviews.push(item)
                            });
                        } else {
                            this.reviews = data.data.items;
                        }
                        this.pagination = data.data.pagination;
                    }

                    if (!isShowMore) {
                        let reviewsItems = document.querySelector('.sort-reviews');
                        if (reviewsItems) {
                            var scrollPosition = reviewsItems.getBoundingClientRect().top + window.scrollY - 100;
                            window.scrollTo({
                                top: scrollPosition,
                                behavior: 'smooth'
                            });
                        }
                    }
                } catch (error) {
                    console.error('Error:', error);
                    $.notify('Невозможно выполнить запрос. Попробуйте позже.', 'error');
                } finally {

                }
            },
            setExists(value) {
                this.exits_review = value
            },
            openPopup(name) {
                Fancybox.bind(`.${name}`, {});
            }
        },
        components: {
            'reviews-items': {
                template: '#vue-reviews-items-template',
                props: {
                    reviews: Array | Object,
                    openPopup: Function,
                },
                mounted() { },
                components: {
                    'reviews-item': {
                        template: '#vue-reviews-item-template',
                        props: {
                            params: Object,
                            openPopup: Function,
                        },
                        data() {
                            return {
                                showText: false,
                                showButton: false,
                                showResponse: true,
                            }
                        },
                        mounted() {
                            if (this.review.review && this.review.review.length > 100) {
                                this.showButtonShowText()
                            }
                        },
                        methods: {
                            showTextMethod() {
                                this.showText = !this.showText
                                if (this.$refs.review_text) {
                                    let scrollPosition = this.$refs.review_text.getBoundingClientRect().top + window.scrollY - 120;
                                    window.scrollTo({
                                        top: scrollPosition,
                                        behavior: 'smooth'
                                    });
                                }
                            },
                            showButtonShowText() {
                                let textBlock = this.$refs.review_text
                                let textBlockHeight = textBlock.clientHeight;
                                let lineHeight = parseInt(window.getComputedStyle(textBlock).lineHeight);
                                let lineCount = Math.round(textBlockHeight / lineHeight);
                                if (lineCount == 5) {
                                    this.showButton = true;
                                } else {
                                    this.showButton = false;
                                }
                            },
                        },
                        computed: {
                            review() {
                                return this.params.review
                            },
                            index_rev() {
                                return this.params.index_rev
                            }
                        }
                    }
                },
            },
            'reviews-pagination': {
                template: '#vue-pagination-reviews-template',
                props: {
                    pagination: Object,
                    changePage: Function,
                },
                data() {
                    return {
                        isMobile: window.innerWidth < 768
                    }
                },
                mounted() { },
                methods: {},
                computed: {
                    showPagination() {
                        return this.pagination.pages.length > 0;
                    },
                    showMoreButton() {
                        return this.pagination.pageCount > this.pagination.currentPage
                    }
                },
            },
            'review-add': {
                template: '#popup-reviews-add',
                props: {
                    params: Object,
                    setExists: Function,
                    actionAdd: String,
                    user_authorize: Boolean
                },
                data() {
                    return {
                        form: {
                            eval: '',
                            review: '',
                            user_name: '',
                            contact: ''
                        },
                        product_id: '',
                        files: [],
                        validationErrors: {
                            eval: null,
                            review: null,
                            files: null,
                            user_name: null,
                        },
                        isRequest: false,
                        reviewAdded: false,
                    }
                },
                mounted(){
                    window.popups.reinitialize();
                },
                methods: {
                    handleFileChange(event) {
                        let videoCount = this.files.filter(file => file.type.startsWith('video')).length;
                        let filesCount = this.files.length;
                        for (let index in event.target.files) {
                            if (event.target.files[index].size) {
                                let size = Math.round(event.target.files[index].size / 1000000);
                                if (size < 101) {
                                    let isVideo = event.target.files[index].type.startsWith('video');
                                    let isImage = event.target.files[index].type.startsWith('image');
                                    if (isImage || isVideo) {
                                        if(filesCount > 4){
                                            $.notify('Максимум 5 файлов', 'error');
                                            return;
                                        }
                                        if (isVideo && videoCount >= 1) {
                                            $.notify('Можно загрузить не больше одного видео', 'error');
                                        } else {
                                            this.files.push(event.target.files[index]);
                                            if (isVideo) {
                                                videoCount++;
                                            }
                                            filesCount++
                                        }
                                    }
                                } else {
                                    $.notify('Размер загружаемого файла должен быть не больше 100мб', 'error');
                                }
                            }
                        }
                    },
                    setRating(star) {
                        this.form.eval = star;
                    },
                    getImageUrl(file) {
                        let url;
                        if(file.type.startsWith('video')){
                            url = '/images/reviews/video_camera.png';
                        } else {
                            url = URL.createObjectURL(file);
                        }
                        return url;
                    },
                    deleteImage(index) {
                        this.files.splice(index, 1)
                    },
                    async add() {
                        if (this.validateForm()) {
                            try {
                                this.isRequest = true;
                                const xhr = new XMLHttpRequest();
                                const formData = new FormData();

                                formData.append('params', JSON.stringify(this.params));
                                formData.append('product_id', this.product_id);
                                formData.append('form', JSON.stringify(this.form));
                                formData.append('sessid', BX.message('bitrix_sessid'));
                                for (let i = 0; i < this.files.length; i++) {
                                    formData.append('files[]', this.files[i]);
                                }
                                
                                xhr.upload.addEventListener('progress', (event) => {
                                    if (event.lengthComputable) {
                                        const percentComplete = (event.loaded / event.total) * 100;
                                        if(percentComplete < 100){
                                            $.notify(`Загрузка файлов на сервер - ${percentComplete}%`, 'info');
                                        }
                                    }
                                });
                    
                                xhr.open('POST', this.actionAdd, true);
                    
                                xhr.onload = function () {
                                    if (xhr.status >= 200 && xhr.status < 300) {
                                        const data = JSON.parse(xhr.responseText);
                                        if (data.data) {
                                            window.popups.close('review_add');
                                            $.notify('Благодарим Вас за отзыв! После модерации отзыв будет опубликован на сайте', 'success');
                                            //this.reviewAdded = true;
                                        }
                                    } else {
                                        $.notify('Невозможно выполнить запрос. Попробуйте позже.', 'error');
                                        console.error('Error:', xhr.statusText);
                                    }
                                    this.isRequest = false;
                                }.bind(this);
                    
                                xhr.onerror = function () {
                                    $.notify('Невозможно выполнить запрос. Попробуйте позже.', 'error');
                                    console.error('Error:', xhr.statusText);
                                    this.isRequest = false;
                                }.bind(this);
                    
                                xhr.send(formData);
                            } catch (error) {
                                $.notify('Невозможно выполнить запрос. Попробуйте позже.', 'error');
                                console.error('Error:', error);
                                this.isRequest = false;
                            }
                        }
                    },
                    validateForm(noValid = []) {
                        let isValid = true;
                        isValid = !noValid.includes('eval') ? this.validateText(false, "eval", 'Поставьте оценку товару') && isValid : isValid;
                        if(!this.user_authorize){
                            isValid = !noValid.includes('user_name') ? this.validateText(false, "user_name", 'Введите ваше имя') && isValid : isValid;
                        }
                        return isValid;
                    },
                    validateText(noCheck = false, field, text, notify = false) {
                        let isValid = true;
                        if (noCheck == true) {
                            this.$set(this.validationErrors, field, null);
                            return isValid;
                        }
                        if (this.form[field].length == 0) {
                           this.$set(this.validationErrors, field, text);
                            if(notify){
                                $.notify(this.validationErrors[field], 'error');
                            }
                            isValid = false;
                        } else {
                            this.$set(this.validationErrors, field, null);
                        }
                        return isValid;
                    },
                },
            },
        }
    })
    window.reviewsApp = reviewsApp
})