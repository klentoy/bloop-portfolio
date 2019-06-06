<template>
    <div>
        <select name="" id="" v-model="tag">
            <option value="" v-for="tag in tags" v-bind:key="tag.id">{{tag.name}}</option>
        </select>
    </div>
</template>
<script>
import api from "../../api";

export default {
    data(){
        return {
            tag: '',
            tags: []
        }
    },
    created() {
        api.getTags( tag => {
            if ( tag ){
                this.tags = tag;
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
