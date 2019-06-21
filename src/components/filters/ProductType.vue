<template>
   <div class="filter-col prodtypes-filter-wrap"> 
        <span v-for="type in product_types" v-bind:key="type.id">
            <input type="checkbox" :value="type.id" v-model="selectedProductTypes" v-on:change="updateValue($event.target.value)"> <span class="checkbox-label"> {{type.name}} </span> <br>
        </span>
    </div>
</template>
<script>
import api from "../../api";

export default {
    props: ['value'],
    data(){
        return {
            product_type: '',
            product_types: [],
            selectedProductTypes: []
        }
    },
    created() {
        api.getProductType( product_type => {
            if ( product_type ){
                this.product_types = product_type;
            }
        });
    },
    methods: {
        updateValue: function (value) {
            this.$emit('getSelectedPtypes', this.selectedProductTypes, 'product_type');
        }
    }
        // api.getPages(pages => {
        // commit(types.STORE_FETCHED_PAGES, { pages });
        // commit(types.PAGES_LOADED, true);
        // commit(types.INCREMENT_LOADING_PROGRESS);
        // });

}
</script>
