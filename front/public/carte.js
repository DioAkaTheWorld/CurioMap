//Par défaut on centre la carte sur Paris, avec un zoom de 13
const map = L.map('map').setView([48.8566, 2.3522], 13);

//Ajout des tuiles (=le fond de carte visuel de la map)
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

//Si on peut localiser l'utilisateur, on récupère sa position et on centre la carte dessus
if(navigator.geolocation) {
    navigator.geolocation.getCurrentPosition((position) => {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;
        map.setView([lat, lon], 13);

        //Ajouter un marqueur à la position de l'utilisateur
        const userMarker = L.marker([lat, lon]).addTo(map);
        userMarker.bindPopup("<b>Vous êtes ici !</b>").openPopup();

    }, () => {
        console.log("La géolocalisation a échoué");
    });
}

//Ex d'ajout de marqueur manuellement pour voir si ça marche
const marker = L.marker([48.8566, 2.3522]).addTo(map);
marker.bindPopup("<b>Salut !</b><br>Exemple point d'intérêt").openPopup();