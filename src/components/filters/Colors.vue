<template>
    <div class="filter-col colors-filter-wrap"> 
        <span v-for="color in colors" v-bind:key="color.id">
            <input type="checkbox" :value="color.id" v-model="selectedColors" v-on:change="updateValue($event.target.value)"> <span class="checkbox-label"> {{color.name}} </span> <br>
        </span>
    </div>
</template>
<script>
import api from "../../api";

export default {
    data(){
        return {
            color: '',
            colors: [],
            selectedColors: []
        }
    },
    created() {
        api.getColors( color => {
            if ( color ){
                this.colors = color;
            }
        });

        // api.getPages(pages => {
        // commit(types.STORE_FETCHED_PAGES, { pages });
        // commit(types.PAGES_LOADED, true);
        // commit(types.INCREMENT_LOADING_PROGRESS);
        // });
    },
    methods: {
        updateValue: function (value) {
            this.$emit('getSelectedColors', this.selectedColors);
        }
    }   
}
</script>
