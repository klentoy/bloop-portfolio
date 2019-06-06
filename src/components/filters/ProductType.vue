<template>
    <div>
        <select name="" id="" v-model="product_type">
            <option value="" v-for="type in product_types" v-bind:key="type.id">{{type.name}}</option>
        </select>
    </div>
</template>
<script>
import api from "../../api";

export default {
    data(){
        return {
            product_type: '',
            product_types: []
        }
    },
    created() {
        api.getProductType( product_type => {
            if ( product_type ){
                this.product_types = product_type;
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
