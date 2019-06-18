<template>
    <div>
        <!-- <select name="" id="" v-model="product_type">
            <option value="" v-for="type in product_types" v-bind:key="type.id">{{type.name}}</option>
        </select> -->

        <!-- <div v-for="(product_type,index) in product_types" :key="index" class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox"  v-model="product_type.checked" v-on:change="getfilteredData">
        <label class="form-check-label">
        {{ product_type.value }}
        </label>
        </div> -->
        <div>
            <span v-for="type in product_types" v-bind:key="type.id">
                <input type="checkbox" :value="type.id" v-model="selectedProductTypes" v-on:change="updateValue($event.target.value)"> <span class="checkbox-label"> {{type.name}} </span> <br>
            </span>
        </div>

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
            this.$emit('getSelected', this.selectedProductTypes);
        }
    }
        // api.getPages(pages => {
        // commit(types.STORE_FETCHED_PAGES, { pages });
        // commit(types.PAGES_LOADED, true);
        // commit(types.INCREMENT_LOADING_PROGRESS);
        // });

}
</script>
