<template>
  <transition class="fade">
    <b-gradient
      
      class="loading position-fixed d-flex-center min-vh-100"
      from="var(--loading-from)"
      to="var(--loading-to)"
    >
      <div class="position-relative sz-180 box d-flex-center" />
      <div class="position-absolute position-center text-center">
        <!-- <b-img size="150" :src="`/storage/${app.logo}`" class="mb-3" v-if="app" /> -->
        <pulse-loader size="1.15rem" color="var(--loader-color)" />
      </div>
    </b-gradient>
  </transition>
</template>

<script>
export default {
  name: 'loading',
}
</script>

<style lang="scss" scoped>
$color-background: var(--loading-from);
$color-fill: var(--loader-to);

html {
  font-size: 48px;
}

body {
  margin: 0;
  min-height: 100vh;
  display: grid;
  place-items: center;
  background: $color-background;
}

.box {
  position: relative;
  background: $color-fill;
  box-sizing: border-box;
  height: 100vh;
  width: 100vw;
  outline: 0;
  overflow: hidden;

  &::before {
    content: "";
    font-family: Arial;
    height: 100%;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  &::before {
    content: "";
    position: absolute;
    bottom: -50%;
    left: -50%;
    height: 200%;
    width: 200%;
    box-sizing: border-box;
    background-color: $color-background;
    border-radius: 40%;
    opacity: 0.5;
    animation: spin 4s linear infinite reverse;
  }

  &::after {
    content: "";
    position: absolute;
    bottom: -50%;
    left: -50%;
    height: 200%;
    width: 200%;
    box-sizing: border-box;
    background-color: $color-background;
    border-radius: 40%;
    animation: spin 4s linear infinite;
  }
}

@keyframes spin {
  0% {
    transform: translateY(0) rotate(0deg);
  }
  100% {
    transform: translateY(-100%) rotate(360deg);
  }
}

</style>