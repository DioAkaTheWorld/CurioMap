//Pour le moment on centre juste la carte sur Paris : 48.8566, 2.3522 avec un zoom de 13
var map = L.map('map').setView([48.8566, 2.3522], 13);

//Ajout des tuiles (=le fond de carte visuel de la map)
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

//Ex d'ajout de marqueur manuellement pour voir si ça marche
var marker = L.marker([48.8566, 2.3522]).addTo(map);
marker.bindPopup("<b>Salut !</b><br>Exemple point d'intérêt").openPopup();