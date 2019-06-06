<template>
  <div>
    <product-type></product-type>
    <categories></categories>
    <tags></tags>
    <colors></colors>
    <!-- <template v-if="allPagesLoaded">
      <h1>{{ pageContent.title.rendered }}</h1>
      <div v-html="pageContent.content.rendered"></div>
    </template>
    <Loader v-else/> -->
  </div>
</template>

<script>
import Loader from "../partials/Loader.vue";  
import ProductType from "../filters/ProductType.vue";
import Categories from "../filters/Categories.vue";
import Tags from "../filters/Tags.vue";
import Colors from "../filters/Colors.vue";
import { mapGetters } from "vuex";


export default {
  components: {
    Loader,
    ProductType,
    Categories,
    Tags,
    Colors,
  },
  computed: {
    ...mapGetters({
      page: "page", 
      allPagesLoaded: "allPagesLoaded"
    }),

    pageContent() {
      return this.page(this.$route.params.pageSlug);
    }
  },

};
</script>
