<template>
  <div class="widget recent-projects">
    <h3>
      <slot></slot>
    </h3>
    <ul v-if="recentProjectsLoaded">
      <li v-for="project in recentProjects(limit)" :key="project.id">
        <router-link :to="project.slug">{{ project.title.rendered }}</router-link>
      </li>
    </ul>
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
