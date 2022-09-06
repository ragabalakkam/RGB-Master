<template>
  <div class="bg-gray-3 d-flex-center rounded-lg border position-relative" v-if="shown">
    {{ $t('adArea') }}
    <b-button class="position-absolute position-top-right m-2 text-white border-0 bg-all-none" @click="shown = false">
      <b-i icon="times" />
    </b-button>
  </div>
</template>

<script>
export default {
  name: 'ad-area',
  data() {
    return {
      shown: true,
    };
  },
}
</script>