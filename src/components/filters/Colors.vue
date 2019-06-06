<template>
    <div>
        <select name="" id="" v-model="color">
            <option value="" v-for="color in colors" v-bind:key="color.id">{{color.name}}</option>
        </select>
    </div>
</template>
<script>
import api from "../../api";

export default {
    data(){
        return {
            color: '',
            colors: []
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
    }
}
</script>
