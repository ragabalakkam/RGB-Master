<template>
  <div @click="getLocation">
    <slot />
  </div>
</template>

<script>
export default {
  name: "location-input",
  data() {
    return {
      error: null,
      fullLocation: null,
    };
  },
  methods: {
    getLocation() {
      if (navigator.geolocation)
        navigator.geolocation.getCurrentPosition(
          this.setPosition,
          this.showError
        );
      else console.error("Geolocation is not supported by this browser");
    },
    setPosition(position) {
      console.log(position);
      this.$emit('location', [position.coords.latitude, position.coords.longitude]);
    },
    showError(error) {
      this.location = [];
      let msg = null;
      switch (error.code) {
        case error.PERMISSION_DENIED:
          msg = "User denied the request for Geolocation.";
          break;
        case error.POSITION_UNAVAILABLE:
          msg = "Location information is unavailable.";
          break;
        case error.TIMEOUT:
          msg = "The request to get user location timed out.";
          break;
        case error.UNKNOWN_ERROR:
          msg = "An unknown error occurred.";
          break;
      }
      console.error(msg);
    },
  },
};
</script>