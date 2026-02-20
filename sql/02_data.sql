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


INSERT INTO PointInteret 
(iduser, titre, image, description, categorie, latitude, longitude, adresse, visibilite) VALUES

-- MONUMENTS (2)
(1, 'Place Stanislas', 'place-stanislas.jpg', 'Place emblématique classée UNESCO au cœur de Nancy', 2, 48.693722, 6.183409, 'Place Stanislas, Nancy', 1),
(1, 'Porte de la Craffe', 'porte-craffe.jpg', 'Ancienne porte médiévale de Nancy', 2, 48.697222, 6.177778, 'Grande Rue, Nancy', 1),
(1, 'Place de la Carrière', 'place-carriere.jpg', 'Place historique dans le prolongement de la place Stanislas', 2, 48.695833, 6.181944, 'Place de la Carrière, Nancy', 1),
(1, 'Basilique Saint-Epvre', 'basilique-saint-epvre.jpg', 'Basilique néogothique située dans la vieille ville', 2, 48.696944, 6.180556, 'Place Saint-Epvre, Nancy', 1),
(1, 'Château de Lunéville', 'chateau-luneville.jpg', 'Surnommé le petit Versailles lorrain', 2, 48.589722, 6.496111, 'Place de la 2e Division de Cavalerie, Lunéville', 1),
(1, 'Fort de Villey-le-Sec', 'fort-villey.jpg', 'Fortification du système Séré de Rivières', 2, 48.675000, 5.966667, 'Villey-le-Sec', 1),

-- MUSÉES (5)
(1, 'Musée des Beaux-Arts de Nancy', 'mba-nancy.jpg', 'Musée situé place Stanislas', 5, 48.693889, 6.183611, 'Place Stanislas, Nancy', 1),
(1, 'Musée de l’École de Nancy', 'ecole-nancy.jpg', 'Musée consacré à l’Art nouveau', 5, 48.684167, 6.171389, '36-38 Rue du Sergent Blandan, Nancy', 1),
(1, 'Muséum-Aquarium de Nancy', 'museum-aquarium.jpg', 'Musée scientifique et aquarium', 5, 48.688611, 6.180833, '34 Rue Sainte-Catherine, Nancy', 1),
(1, 'Centre Pompidou-Metz', 'pompidou-metz.jpg', 'Grand centre d’art moderne en Lorraine', 5, 49.109722, 6.176389, '1 Parvis des Droits de l’Homme, Metz', 1),
(1, 'Musée de la Cour d’Or', 'cour-or.jpg', 'Musée d’archéologie et d’art à Metz', 5, 49.120556, 6.175000, '2 Rue du Haut Poirier, Metz', 1),

-- PARCS (4)
(1, 'Parc de la Pépinière', 'pepiniere.jpg', 'Grand parc public au centre de Nancy', 4, 48.697500, 6.184444, 'Parc de la Pépinière, Nancy', 1),
(1, 'Jardin Botanique Jean-Marie Pelt', 'jardin-botanique.jpg', 'Grand jardin botanique à Villers-lès-Nancy', 4, 48.664444, 6.155556, '100 Rue du Jardin Botanique, Villers-lès-Nancy', 1),
(1, 'Plan d’Eau de la Méchelle', 'plan-eau-mechelle.jpg', 'Zone naturelle pour promenade', 4, 48.706111, 6.211944, 'Tomblaine', 1),
(1, 'Parc Sainte-Marie', 'parc-sainte-marie.jpg', 'Parc arboré avec roseraie', 4, 48.676667, 6.170833, 'Rue Dupont des Loges, Nancy', 1),

-- RESTAURANTS (1)
(1, 'La Maison dans le Parc', 'maison-parc.jpg', 'Restaurant gastronomique étoilé à Nancy', 1, 48.688056, 6.180278, '3 Rue Sainte-Catherine, Nancy', 1),
(1, 'Brasserie Excelsior', 'excelsior.jpg', 'Brasserie Art nouveau emblématique', 1, 48.689722, 6.176389, '50 Rue Henri-Poincaré, Nancy', 1),
(1, 'Le V-Four', 'v-four.jpg', 'Restaurant traditionnel lorrain', 1, 48.695278, 6.180278, 'Rue Gambetta, Nancy', 1),
(1, 'Chez Véro', 'chez-vero.jpg', 'Cuisine française maison', 1, 48.692222, 6.181667, 'Nancy Centre', 1),

-- CONCERTS / SALLES (3)
(1, 'Zénith de Nancy', 'zenith-nancy.jpg', 'Grande salle de concert de la métropole', 3, 48.704722, 6.139722, 'Rue du Zénith, Maxéville', 1),
(1, 'L’Autre Canal', 'autre-canal.jpg', 'Salle dédiée aux musiques actuelles', 3, 48.683611, 6.199444, '45 Boulevard d’Austrasie, Nancy', 1),
(1, 'Arsenal de Metz', 'arsenal-metz.jpg', 'Salle de concert symphonique réputée', 3, 49.106389, 6.172222, '3 Avenue Ney, Metz', 1),

-- AUTRES LIEUX TOURISTIQUES
(1, 'Cathédrale Saint-Étienne de Metz', 'cathedrale-metz.jpg', 'Cathédrale gothique surnommée la lanterne du Bon Dieu', 2, 49.120278, 6.175556, 'Place d’Armes, Metz', 1),
(1, 'Place Saint-Louis', 'place-saint-louis.jpg', 'Place médiévale emblématique de Metz', 2, 49.119444, 6.177222, 'Metz Centre', 1),
(1, 'Parc animalier de Sainte-Croix', 'sainte-croix.jpg', 'Grand parc animalier en Moselle', 4, 48.777222, 6.901944, 'Rhodes, Moselle', 1),
(1, 'Lac de Gérardmer', 'lac-gerardmer.jpg', 'Grand lac naturel des Vosges', 4, 48.070556, 6.877778, 'Gérardmer', 1),
(1, 'Station de La Bresse', 'la-bresse.jpg', 'Station de ski des Vosges', 4, 47.997222, 6.876944, 'La Bresse', 1),
(1, 'Mine de fer de Neuves-Maisons', 'mine-neuves-maisons.jpg', 'Ancienne mine visitable en Lorraine', 5, 48.613889, 6.102500, 'Neuves-Maisons', 1),
(1, 'Villa Majorelle', 'villa-majorelle.jpg', 'Maison Art nouveau emblématique', 2, 48.684722, 6.170278, '1 Rue Louis Majorelle, Nancy', 1),
(1, 'Église des Cordeliers', 'eglise-cordeliers.jpg', 'Église historique de la vieille ville', 2, 48.695833, 6.179722, 'Grande Rue, Nancy', 1),
(1, 'Nancy Thermal', 'nancy-thermal.jpg', 'Complexe thermal et bien-être', 4, 48.677500, 6.160833, 'Rue Sergent Blandan, Nancy', 1),
(1, 'Marché Central de Nancy', 'marche-central.jpg', 'Marché couvert historique', 2, 48.692500, 6.183056, 'Rue Saint-Dizier, Nancy', 1);