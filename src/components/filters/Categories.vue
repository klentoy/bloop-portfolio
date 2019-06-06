<template>
    <div>
        <select name="" id="" v-model="category">
            <option value="" v-for="category in categories" v-bind:key="category.id">{{category.name}}</option>
        </select>
    </div>
</template>
<script>
import api from "../../api";

export default {
    data(){
        return {
            category: '',
            categories: []
        }
    },
    created() {
        api.getPortfolioCats( categories => {
            if ( categories ){
                this.categories = categories;
            }
        });

        // api.getPages(pages => {
        // commit(types.STORE_FETCHED_PAGES, { pages });
        // commit(types.PAGES_LOADED, true);
        // commit(types.INCREMENT_LOADING_PROGRESS);
        // });
    }
}
</script>
