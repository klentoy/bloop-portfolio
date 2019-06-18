<template>
  <div class="widget recent-posts">
    <div class="search-bar-section">
      <input type="text" class="form-control" placeholder="Enter portfolio name" v-model="search" v-on:keyup="getfilteredData">
    </div>

    <product-type @getSelected="getSelected"></product-type>

    <!-- <div v-for="(stack,index) in stacks" :key="index" class="form-check form-check-inline">
      <input class="form-check-input" type="checkbox"  v-model="stack.checked" v-on:change="getfilteredData">
      <label class="form-check-label">
      {{ stack.value }}
      </label>
    </div> -->

    <div class="posts" v-if="recentProjectsLoaded">
      <div class="item" v-for="post in filteredData" :key="post.id">
        <!-- {{ post.name }} -->
        {{ post.title.rendered }}
        <!-- <router-link :to="post.slug">{{ post.title.rendered }}</router-link>
 -->
         <div class="categories">
          <div class="category" v-for="cat in post.cats" :key="cat.term_id">
            <span>{{ cat.cat_name }}</span>
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

export default {
  components: {
    ProductType,
  },
  data: function() {
    return {
      filteredData: [],
      search: '',
      product_types: []   
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
      let filteredDataByfilters = [];
      let filteredDataBySearch = [];
      // first check if filters where selected

      
      if (this.product_types.length > 0) {
        filteredDataByfilters = this.filteredData.filter(obj => this.product_types.some(val => obj.product_type.indexOf(parseInt(val)) >= 0));
        this.filteredData = filteredDataByfilters;
      }

      // then filter according to keyword, for now this only affects the name attribute of each data
      if (this.search !== '') {
        filteredDataBySearch = this.filteredData.filter(obj => obj.title.rendered.toLowerCase().match(this.search.toLowerCase()));
        this.filteredData = filteredDataBySearch;
      }
    },
    getSelected: function(value) {
      this.product_types = value;

      this.getfilteredData();

    }
  },
  mounted() {
    this.$store.dispatch("getProjects", { limit: this.limit });
    this.getfilteredData();
  }
};
</script>

<style scoped>
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