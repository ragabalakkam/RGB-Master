<template>
  <div  class="google-map border" :style="`width: ${width}; height: ${height};`">
    <div class="h-100 w-100" id="map" v-if="!app.offline" />
    <div class="h-100 w-100 bg-light d-flex-center p-4 text-center font-xl" v-t="'mapsServiceNotAvailableWhileOffline'" v-else />
  </div>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  name: "b-map",
  props: {
    location: { required: true, type: Array },
    zoom: { default: 16 },
    width: { default: "100%" },
    height: { default: "25rem" },
  },
  computed: {
    ...mapGetters({
      app: "app",
    }),
  },
  methods: {
    init: function (latitude, longitude) {
      var latlng = new google.maps.LatLng(latitude, longitude);
      var map = new google.maps.Map(document.getElementById("map"), {
        center: latlng,
        zoom: this.zoom,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
      });
      var marker = new google.maps.Marker({
        position: latlng,
        map,
        title: "Set lat/lon values for this property",
        draggable: true,
      });
      google.maps.event.addListener(marker, "dragend", function (a) {
        var div = document.createElement("div");
        div.innerHTML = a.latLng.lat().toFixed(4) + ", " + a.latLng.lng().toFixed(4);
        document.getElementsByTagName("body")[0].appendChild(div);
      });
    },
  },
  mounted() {
    if (!this.app.offline) this.init(this.location[0], this.location[1]);
  },
};
</script>