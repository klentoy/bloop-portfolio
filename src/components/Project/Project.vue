<template>
  <div class="bv-example-row pt-4">
    <template v-if="project">
      <h1>{{ project.title.rendered }}</h1>
      
      <!-- <div v-html="project.content.rendered"></div> -->
    </template>
    <Loader v-else/>
  </div>
</template>

<script>
import axios from "axios";
import Loader from "../partials/Loader.vue";
import { mapGetters } from "vuex";
import SETTINGS from "../../settings";

export default {
  
  data() {
    return {
      project: false
    };
  },

  computed: {},

  beforeMount() {
    this.getProject();
  },

  methods: {
    getProject: function() {
      axios
        .get(
          SETTINGS.API_BASE_PATH + "portfolios?slug=" + this.$route.params.projectSlug
        )
        .then(response => {
          this.project = response.data[0];
        })
        .catch(e => {
          console.log(e);
        });
    }
  },

  components: {
    Loader
  }
};
</script>
