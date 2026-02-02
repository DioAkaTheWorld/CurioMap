<template>
  <div class="map-wrapper">
    <div ref="map" class="map"></div>
    <button class="recenter-btn" @click="recenter" title="Recentrer">📍</button>
  </div>
</template>

<script>
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

export default {
  name: 'MapView',
  data() {
    return {
      map: null
    }
  },
  mounted() {
    this.initMap()
  },
  beforeUnmount() {
    if (this.map) this.map.remove()
  },
  methods: {
    initMap() {
      if (this.map) this.map.remove()

      //Par défaut on centre la carte sur Paris
      this.map = L.map(this.$refs.map, {
        //minzoom pour que l'utilisateur ne dézoome pas trop
        minZoom: 3,
        maxBounds: [
          //maxbounds pour que l'utilisateur ne puisse pas sortir de la carte
          [-90, -180],
          [90, 180]
        ]
      }).setView([48.8566, 2.3522], 13)

      //Ajout des tuiles (=le fond de carte visuel de la map)
      L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        noWrap: true
      }).addTo(this.map)

      //On tente de géolocaliser l'utilisateur. Si on y arrive, on centre la carte dessus
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((position) => {
          const lat = position.coords.latitude
          const lon = position.coords.longitude
          this.map.setView([lat, lon], 13)

          L.circleMarker([lat, lon], {
            radius: 8,
            fillColor: "#f00020",
            color: "#ffffff",
            weight: 2,
            opacity: 1,
            fillOpacity: 1
          }).addTo(this.map).bindPopup("<b>Vous êtes ici !</b>").openPopup()
        }, () => {
          console.log("La géolocalisation a échoué")
        })
      }

      //Marqueur d'exemple ajouté manuellement
      L.circleMarker([48.8566, 2.3522], {
        radius: 6,
        fillColor: "#3388ff",
        color: "#fff",
        weight: 1,
        fillOpacity: 0.9
      }).addTo(this.map).bindPopup("<b>Salut !</b><br>Exemple point d'intérêt").openPopup()
    },
    recenter() {
      this.initMap()
    }
  }
}
</script>

<style scoped>
.map-wrapper {
  height: 100vh;
  width: 100%;
  position: relative;
  margin: 0;
  padding: 0;
}
.map {
  height: 100%;
  width: 100%;
}
.recenter-btn {
  position: absolute;
  z-index: 10000;
  top: 10px;
  right: 10px;
  background: white;
  border: none;
  padding: 8px;
  border-radius: 4px;
  cursor: pointer;
}
</style>
