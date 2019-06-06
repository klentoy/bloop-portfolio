<template>
  <div>
    <!-- <recent-posts-widget limit="5">Recent Posts</recent-posts-widget> -->
    <projects-widget limit="20">Projects</projects-widget>
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
