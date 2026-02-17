<template>
  <div class="map-wrapper">
    <div ref="map" class="map"></div>
    <!-- Ajout du formulaire d'ajout de point -->
    <ModalePoint
      :isOpen="modaleOuverte"
      :latitude="nouveauPoint.latitude"
      :longitude="nouveauPoint.longitude"
      :categories="categories"
      @close="fermerModale"
      @submit="creerPoint"
    />
  </div>

  <div v-if="modaleAgendaOuverte" class="modal-overlay" @click.self="fermerModaleAgenda">
    <div class="modal-content">
      <span class="close-btn" @click="fermerModaleAgenda">&times;</span>
      <h2>Ajouter à l'agenda</h2>
      <p class="event-title">{{ pointSelectionne?.titre }}</p>

      <form @submit.prevent="confirmerAjoutAgenda">
        <div class="form-group">
          <label>Date de début</label>
          <input
              v-model="formulaireAgenda.dateDebut"
              type="datetime-local"
              required
              class="form-control"
              :min="getMinDate()"
              :max="getMaxDate()"
          />
        </div>

        <div class="form-group">
          <label>Date de fin</label>
          <input
              v-model="formulaireAgenda.dateFin"
              type="datetime-local"
              required
              class="form-control"
              :min="getMinDate()"
              :max="getMaxDate()"
          />
        </div>
        <div class="form-group">
          <label>Notes (optionnel)</label>
          <textarea v-model="formulaireAgenda.notes" class="form-control" rows="3"></textarea>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn-primary">Ajouter à l'agenda</button>
          <button type="button" @click="fermerModaleAgenda" class="btn btn-secondary">Annuler</button>
        </div>
      </form>

    </div>
  </div>

    <!-- Les fiiiiiiiltres -->
    <Filtre
      :categories="categories"
      v-model="selectedCategories"
      v-model:distance="maxDistance"
      v-model:dateDebut="filterDateStart"
      v-model:dateFin="filterDateEnd"
      :localisationActive="!!(filterCenter || userLocation)"
      @change="updateMarkers"
    />
</template>

<script>
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import { markRaw } from 'vue'
import Filtre from '../components/Filtre.vue'
import ModalePoint from '../components/ModalePoint.vue'
import {useAuthStore} from "../stores/auth"
import {useFavoritesStore} from "../stores/favorites"

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
  components: {
    Filtre,
    ModalePoint
  },
  computed: {
    authStore() {
      return useAuthStore()
    },
    favoritesStore() {
      return useFavoritesStore()
    }
  },
  data() {
    return {
      map: null,
      markerLayerGroup: null, //Groupe de calques pour les marqueurs
      userMarker:null,
      distanceCircle: null, //Cercle de portée autour du user
      userLocation: null, //Pour stocker les coos du user
      filterCenter: null, //Centre du filtre de distance
      maxDistance: 500,
      filterDateStart: '',
      filterDateEnd: '',
      modaleOuverte: false,
      points: [], //On charge tout les points au lancement
      categories: [],
      selectedCategories: [], //Tout coché par défaut
      nouveauPoint: {
        latitude: 0,
        longitude: 0,
      },
      modaleAgendaOuverte: false,
      pointSelectionne: null,
      formulaireAgenda: {
        dateDebut: '',
        dateFin: '',
        notes: ''
      }
    }
  },

  mounted() {
    this.initMap()
    this.fetchCategories()
    this.fetchPoints()
    if (this.authStore.isLoggedIn) {
      this.favoritesStore.fetchFavorites().then(() => {
        this.updateMarkers()
      })
    }
  },

  beforeUnmount() {
    if (this.map) this.map.remove()
  },

  methods: {
    //Chargement des catégories
    async fetchCategories() {
      try {
        let url = `${import.meta.env.VITE_API_URL}/categories`;
        if (this.authStore.isLoggedIn) {
          url += `?user_id=${this.authStore.user.id}`;
        }

        const response = await fetch(url, {
          headers: this.authStore.getAuthHeaders()
        });
        if (response.ok) {
          const data = await response.json();
          //Mapper libelle vers label pour compatibilité
          this.categories = data.map(cat => ({
            id: cat.id,
            label: cat.libelle,
            iduser: cat.iduser
          }));

          //Maj selectedCategories pour inclure toutes les catégories au 1er chargement
          if (this.selectedCategories.length === 0) {
             this.selectedCategories = this.categories.map(cat => cat.id);
          }
        }
      } catch (e) {
        console.error("Erreur chargement catégories", e);
      }
    },

    //Chargement des points au lancement
    async fetchPoints() {
      try {
        const response = await fetch(`${import.meta.env.VITE_API_URL}/points`, {
          headers: this.authStore.getAuthHeaders()
        });
        if (response.ok) {
          this.points = await response.json();
          this.updateMarkers();
        }
      } catch (e) {
        console.error("Erreur chargement points", e);
      }
    },

    //Maj de l'affichage des points
    updateMarkers(pointIdToOpen = null) {
      if (!this.markerLayerGroup) return;
        this.updateRadiusCircle();
        this.markerLayerGroup.clearLayers();

        this.points.forEach(point => {
        //Filtre par distance (si filtre centré défini et filtre activé < 500km)
        const center = this.filterCenter || this.userLocation;
        if (center && this.maxDistance < 500) {
          const centerLatLng = L.latLng(center.lat, center.lon);
          const pointLatLng = L.latLng(point.latitude, point.longitude);
          const distKm = centerLatLng.distanceTo(pointLatLng) / 1000;

          if (distKm > this.maxDistance) {
            return; //On passe au point suivant, celui-ci est trop loin
          }
        }

        //Filtre par date
        if (this.filterDateStart || this.filterDateEnd) {
            //Si le point n'a pas de date défini, on l'affiche quand même (car permanent)
            if (point.dateDebut && point.dateFin) {
                const pointStart = new Date(point.dateDebut).getTime();
                const pointEnd = new Date(point.dateFin).getTime();

                let filterStart = this.filterDateStart ? new Date(this.filterDateStart).getTime() : -8640000000000000; //Min date possible
                let filterEnd = this.filterDateEnd ? new Date(this.filterDateEnd).getTime() : 8640000000000000;   //Max date possible

                //On ajoute l'heure de fin de journée pour la date de fin du filtre (23:59:59)
                if (this.filterDateEnd) {
                    const d = new Date(this.filterDateEnd);
                    d.setHours(23, 59, 59, 999);
                    filterEnd = d.getTime();
                }

                //Verif de l'intersection des plages : si le point commence après la fin du filtre ou finit avant le début du filtre, on ne l'affiche pas
                if (!(filterStart <= pointEnd && pointStart <= filterEnd)) {
                    return;
                }
            }
        }

        //Vérif si la caté est sélectionnée
        if (this.selectedCategories.includes(parseInt(point.categorie))) {
          const color = this.getCategoryColor(point.categorie);
          const label = this.getCategoryLabel(point.categorie);
          const isUserLoggedIn = this.authStore.isLoggedIn;
          const isFavorite = isUserLoggedIn ? this.favoritesStore.isFavorite(point.id) : false;
          const heartIcon = isFavorite ? '❤️' : '🤍';

          //Contenu de la popup avec les infos du point
          let popupContent = `<b>${point.titre}</b><br><span style="color:${color}; font-weight:bold">${label}</span>`;
          if (point.description) {
            popupContent += `<br><i>${point.description}</i>`;
          }
          if (point.dateDebut) {
            popupContent += `<br>📅 <b>Date de debut :</b> ${this.formatDateEvent(point.dateDebut)}`;
          }
          if (point.dateFin) {
            popupContent += `<br>📅 <b>Date de fin :</b> ${this.formatDateEvent(point.dateFin)}`;
          }

          // Boutons d'action
          popupContent += `<div style="display: flex; gap: 10px; margin-top: 10px;">`;
          if (isUserLoggedIn) {
            popupContent += `<button class="btn-favorite" data-point-id="${point.id}" style="flex: 0 0 auto;">${heartIcon}</button>`;
          }
          popupContent += `<button class="btn-agenda" style="flex: 1;">Ajouter à mon agenda</button>`;
          popupContent += `</div>`;

          //Date de création
          if (point.date) {
            const dateCrea = new Date(point.date);
            const formattedCrea = dateCrea.toLocaleDateString('fr-FR');
            popupContent += `<br><small style="color:#666">Créé le ${formattedCrea}</small>`;
          }

          const marker = L.circleMarker([point.latitude, point.longitude], {
            radius: 8,
            fillColor: color,
            color: "#fff",
            weight: 2,
            opacity: 1,
            fillOpacity: 1
          });

          marker.bindPopup(popupContent);

          //Si on a cliqué sur ce point pour filtrer, on rouvre sa popup
          if (pointIdToOpen && point.id === pointIdToOpen) {
            setTimeout(() => marker.openPopup(), 100);
          }

          marker.on('popupopen', (e) => {
            const popupNode = e.popup._contentNode;
            const btnAgenda = popupNode.querySelector('.btn-agenda');
            const btnFavorite = popupNode.querySelector('.btn-favorite');

            if (btnAgenda) {
              btnAgenda.onclick = () => {
                this.ajouterEvenement(point);
              };
            }

            if (btnFavorite) {
              btnFavorite.onclick = async () => {
                await this.toggleFavorite(point.id);
                const newIsFavorite = this.favoritesStore.isFavorite(point.id);
                btnFavorite.textContent = newIsFavorite ? '❤️' : '🤍';
              };
            }
          });

          //Au clic, on définit ce point comme centre du filtre et on met à jour les dates
          marker.on('click', () => {
             let aEteModif = false;

             //Maj du centre (si différent)
             if (!this.filterCenter || this.filterCenter.lat !== point.latitude || this.filterCenter.lon !== point.longitude) {
                 this.filterCenter = {lat: point.latitude, lon: point.longitude};
                 aEteModif = true;
             }

             //Maj des dates si le point en a
             if (point.dateDebut && point.dateFin) {
                 //On prend les 10 premiers caractères (YYYY-MM-DD)
                 const pDateDebut = point.dateDebut.substring(0, 10);
                 const pDateFin = point.dateFin.substring(0, 10);

                 if (this.filterDateStart !== pDateDebut || this.filterDateEnd !== pDateFin) {
                     this.filterDateStart = pDateDebut;
                     this.filterDateEnd = pDateFin;
                     aEteModif = true;
                 }
             }

             if (aEteModif) {
                 this.updateMarkers(point.id);
             }
          });

          this.markerLayerGroup.addLayer(marker);
        }
      });
    },

    updateRadiusCircle() {
        if (this.distanceCircle) {
            this.map.removeLayer(this.distanceCircle);
            this.distanceCircle = null;
        }

        const center = this.filterCenter || this.userLocation;
        if (center && this.maxDistance < 500) {
            this.distanceCircle = L.circle([center.lat, center.lon], {
                radius: this.maxDistance * 1000, //km -> m
                color: '#3388ff',
                fillColor: '#3388ff',
                fillOpacity: 0.1,
                weight: 1
            }).addTo(this.map);
        }
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
        const {lat, lng} = e.latlng;

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
          console.log("Position trouvée:", position);
          const lat = position.coords.latitude
          const lon = position.coords.longitude

          this.userLocation = { lat, lon }; //On garde la pos en mémoire pour les filtres
          //Si pas encore de centre de filtre, on prend la position user
          if (!this.filterCenter) {
             this.filterCenter = { lat, lon };
          }

          this.updateMarkers(); //Maj pour afficher le cercle de portée

          this.map.setView([lat, lon], 13)
          if (this.userMarker) {
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
        }, (error) => {
          console.warn("La géolocalisation a échoué ou a été refusée", error);
        }, {
          enableHighAccuracy: true,
          timeout: 10000,
          maximumAge: 0
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

          this.userLocation = {lat, lon}; //Maj
          this.filterCenter = {lat, lon}; //On reset le filtre sur l'utilisateur

          if (this.userMarker) {
            this.userMarker.setStyle({opacity: 0, fillOpacity: 0});
            this.userMarker.closePopup()
          }
          this.map.once('moveend', () => {
            if (this.userMarker) {
              this.userMarker.openPopup();
              this.userMarker.setStyle({opacity: 1, fillOpacity: 1});
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
    },

    //Recup le libelle des catés
    getCategoryLabel(id) {
      const cat = this.categories.find(c => c.id === parseInt(id));
      return cat ? cat.label : 'Autre';
    },

    //Des jolies couleurs pour chaque caté
    getCategoryColor(id) {
      if (!id) return '#3388ff';
      const intId = parseInt(id);

      //Pour les caté de base
      switch (intId) {
        case 1: return '#ff9800'; //Resto en orange
        case 2: return '#774d0e'; //Monument en brun
        case 3: return '#ea5a90'; //Concert en rose
        case 4: return '#4caf50'; //Parc en vert
        case 5: return '#9c27b0'; //Musée en violet
        default:
          //Génération aléatoire basée sur l'id
          //id * golden_ratio modulo 0xFFFFFF
          const c = (intId * 137.508) % 360;
          return `hsl(${c}, 70%, 50%)`;
      }
    },

    //Formater la date pour l'affichage (cache l'heure si 00h00)
    formatDateEvent(dateStr) {
      if (!dateStr) return '';
      const date = new Date(dateStr);
      if (isNaN(date.getTime())) return dateStr;

      const dateOptions = { day: '2-digit', month: '2-digit', year: 'numeric' };
      const datePart = date.toLocaleDateString('fr-FR', dateOptions);

      //Si l'heure est 00h00 (= pas d'heure définie par l'user), on n'affiche que la date
      if (date.getHours() === 0 && date.getMinutes() === 0) {
        return datePart;
      }

      const timeOptions = { hour: '2-digit', minute: '2-digit' };
      const timePart = date.toLocaleTimeString('fr-FR', timeOptions);
      return `${datePart} à ${timePart}`;
    },

    //Créer un point
    async creerPoint(payload) {
      try {
        //Si nouvelle catégorie, on la crée d'abord
        if (payload.newCategorie) {
            const catResponse = await fetch(`${import.meta.env.VITE_API_URL}/categories`, {
                method: 'POST',
                headers: this.authStore.getAuthHeaders(),
                body: JSON.stringify({
                    libelle: payload.newCategorie,
                    iduser: this.authStore.user?.id
                })
            });

            if (catResponse.ok) {
                const catData = await catResponse.json();
                //Ajout de la nouvelle caté à la liste locale
                const newCat = {
                    id: catData.id,
                    label: catData.libelle,
                    iduser: catData.iduser
                };
                this.categories.push(newCat);
                //On l'ajoute aux filtres pour voir le point
                this.selectedCategories.push(newCat.id);
                //On assigne l'id de la nouvelle caté au point
                payload.categorie = newCat.id;
            } else {
                const err = await catResponse.json();
                alert('Erreur création catégorie: ' + (err.error || 'Problème serveur'));
                return;
            }
        }

        //'http://localhost:8888/api/points'
        const response = await fetch(`${import.meta.env.VITE_API_URL}/points`, {
          method: 'POST',
          headers: this.authStore.getAuthHeaders(),
          body: JSON.stringify({
             ...payload,
             iduser: this.authStore.user?.id //Ajout de l'ID utilisateur
          })
        });

        if (response.ok) {
          alert('Point créé avec succès !');

          //Ajout du point à la liste locale
          //Pour l'affichage immédiat, on s'assure que les types sont bons
          const nouveauPoint = {
            ...payload,
            id: (await response.json()).id || Date.now(), //On essaie de recup l'id sinon temp
            latitude: parseFloat(payload.latitude),
            longitude: parseFloat(payload.longitude)
          };

          this.points.push(nouveauPoint);

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
    },

    ajouterEvenement(point) {
      this.pointSelectionne = point;

      //On préremplit avec les dates du point si elles sont dispo
      if (point.dateDebut && point.dateFin) {
        this.formulaireAgenda.dateDebut = point.dateDebut.replace(' ', 'T').slice(0, 16);
        this.formulaireAgenda.dateFin = point.dateFin.replace(' ', 'T').slice(0, 16);
      }

      this.modaleAgendaOuverte = true;
    },

    fermerModaleAgenda() {
      this.modaleAgendaOuverte = false;
      this.formulaireAgenda = { dateDebut: '', dateFin: '', notes: '' };
    },

    async confirmerAjoutAgenda() {
      try {
        const payload = {
          iduser: this.authStore.user?.id,
          idpoint: this.pointSelectionne.id,
          titre_evenement: this.pointSelectionne.titre,
          dateDebut: this.formulaireAgenda.dateDebut.replace('T', ' ') + ':00',
          dateFin: this.formulaireAgenda.dateFin.replace('T', ' ') + ':00',
          notes: this.formulaireAgenda.notes
        };

        const response = await fetch(`${import.meta.env.VITE_API_URL}/agenda`, {
          method: 'POST',
          headers: this.authStore.getAuthHeaders(),
          body: JSON.stringify(payload)
        });

        if (response.ok) {
          alert('Événement ajouté à l\'agenda !');
          this.fermerModaleAgenda();
          this.$router.push('/agenda')
        } else {
          alert('Erreur lors de l\'ajout');
        }
      } catch (error) {
        console.error(error);
        alert('Erreur de connexion');
      }
    },
    getMinDate(){
      return this.pointSelectionne.dateDebut ?? null;
    },

    getMaxDate(){
      return this.pointSelectionne.dateFin ?? null;
    },

    async toggleFavorite(pointId) {
      const success = await this.favoritesStore.toggleFavorite(pointId);
      if (!success) {
        alert('Erreur lors de la mise à jour du favori');
      } else {
        this.updateMarkers(pointId);
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
:deep(.btn-agenda) {
  margin-top: 10px;
  padding: 8px 16px;
  background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  font-size: 14px;
  transition: all 0.3s ease;
  box-shadow: 0 2px 4px rgba(99, 102, 241, 0.2);
  width: 100%;
}

:deep(.btn-agenda:hover) {
  background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(99, 102, 241, 0.3);
}

:deep(.btn-agenda:active) {
  transform: translateY(0);
  box-shadow: 0 2px 4px rgba(99, 102, 241, 0.2);
}

:deep(.btn-favorite) {
  padding: 8px 12px;
  background: white;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  cursor: pointer;
  font-size: 20px;
  line-height: 1;
  transition: all 0.3s ease;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

:deep(.btn-favorite:hover) {
  transform: scale(1.1);
  border-color: #ff6b9d;
  box-shadow: 0 4px 8px rgba(255, 107, 157, 0.3);
}

:deep(.btn-favorite:active) {
  transform: scale(0.95);
}

.options-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    background-color: #f9f9f9;
    padding: 10px;
    border-radius: 4px;
    margin-bottom: 10px;
    border: 1px solid #eee;
}
.options-header h2 {
    margin: 0;
    font-size: 1.1rem;
    color: #555;
}
.options-header span {
    transition: transform 0.3s ease;
    font-size: 0.8rem;
    color: #777;
}
.options-header span.rotated {
    transform: rotate(180deg);
}
.options-content {
    background-color: #fafafa;
    border-radius: 4px;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #eee;
}
</style>
