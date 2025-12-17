document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('recenter-btn');
    let map;

    function centrerMap() {
        //si une map existe déjà, on la supprime
        if (map) {
            map.remove();
        }

        //Par défaut on centre la carte sur Paris, avec un zoom de 13
        map = L.map('map').setView([48.8566, 2.3522], 13);

        //Ajout des tuiles (=le fond de carte visuel de la map)
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        //On tente de géolocaliser l'utilisateur. Si on y arrive, on centre la carte dessus
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition((position) => {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;
                map.setView([lat, lon], 13);

                const userMarker = L.circleMarker([lat, lon], {
                    radius: 8,
                    fillColor: "#f00020",
                    color: "#ffffff",
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 1
                }).addTo(map);
                userMarker.bindPopup("<b>Vous êtes ici !</b>").openPopup();
            }, () => {
                console.log("La géolocalisation a échoué");
            });
        }
        //Marqueur d'exemple ajouté manuellement
        const marker = L.marker([48.8566, 2.3522]).addTo(map);
        marker.bindPopup("<b>Salut !</b><br>Exemple point d'intérêt").openPopup();
    }

    //On ajoute un événement sur le bouton pour recentrer la carte sur l'utilisateur
    if (btn) {
        btn.addEventListener('click', () => {
            centrerMap();
        });
    }
    centrerMap();
});
