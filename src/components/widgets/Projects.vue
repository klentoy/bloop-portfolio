<template>
  <div class="widget recent-posts">
    <div class="search-bar-section">
      <input type="text" class="form-control" placeholder="Enter portfolio name" v-model="search" v-on:keyup="getfilteredData">
    </div>

    <div class="portfolio-filters-wrap">
      <product-type @getSelectedPtypes="getSelectedPtypes"></product-type>
      <categories @getSelectedCats="getSelectedCats"></categories>
      <tags @getSelectedTags="getSelectedTags"></tags>
      <colors @getSelectedColors="getSelectedColors"></colors>
    </div>

    <div class="posts" v-if="recentProjectsLoaded">
      <div class="item" v-for="post in filteredData" :key="post.id">
        <!-- {{ post.name }} -->
        {{ post.title.rendered }}
        <!-- <router-link :to="post.slug">{{ post.title.rendered }}</router-link>
 -->
         <div class="categories">
          <div class="category" v-for="type in post.prod_type" :key="type.term_id">
            <span>{{ type.name }}</span>
          </div>
        </div>

      </div>
    </div>

    <div v-else>Loading...</div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import ProductType from "../filters/ProductType.vue";
import Categories from "../filters/Categories.vue";
import Tags from "../filters/Tags.vue";
import Colors from "../filters/Colors.vue";

export default {
  components: {
    ProductType,
    Categories,
    Tags,
    Colors
  },
  data: function() {
    return {
      filteredData: [],
      search: '',
      product_types: [],
      categories: [],
      tags: [],
      colors: []   
    }
  },
  props: ["limit"],
  computed: {
    ...mapGetters({
      recentProjects: "recentProjects",
      recentProjectsLoaded: "recentProjectsLoaded"
    }),
    selectedFilters: function() {
      let filters = [];
      let checkedFiters = this.product_types.filter(obj => obj.checked);
      checkedFiters.forEach(element => {
        filters.push(element.value);
      });
      return this.product_types;
    },
  },
  methods: {
    getfilteredData: function() {
      this.filteredData = this.recentProjects(this.limit);
      //let filteredDataByfilters = [];
      let filteredDataBySearch = [];
      
      this.filterSearch( this.colors, 'portfolio_colors' );

      this.filterSearch( this.tags, 'portfolio_tags' );

      this.filterSearch( this.categories, 'portfolio_categories' );

      this.filterSearch( this.product_types, 'product_type' );

      // then filter according to keyword, for now this only affects the name attribute of each data
      if (this.search !== '') {
        filteredDataBySearch = this.filteredData.filter(obj => obj.title.rendered.toLowerCase().match(this.search.toLowerCase()));
        this.filteredData = filteredDataBySearch;
      }
    },
    getSelectedPtypes: function(value) {
      this.product_types = value;
      this.getfilteredData();
    },
    getSelectedCats: function(value) {
      this.categories = value;
      this.getfilteredData();
    },
    getSelectedTags: function(value) {
      this.tags = value;
      this.getfilteredData();
    },
    getSelectedColors: function(value) {
      this.colors = value;
      this.getfilteredData();
    },
    filterSearch: function( selectedItems, filterField ) {
      let filteredDataByfilters = [];
      // check iffilters were selected
      if (selectedItems.length > 0) {
        filteredDataByfilters = this.filteredData.filter(obj => selectedItems.some(val => obj[filterField].indexOf(parseInt(val)) >= 0));
        this.filteredData = filteredDataByfilters;
      }
    }
  },
  mounted() {
    this.$store.dispatch("getProjects", { limit: this.limit });
    this.getfilteredData();
  }
};
</script>

<style scoped>
  .portfolio-filters-wrap {
      display: flex;
      justify-content: space-between;
  }
  
  .search-bar-section { margin: 20px 0;}
  .posts {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
  }

  .item {
    width: 40%;
    background: #eaeaea;
    padding: 20px;
    margin-bottom: 10px;
  }

  .categories {
    margin-top: 17px;
  }

  .category {
      font-size: 12px;
      display: inline-block;
      background: #212121;
      padding: 5px;
      border-radius: 5px;
      color: #fff;
      margin-right: 4px;
  }

</style>