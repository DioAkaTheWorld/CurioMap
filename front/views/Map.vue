<template>
  <div class="map-wrapper">
    <div ref="map" class="map"></div>
    <button class="recenter-btn" @click="recentrer" title="Recentrer">📍</button>

    <!-- Ajout de la modale -->
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
      userMarker:null,
      modaleOuverte: false,
      nouveauPoint: {
        titre: '',
        categorie: 2, // Par défaut 'Monument'
        description: '',
        latitude: 0,
        longitude: 0
      }
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

      //Utilisation de markRaw pour éviter que Vue rende l'objet Map réactif (ce qui cause des bugs avec Leaflet)
      this.map = markRaw(L.map(this.$refs.map, {
        minZoom: 3,
        maxBounds: [
          [-90, -180],
          [90, 180]
        ]
      })).setView([48.8566, 2.3522], 13)

      //Ajout des tuiles (=le fond de carte visuel de la map)
      L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        noWrap: true
      }).addTo(this.map)

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

      //Marqueur d'exemple ajouté manuellement
      L.circleMarker([48.8566, 2.3522], {
        radius: 6,
        fillColor: "#3388ff",
        color: "#fff",
        weight: 1,
        fillOpacity: 0.9
      }).addTo(this.map).bindPopup("<b>Salut !</b><br>Exemple point d'intérêt").openPopup()
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
          this.map.flyTo([lat, lon], 13);
          this.map.once('moveend', () =>{
            if(this.userMarker){
              this.userMarker.openPopup();
              this.userMarker.setStyle({ opacity: 1, fillOpacity: 1 });
            }
          })
        }, () => {
          //Si échec géolocalisation, on retourne sur Paris
          this.map.flyTo([latParis, lngParis], 13)
        })
      } else {
        this.map.flyTo([latParis, lngParis], 13)
      }
    },
    ouvrirModale(lat, lng) {
      this.nouveauPoint.latitude = lat;
      this.nouveauPoint.longitude = lng;
      this.modaleOuverte = true;
    },
    fermerModale() {
      this.modaleOuverte = false;
      this.nouveauPoint.titre = '';
      this.nouveauPoint.description = '';
      this.nouveauPoint.categorie = 2;
    },
    getCategoryColor(id) {
      switch(parseInt(id)) {
        case 1: return '#ff9800'; //Resto en orange
        case 2: return '#774d0e'; //Monument en brun
        case 3: return '#e91e63'; //Concert en rose
        case 4: return '#4caf50'; //Parc en vert
        case 5: return '#9c27b0'; //Musée en violet
        default: return '#3388ff'; //Par défaut en bleu
      }
    },
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

            //Couleur selon la catégorie
            const color = this.getCategoryColor(this.nouveauPoint.categorie);

            //Un point plutôt qu'un pin comme ça c'est joli avec la couleur
            L.circleMarker([
              parseFloat(this.nouveauPoint.latitude),
              parseFloat(this.nouveauPoint.longitude)
            ], {
              radius: 8,
              fillColor: color,
              color: "#fff", // Bordure blanche
              weight: 2,
              opacity: 1,
              fillOpacity: 1
            })
             .addTo(this.map)
             .bindPopup(`<b>${this.nouveauPoint.titre}</b><br>${this.nouveauPoint.description}`);

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
  background: rgb(72, 72, 72);
  border: none;
  padding: 8px;
  border-radius: 4px;
  cursor: pointer;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 20000; /* Plus haut que Leaflet */
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
</style>
