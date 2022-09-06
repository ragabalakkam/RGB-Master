<template>
  <div class="default">
    <default-master>
      <router-view :key="$route.fullPath">
    </default-master>
  </div>
</template>

<script>
import DefaultMaster from "../../common/masters/Default/DefaultMaster";
export default {
  name: "master",
  components: {
    DefaultMaster,
  },
};
</script>