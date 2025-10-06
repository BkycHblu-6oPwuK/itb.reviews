<script type="text/x-template" id="vue-pagination-reviews-template">
<nav class="pagination mod__reviews">
        <template v-if="showPagination">
            <div class="pagination__pages">
                <button v-if="pagination.currentPage != 1"
                        type="button"
                        class="pagination__item pagination__arrow arrow__left"
                        @click="changePage(Number(pagination.currentPage) - 1)"
                >
                    <span></span>
                </button>
                <div>
                    <button v-if="pagination.pageCount > 5 && pagination.currentPage > 3"
                            type="button"                       
                            class="pagination__item"
                            @click="changePage(1)"
                    >1</button>
                    <button v-if="pagination.pageCount > 5 && pagination.currentPage > 4"
                            type="button"
                            class="pagination__item"
                            @click="changePage(Math.round(Number(pagination.currentPage) / 2))"
                    >...</button>

                    <button v-for="(page,index) in pagination.pages"
                            type="button"
                            class="pagination__item"
                            :class="{'mod-selected': page.isSelected }"
                            v-if="pagination.currentPage < 4 || pagination.pageCount <= 5"
                            @click="changePage(page.pageNumber)"
                    >{{ page.pageNumber }}</button>
                    <button v-for="(page,index) in pagination.pages"
                            type="button"
                            class="pagination__item"
                            :class="{'mod-selected': page.isSelected }"
                            v-if="(pagination.pageCount > 5 && pagination.currentPage == 4 && index != 4)"
                            @click="changePage(page.pageNumber)"
                    >{{ page.pageNumber }}</button>
                    <button v-for="(page,index) in pagination.pages"
                            type="button"
                            class="pagination__item"
                            :class="{'mod-selected': page.isSelected }"
                            v-if="pagination.pageCount > 5 && pagination.currentPage > 4 && (pagination.currentPage <= (pagination.pageCount - 4)) && ((pagination.currentPage > 3 && index !=0 && index <= 3))"
                            @click="changePage(page.pageNumber)"
                    >{{ page.pageNumber }}</button>
                    <button v-for="(page,index) in pagination.pages"
                            type="button"
                            class="pagination__item"
                            :class="{'mod-selected': page.isSelected }"
                            v-if="pagination.pageCount > 5 && pagination.currentPage > 4 && (pagination.currentPage == (pagination.pageCount - 3)) && index != 0"
                            @click="changePage(page.pageNumber)"
                    >{{ page.pageNumber }}</button>
                    <button v-for="(page,index) in pagination.pages"
                            type="button"
                            class="pagination__item"
                            :class="{'mod-selected': page.isSelected }"
                            v-if="pagination.pageCount > 5 && pagination.currentPage > 4 && (pagination.currentPage > (pagination.pageCount - 3))"
                            @click="changePage(page.pageNumber)"
                    >{{ page.pageNumber }}</button>

                    <button v-if="pagination.pageCount > 5 && pagination.currentPage <= pagination.pageCount - 4"
                            type="button"
                            class="pagination__item"
                            @click="changePage(Math.round((Number(pagination.currentPage) + Number(pagination.pageCount)) / 2))"
                    >...</button>
                    <button v-if="(pagination.pageCount > 5 && pagination.currentPage <= pagination.pageCount - 3) || (pagination.pageCount == 6 && pagination.currentPage == 4)"
                            type="button"
                            class="pagination__item"
                            @click="changePage(pagination.pageCount)"
                    >{{pagination.pageCount}}</button>
                </div>
                <button v-if="pagination.currentPage != pagination.pageCount"
                        type="button"
                        class="pagination__item pagination__arrow arrow__right"
                        @click="changePage(Number(pagination.currentPage) + 1)"
                ><span></span></button>
            </div>
            <button v-if="showMoreButton"
                    class="pagination__more"
                    type="button"
                    @click="changePage(pagination.currentPage, true)"
            >Показать еще</button>
        </template>
    </nav>
</script>