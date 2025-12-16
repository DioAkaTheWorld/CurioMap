INSERT INTO Categorie (libelle) VALUES
('Restaurant'),
('Monument'),
('Concert'),
('Parc'),
('Musée');

INSERT INTO PointInteret (iduser, titre, image, description, categorie, latitude, longitude, adresse, visibilite) VALUES
(1, 'Tour Eiffel', 'tour-eiffel.jpg', 'Monument emblématique de Paris', 2, 48.858370, 2.294481, 'Champ de Mars, Paris', 'public');