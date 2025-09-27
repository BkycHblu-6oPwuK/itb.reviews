<script type="text/x-template" id="vue-product-detail-reviews-star-template">
  <div class="stars-items">
        <div v-for="i in 5" :key="i" class="star-container">
              <svg
                v-if="i <= Math.floor(avgFixed)"
                :width="width"
                :height="height"
                viewBox="0 0 20 19"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path d="M10 0L12.9389 5.95492L19.5106 6.90983L14.7553 11.5451L15.8779 18.0902L10 15L4.12215 18.0902L5.24472 11.5451L0.489435 6.90983L7.06107 5.95492L10 0Z" fill="#FF0000"/>
              </svg>
              <svg
                v-else-if="i === Math.ceil(avgFixed)"
                :width="width"
                :height="height"
                viewBox="0 0 20 19"
                xmlns="http://www.w3.org/2000/svg"
              >
                <defs>
                  <clipPath id="clip-path">
                    <rect :width="getSizeStar()" height="20"/>
                  </clipPath>
                </defs>
                <path
                  d="M10 0L12.9389 5.95492L19.5106 6.90983L14.7553 11.5451L15.8779 18.0902L10 15L4.12215 18.0902L5.24472 11.5451L0.489435 6.90983L7.06107 5.95492L10 0Z"
                  fill="#E0E0E0"
                />
                <path
                  d="M10 0L12.9389 5.95492L19.5106 6.90983L14.7553 11.5451L15.8779 18.0902L10 15L4.12215 18.0902L5.24472 11.5451L0.489435 6.90983L7.06107 5.95492L10 0Z"
                  fill="#FF0000"
                  clip-path="url(#clip-path)"
                />
              </svg>
              <svg
                v-else
                :width="width"
                :height="height"
                viewBox="0 0 20 19"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path d="M10 0L12.9389 5.95492L19.5106 6.90983L14.7553 11.5451L15.8779 18.0902L10 15L4.12215 18.0902L5.24472 11.5451L0.489435 6.90983L7.06107 5.95492L10 0Z" fill="#E0E0E0"/>
              </svg>
        </div>
    </div>
</script>

<style>
  .stars {
    display: flex;
    gap: 2px;
  }

  .star-container {
    display: flex;
  }

  .stars-items {
    display: flex;
    align-items: center;
    gap: 2px;
  }
</style>

<script>
  Vue.component('reviews-star', {
    template: '#vue-product-detail-reviews-star-template',
    props: {
      avg: {
        type: Number,
        default: 0,
      },
      width: {
        type: Number,
        default: 14,
      },
      height: {
        type: Number,
        default: 14,
      },
    },
    methods: {
      getSizeStar() {
        let minSize = 4;
        let size = Math.round((this.avgFixed % 1) * 20);
        if (size < minSize) size = minSize;
        return size + 'px';
      }
    },
    computed: {
      avgFixed() {
        return Number(this.avg).toFixed(1)
      }
    },
  });
</script>