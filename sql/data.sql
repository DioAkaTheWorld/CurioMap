INSERT INTO Utilisateur (id, nom, email, motdepasse, role) VALUES
(1, 'Admin', 'admin@curiomap.fr', 'admin', 1);

-- Mise à jour de la séquence pour éviter les conflits futurs
SELECT setval('utilisateur_id_seq', (SELECT MAX(id) FROM Utilisateur));

INSERT INTO Categorie (id, libelle) VALUES
(1, 'Restaurant'),
(2, 'Monument'),
(3, 'Concert'),
(4, 'Parc'),
(5, 'Musée');

-- Mise à jour de la séquence pour les catégories
SELECT setval('categorie_id_seq', (SELECT MAX(id) FROM Categorie));

-- Monuments célèbres
INSERT INTO PointInteret (iduser, titre, image, description, categorie, latitude, longitude, adresse, visibilite) VALUES
(1, 'Tour Eiffel', 'tour-eiffel.jpg', 'Monument emblématique de Paris', 2, 48.858370, 2.294481, 'Champ de Mars, Paris', 1),
(1, 'Colisée', 'colisee.jpg', 'Amphithéâtre emblématique de Rome', 2, 41.890210, 12.492231, 'Piazza del Colosseo, Rome', 1),
(1, 'Statue de la Liberté', 'statue-liberte.jpg', 'Symbole de New York et de la liberté', 2, 40.689247, -74.044502, 'Liberty Island, New York', 1),
(1, 'Taj Mahal', 'taj-mahal.jpg', 'Mausolée célèbre en Inde', 2, 27.175015, 78.042155, 'Agra, Inde', 1),
(1, 'Big Ben', 'big-ben.jpg', 'Cloche et tour emblématique de Londres', 2, 51.500729, -0.124625, 'Westminster, Londres', 1),
(1, 'Pyramides de Gizeh', 'pyramides-gizeh.jpg', 'Anciennes pyramides en Égypte', 2, 29.979235, 31.134202, 'Gizeh, Égypte', 1),
(1, 'Sagrada Familia', 'sagrada-familia.jpg', 'Basilique emblématique de Barcelone', 2, 41.403629, 2.174356, 'Barcelona, Espagne', 1);

-- Musées célèbres
INSERT INTO PointInteret (iduser, titre, image, description, categorie, latitude, longitude, adresse, visibilite) VALUES
(1, 'Louvre', 'louvre.jpg', 'Le musée le plus visité de Paris', 5, 48.860611, 2.337644, 'Rue de Rivoli, Paris', 1),
(1, 'British Museum', 'british-museum.jpg', 'Musée à Londres avec des collections mondialement célèbres', 5, 51.519413, -0.126957, 'Great Russell St, Londres', 1),
(1, 'Metropolitan Museum of Art', 'met-museum.jpg', 'Grand musée de New York', 5, 40.779437, -73.963244, '1000 5th Ave, New York', 1),
(1, 'Musée du Prado', 'prado.jpg', 'Musée majeur à Madrid', 5, 40.413781, -3.692127, 'Calle de Ruiz de Alarcón, Madrid', 1);

-- Parcs célèbres
INSERT INTO PointInteret (iduser, titre, image, description, categorie, latitude, longitude, adresse, visibilite) VALUES
(1, 'Central Park', 'central-park.jpg', 'Parc emblématique de New York', 4, 40.785091, -73.968285, 'New York, USA', 1),
(1, 'Hyde Park', 'hyde-park.jpg', 'Grand parc de Londres', 4, 51.507268, -0.165730, 'Londres, Royaume-Uni', 1),
(1, 'Jardin du Luxembourg', 'luxembourg.jpg', 'Jardin célèbre à Paris', 4, 48.846222, 2.337160, 'Paris, France', 1);

-- Restaurants / gastronomie célèbres (un peu mythique pour la base)
INSERT INTO PointInteret (iduser, titre, image, description, categorie, latitude, longitude, adresse, visibilite) VALUES
(1, 'Pizzeria Da Michele', 'da-michele.jpg', 'Pizzeria emblématique de Naples', 1, 40.852163, 14.268111, 'Via Cesare Sersale 1, Naples, Italie', 1),
(1, 'Le Meurice', 'le-meurice.jpg', 'Restaurant étoilé à Paris', 1, 48.865633, 2.328300, '228 Rue de Rivoli, Paris', 1),
(1, 'Noma', 'noma.jpg', 'Restaurant renommé à Copenhague', 1, 55.686724, 12.599640, 'Refshalevej 96, Copenhague', 1);

-- Activités / concerts / festivals
INSERT INTO PointInteret (iduser, titre, image, description, categorie, latitude, longitude, adresse, visibilite) VALUES
(1, 'Festival Coachella', 'coachella.jpg', 'Festival de musique à Indio, Californie', 3, 33.678422, -116.237964, 'Indio, Californie', 1),
(1, 'Tomorrowland', 'tomorrowland.jpg', 'Festival de musique électronique en Belgique', 3, 51.079360, 4.425480, 'Boom, Belgique', 1),
(1, 'Rock in Rio', 'rock-in-rio.jpg', 'Festival de musique emblématique au Brésil', 3, -22.911013, -43.228489, 'Rio de Janeiro, Brésil', 1);
