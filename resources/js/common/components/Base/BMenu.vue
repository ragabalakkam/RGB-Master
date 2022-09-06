<template>
  <div class="dropdown-menu py-0 border-0 position-absolute rounded-lg shadow">
    <div class="d-flex flex-column h-100">
      <!-- header -->
      <header v-if="$slots['header']" class="bg-primary text-light rounded-top">
        <slot name="header" />
      </header>

      <!-- main -->
      <main class="flex-1" style="overflow-y:scroll" v-if="$slots['body']">
        <slot name="body" />
      </main>

      <!-- footer -->
      <footer v-if="$slots['footer']" class="rounded-bottom">
        <slot name="footer" />
      </footer>
    </div>
  </div>
</template>

<script>
export default {
  name: "b-menu",
};
</script>

<style lang="scss" scoped>
.dropdown-menu {
  transform: none !important;
  top: 2.5rem !important;
}

html[dir="ltr"] .dropdown-menu {
  left: unset !important;
  right: 0 !important;
}

html[dir="rtl"] .dropdown-menu {
  right: unset !important;
  left: 0 !important;
}
</style>