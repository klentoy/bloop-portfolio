<template>
  <div class="widget recent-projects">
    <h3>
      <slot></slot>
    </h3>
    
    <div id="projects-wrapper" v-if="recentProjectsLoaded">
      <div class="project-item" v-for="project in recentProjects(limit)" :key="project.id">
        <router-link :to="project.slug">
          
          <img :src="project.acf.project_desktop_view">

          <div class="project-info">
            <p>{{ project.title.rendered }}</p>

            <!-- TODO: getCategories() -->
            <p>Categories: {{ project.categories }}</p>

            <!-- TODO: getColors() -->
            <p>Site Colors: {{ project.portfolio_colors }}</p>
          </div>
        </router-link>
      </div>
    </div>

    <div v-else>Loading...</div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";

export default {
  props: ["limit"],
  computed: {
    ...mapGetters({
      recentProjects: "recentProjects",
      recentProjectsLoaded: "recentProjectsLoaded"
    })
  },

  mounted() {
    this.$store.dispatch("getProjects", { limit: this.limit });
  }
};
</script>

<style scoped>
  #projects-wrapper {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
  }

  .project-item {
    background: #eaeaea;
    width: 32%;
    margin-bottom: 10px;
    box-sizing: border-box;
  }

  .project-info {
    padding: 0 30px;
  }

  .project-item a {
    color: #212121;
    text-decoration: none;
  }

  .project-item img {
    width: 100%;
  }

</style>