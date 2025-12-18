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

INSERT INTO PointInteret (iduser, titre, image, description, categorie, latitude, longitude, adresse, visibilite) VALUES
(1, 'Tour Eiffel', 'tour-eiffel.jpg', 'Monument emblématique de Paris', 2, 48.858370, 2.294481, 'Champ de Mars, Paris', 1);