<template>
  <div>
    
    <projects-widget limit="5">Projects</projects-widget>
  </div>
</template>

<script>
import RecentPostsWidget from "./widgets/RecentPosts.vue";
import PagesWidget from "./widgets/Pages.vue";
import ProjectsWidget from "./widgets/Projects.vue";

export default {
  components: {
    RecentPostsWidget,
    PagesWidget,
    ProjectsWidget 
  }
};
</script>
