<template>
    <div class="filter-col categories-filter-wrap">
        <span v-for="category in categories" v-bind:key="category.id">
            <input type="checkbox" :value="category.id" v-model="selectedCategories" v-on:change="updateValue($event.target.value)"> <span class="checkbox-label"> {{category.name}} </span> <br>
        </span>
    </div>
</template>
<script>
import api from "../../api";

export default {
    data(){
        return {
            category: '',
            categories: [],
            selectedCategories: []
        }
    },
    created() {
        api.getPortfolioCats( categories => {
            if ( categories ){
                this.categories = categories;
            }
        });
    },
    methods: {
        updateValue: function (value) {
            this.$emit('getSelectedCats', this.selectedCategories);
        }
    }
        // api.getPages(pages => {
        // commit(types.STORE_FETCHED_PAGES, { pages });
        // commit(types.PAGES_LOADED, true);
        // commit(types.INCREMENT_LOADING_PROGRESS);
        // });

}
</script>
