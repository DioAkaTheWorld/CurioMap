<template>
  <div class="map-wrapper">
    <div ref="map" class="map"></div>
    <!-- Ajout du formulaire d'ajout de point -->
    <div v-if="modaleOuverte" class="modal-overlay" @click.self="fermerModale">
      <div class="modal-content">
        <span class="close-btn" @click="fermerModale">&times;</span>
        <h2>Ajouter un point</h2>
        <form @submit.prevent="creerPoint">
          <div class="form-group">
            <label>Titre</label>
            <input v-model="nouveauPoint.titre" type="text" required class="form-control" />
          </div>

          <div class="form-group">
            <label>Catégorie</label>
            <select v-model="nouveauPoint.categorie" class="form-control">
              <option value="1">Restaurant</option>
              <option value="2">Monument</option>
              <option value="3">Concert</option>
              <option value="4">Parc</option>
              <option value="5">Musée</option>
            </select>
          </div>

          <div class="form-group">
            <label>Description</label>
            <textarea v-model="nouveauPoint.description" class="form-control" rows="3"></textarea>
          </div>

          <div class="coords-row">
            <div class="form-group">
              <label>Latitude</label>
              <input v-model="nouveauPoint.latitude" type="text" readonly class="form-control readonly" />
            </div>
            <div class="form-group">
              <label>Longitude</label>
              <input v-model="nouveauPoint.longitude" type="text" readonly class="form-control readonly" />
            </div>
          </div>

           <div class="form-actions">
            <button type="submit" class="btn btn-primary">Créer</button>
            <button type="button" @click="fermerModale" class="btn btn-secondary">Annuler</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Les fiiiiiiiltres -->
    <div class="filter-panel">
      <h3>Filtres</h3>
      <div class="filter-group">
        <h4>Catégories</h4>
        <div v-for="cat in categories" :key="cat.id" class="checkbox-item">
            <input type="checkbox" :id="'cat-'+cat.id" :value="cat.id" v-model="selectedCategories" @change="updateMarkers">
            <label :for="'cat-'+cat.id" :style="{color: getCategoryColor(cat.id)}">{{ cat.label }}</label>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import { markRaw } from 'vue'

//Correctif pour les icônes Leaflet avec Vite
import icon from 'leaflet/dist/images/marker-icon.png'
import iconShadow from 'leaflet/dist/images/marker-shadow.png'

//Reset de la config par défaut des icônes
delete L.Icon.Default.prototype._getIconUrl;

L.Icon.Default.mergeOptions({
    iconRetinaUrl: icon,
    iconUrl: icon,
    shadowUrl: iconShadow
});

export default {
  name: 'MapView',
  data() {
    return {
      map: null,
      markerLayerGroup: null, //Groupe de calques pour les marqueurs
      userMarker:null,
      modaleOuverte: false,
      points: [], //On charge tout les points au lancement
      categories: [
          { id: 1, label: 'Restaurant' },
          { id: 2, label: 'Monument' },
          { id: 3, label: 'Concert' },
          { id: 4, label: 'Parc' },
          { id: 5, label: 'Musée' }
      ],
      selectedCategories: [1, 2, 3, 4, 5], //Tout coché par défaut
      nouveauPoint: {
        titre: '',
        categorie: 1,
        description: '',
        latitude: 0,
        longitude: 0
      }
    }
  },
  mounted() {
    this.initMap()
    this.fetchPoints()
  },
  beforeUnmount() {
    if (this.map) this.map.remove()
  },
  methods: {
    //Chargement des points au lancement
    async fetchPoints() {
        try {
            const response = await fetch('http://localhost:8888/api/points');
            if (response.ok) {
                this.points = await response.json();
                this.updateMarkers();
            }
        } catch (e) {
            console.error("Erreur chargement points", e);
        }
    },

    //Maj de l'affichage des points
    updateMarkers() {
        if (!this.markerLayerGroup) return;

        this.markerLayerGroup.clearLayers();

        this.points.forEach(point => {
            //Vérif si la caté est sélectionnée
            if (this.selectedCategories.includes(parseInt(point.categorie))) {
                const color = this.getCategoryColor(point.categorie);
                const label = this.getCategoryLabel(point.categorie);

                const marker = L.circleMarker([point.latitude, point.longitude], {
                    radius: 8,
                    fillColor: color,
                    color: "#fff",
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 1
                });

                marker.bindPopup(`<b>${point.titre}</b><br><span style="color:${color}; font-weight:bold">${label}</span><br>${point.description || ''}`);

                this.markerLayerGroup.addLayer(marker);
            }
        });
    },

    //Initialisation de la map
    initMap() {
      if (this.map) this.map.remove()

      //Utilisation de markRaw pour éviter que Vue rende l'objet Map réactif (qui cause des bugs avec Leaflet)
      this.map = markRaw(L.map(this.$refs.map, {
        minZoom: 3,
        maxBounds: [
          [-90, -180],
          [90, 180]
        ],
        zoomControl: false
      })).setView([48.8566, 2.3522], 13)

      //Ajout des tuiles (=le fond de carte visuel de la map)
      L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        noWrap: true
      }).addTo(this.map)

      //LayerGroup pour les marqueurs filtrables
      this.markerLayerGroup = L.layerGroup().addTo(this.map);

      //Gestion du clic pour ajouter un point
      this.map.on('click', (e) => {
        const { lat, lng } = e.latlng;

        const div = document.createElement('div');
        div.innerHTML = `
           <div style="text-align: center;">
             <p style="margin:5px 0;">Coords: ${lat.toFixed(4)}, ${lng.toFixed(4)}</p>
             <button id="popup-add-btn" style="cursor: pointer; padding: 5px 10px;">Ajouter un point ici</button>
           </div>
        `;

        //Écouteur sur le bouton de la popup
        const btn = div.querySelector('#popup-add-btn');
        btn.onclick = () => {
           this.ouvrirModale(lat, lng);
           this.map.closePopup();
        };

        L.popup()
         .setLatLng(e.latlng)
         .setContent(div)
         .openOn(this.map);
      });

      //On tente de géolocaliser l'utilisateur. Si on y arrive, on centre la carte dessus
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((position) => {
          const lat = position.coords.latitude
          const lon = position.coords.longitude
          this.map.setView([lat, lon], 13)
          if(this.userMarker){
            this.userMarker.remove()
            this.userMarker = null
          }

          this.userMarker = L.circleMarker([lat, lon], {
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
    },

    //Recentrer sur la position de l'utilisateur
    recentrer() {
      const latParis = 48.8566;
      const lngParis = 2.3522;

      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((position) => {
          const lat = position.coords.latitude
          const lon = position.coords.longitude
          if(this.userMarker){
            this.userMarker.setStyle({ opacity: 0, fillOpacity: 0 });
            this.userMarker.closePopup()
          }
          this.map.once('moveend', () =>{
            if(this.userMarker){
              this.userMarker.openPopup();
              this.userMarker.setStyle({ opacity: 1, fillOpacity: 1 });
            }
          })
          this.map.flyTo([lat, lon], 13);
        }, () => {
          //Si échec géolocalisation, on retourne sur Paris
          this.map.flyTo([latParis, lngParis], 13)
        })
      } else {
        this.map.flyTo([latParis, lngParis], 13)
      }
    },

    //Pour ouvrir le formulaire de création de point
    ouvrirModale(lat, lng) {
      this.nouveauPoint.latitude = lat;
      this.nouveauPoint.longitude = lng;
      this.modaleOuverte = true;
    },

    //Pour le fermer
    fermerModale() {
      this.modaleOuverte = false;
      this.nouveauPoint.titre = '';
      this.nouveauPoint.description = '';
      this.nouveauPoint.categorie = 2;
    },

    //Recup le libelle des catés
    getCategoryLabel(id) {
      switch(parseInt(id)) {
        case 1: return 'Restaurant';
        case 2: return 'Monument';
        case 3: return 'Concert';
        case 4: return 'Parc';
        case 5: return 'Musée';
        default: return 'Autre';
      }
    },

    //Des jolies couleurs pour chaque caté
    getCategoryColor(id) {
      switch(parseInt(id)) {
        case 1: return '#ff9800'; //Resto en orange
        case 2: return '#774d0e'; //Monument en brun
        case 3: return '#ea5a90'; //Concert en rose
        case 4: return '#4caf50'; //Parc en vert
        case 5: return '#9c27b0'; //Musée en violet
        default: return '#3388ff'; //Par défaut en bleu
      }
    },

    //Créer un point
    async creerPoint() {
      try {
        const payload = {
            ...this.nouveauPoint,
            categorie: parseInt(this.nouveauPoint.categorie)
        };

        const response = await fetch('http://localhost:8888/api/points', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        if (response.ok) {
            alert('Point créé avec succès !');

            //Ajout du point à la liste locale
            this.points.push({
                ...payload,
                latitude: parseFloat(payload.latitude),
                longitude: parseFloat(payload.longitude)
            });

            //Maj des marqueurs (si filtre)
            this.updateMarkers();

            this.fermerModale();
        } else {
            const err = await response.json().catch(() => ({}));
            alert('Erreur: ' + (err.error || 'Problème serveur'));
        }
      } catch (error) {
        console.error(error);
        alert('Impossible de contacter le serveur.');
      }
    }
  },
  expose: ['recentrer']
}
</script>

<style scoped>
.map-wrapper {
  height:100vh;
  width: 100%;
  position: fixed;
  top: 60px;
  overflow: hidden;
  left:0;
}
.map {
  height: 100%;
  width: 100%;
}

.modal-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 20000; /*pour que le formulaire apparaisse au dessus de leaflet */
}
.modal-content {
  background: white;
  padding: 20px;
  border-radius: 8px;
  width: 90%;
  max-width: 500px;
  position: relative;
}
.close-btn {
  position: absolute;
  top: 10px;
  right: 10px;
  font-size: 24px;
  cursor: pointer;
}
.form-group {
  margin-bottom: 15px;
}
.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}
.form-control {
  width: 100%;
  padding: 8px;
  box-sizing: border-box;
  border: 1px solid #ddd;
  border-radius: 4px;
}
.form-control.readonly {
  background: #eee;
}
.coords-row {
  display: flex;
  gap: 15px;
}
.coords-row .form-group {
  flex: 1;
}
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}
.btn {
  padding: 8px 15px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
.btn-primary {
  background: #3388ff;
  color: white;
}
.btn-secondary {
  background: #ccc;
  color: black;
}

/* Filtres */
.filter-panel {
    position: absolute;
    top: 50%;
    left: 10px;
    transform: translateY(-50%);
    background: white;
    padding: 10px;
    border-radius: 4px;
    border: 2px solid rgba(0,0,0,0.2);
    z-index: 1000;
    width: 150px;
    box-shadow: 0 1px 5px rgba(0,0,0,0.4);
}

.filter-panel h3 {
    margin: 0 0 10px 0;
    font-size: 1rem;
    text-align: center;
    border-bottom: 1px solid #eee;
    padding-bottom: 5px;
}

.filter-group h4 {
    margin: 0 0 5px 0;
    font-size: 0.9rem;
    color: #555;
}

.checkbox-item {
    display: flex;
    align-items: center;
    margin-bottom: 5px;
}

.checkbox-item input {
    margin-right: 8px;
    cursor: pointer;
}

.checkbox-item label {
    font-size: 0.85rem;
    cursor: pointer;
    font-weight: bold;
}
</style>
