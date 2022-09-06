<template>
  <b-form-group class="position-static" :label="$t('ال') + $t('coords')">
    <div v-if="!visitedGoogleMaps" class="d-flex flex-gap-2 flex-column" >
      <div class="flex-1">
        <b-labeled-input v-model="location[0]" :label="$t('latitude')" name="latitude" :showLabel="false" class="mb-0" :label-class="{ 'wd-90' : wXS}" />
      </div>

      <div class="flex-1 d-flex flex-gap-2">
        <b-labeled-input v-model="location[1]" :label="$t('longitude')" name="longitude" :showLabel="false" class="mb-0" :label-class="{ 'wd-90' : wXS}" />

        <b-button :variant="variant" :class="{ border: variant == 'light' }" :title="$t('getCurrentCoords')" @click="getLocation">
          <b-i icon="map-marked-alt" />
        </b-button>
      </div>
    </div>

    <div class="position-relative" v-else>
      <b-input
        name="location"
        :showLabel="false"
        :placeholder="$t('pasteXHere', { attr: $t('ال') + $t('coords') }) + ' ..'"
        class="bg-light mb-0"
        v-model="fullLocation"
      />

      <b-button variant="light" class="bg-all-none border-0 py-2 px-3 position-absolute position-right" @click="visitedGoogleMaps = false">
        <b-i icon="times" />
      </b-button>
    </div>

    <div v-if="!visitedGoogleMaps" class="mt-1 d-flex align-items-center">
      <a
        class="text-info text-hover-primary"
        href="https://www.google.com/maps"
        target="_blank"
        @click="visitedGoogleMaps = true"
      >
        <b-i icon="map" />
        <span v-text="$t('toX', { attr: $t('googleMaps') })" class="pl-1 pr-2" />
      </a>

      <b-button variant="light" class="bg-all-none border-0 p-0 text-info-7 text-hover-info" @click.stop="showGuide = true">
        <b-i icon="question-circle" />
      </b-button>
    </div>

    <div
      v-if="showGuide"
      class="position-absolute position-top-left min-vh-100 vw-100 d-flex-center bg-black-9 bd-blur-3 index-up px-3 c-ptr"
      @click="showGuide = false"
    >
      <img class="rounded-lg" :class="wXS ? 'w-100' : 'w-75'" src="/imgs/instructions/google-maps.gif" @click.stop />
    </div>
  </b-form-group>
</template>

<script>
export default {
  name: "location-input",
  props: {
    value       : { required: true },
    variant     : { default: "light" },
    flexColumn  : { default: false },
    gap         : { default: null },
  },
  data() {
    return {
      error: null,
      visitedGoogleMaps: false,
      fullLocation: null,
      showGuide: false,
    };
  },
  // created() {
  //   var latlng = new google.maps.LatLng(30.802498000000003, 26.820553);
  //   console.log(new google.maps.Geocoder().geocode({ location : latlng }));
  //   axios.get("http://maps.googleapis.com/maps/api/geocode/json?latlng=" + [30.802498000000003, 26.820553].join(',') + "&sensor=true").then(({ data }) => console.log({ data }));
  // },
  computed: {
    location: {
      set(val) {
        this.$emit("input", val);
      },
      get() {
        return this.value || [null, null];
      },
    },
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
      this.location = [position.coords.latitude, position.coords.longitude];
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
  watch: {
    fullLocation: function (newVal) {
      this.location = newVal ? newVal.trim().split(",") : [];
      this.visitedGoogleMaps = false;
    },
  },
};
</script>

<style lang="scss" scoped>
.index-up {
  z-index: 9999;
}
</style>