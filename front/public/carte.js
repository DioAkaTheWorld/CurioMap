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

        // Gestion du clic sur la carte pour ajouter un point
        map.on('click', (e) => {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;

            const popupContent = `
                <div style="text-align: center;">
                    <p style="margin: 5px 0;">Coords: ${lat.toFixed(4)}, ${lng.toFixed(4)}</p>
                    <button id="btn-open-add-point" style="cursor: pointer; padding: 5px 10px;">Ajouter un point ici</button>
                </div>
            `;

            const popup = L.popup()
                .setLatLng(e.latlng)
                .setContent(popupContent)
                .openOn(map);
        });

        // Délégation d'événement pour le bouton dans la popup (car il est créé dynamiquement)
        document.addEventListener('click', (e) => {
            if(e.target && e.target.id === 'btn-open-add-point'){
                // On récupère les coords depuis la popup ouverte ou on les stocke avant
                // Le plus simple est de récupérer la position du popup ouvert
                const popup = map._popup;
                if(popup){
                    const latlng = popup.getLatLng();
                    openModal(latlng.lat, latlng.lng);
                    map.closePopup();
                }
            }
        });
    }

    //On ajoute un événement sur le bouton pour recentrer la carte sur l'utilisateur
    if (btn) {
        btn.addEventListener('click', () => {
            centrerMap();
        });
    }
    centrerMap();

    // --- Logique de la Modale ---
    const modal = document.getElementById('modal-add-point');
    const closeBtn = document.querySelector('.close-btn');
    const form = document.getElementById('form-add-point');

    function openModal(lat, lng) {
        document.getElementById('input-lat').value = lat;
        document.getElementById('input-lng').value = lng;
        modal.classList.add('show');
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            modal.classList.remove('show');
        });
    }

    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.classList.remove('show');
        }
    });

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            // On s'assure que les types sont corrects pour l'API
            const payload = {
                ...data,
                latitude: parseFloat(data.latitude),
                longitude: parseFloat(data.longitude),
                categorie: parseInt(data.categorie),
                // iduser: 1 // Géré par le back par défaut pour l'instant
            };

            try {
                // Remplacez l'URL par celle de votre API backend
                // Si vous lancez le back avec `php -S localhost:8888 -t back/public`
                const apiUrl = 'http://localhost:8888/api/points';

                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                if (response.ok) {
                    const result = await response.json();
                    alert('Point créé avec succès !');
                    modal.classList.remove('show');
                    form.reset();

                    // Ajouter le marqueur sur la carte
                    L.marker([payload.latitude, payload.longitude]).addTo(map)
                        .bindPopup(`<b>${payload.titre}</b><br>${payload.description || ''}`);
                } else {
                    const errorData = await response.json();
                    alert('Erreur : ' + (errorData.error || 'Une erreur est survenue'));
                }
            } catch (error) {
                console.error('Erreur fetch:', error);
                alert('Impossible de contacter le serveur.');
            }
        });
    }
});