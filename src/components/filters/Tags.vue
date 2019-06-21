<template>
    <div class="tags-filter-wrap">
        <span v-for="tag in tags" v-bind:key="tag.id">
            <input type="checkbox" :value="tag.id" v-model="selectedTags" v-on:change="updateValue($event.target.value)"> <span class="checkbox-label"> {{tag.name}} </span> <br>
        </span>
    </div>
</template>
<script>
import api from "../../api";

export default {
    data(){
        return {
            tag: '',
            tags: [],
            selectedTags: []
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
    },

    methods: {
        updateValue: function (value) {
            this.$emit('getSelectedTags', this.selectedTags);
        }
    }
}
</script>
