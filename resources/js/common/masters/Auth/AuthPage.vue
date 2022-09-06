<template>
  <b-gradient
    class="auth-page vh-100 d-flex-center"
    from="#FFFDE4"
    to="#005AA7"
    dir="bottom left"
  >
    <router-view
      :key="$route.fullPath"
      class="text-center"
      :class="wXS ? 'min-vw-100 min-vh-100' : 'rounded-xl'"
      @proceeded="smartRedirect('/redirect')"
    ></router-view>
  </b-gradient>
</template>

<script>
export default {
  name: "auth-page",
};
</script>