CREATE TABLE Utilisateur (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    motdepasse VARCHAR(255) NOT NULL,
    role smallint DEFAULT 0 NOT NULL
);

CREATE TABLE Categorie (
    id SERIAL PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE PointInteret (
    id SERIAL PRIMARY KEY,
    iduser INT NOT NULL,
    titre VARCHAR(200) NOT NULL,
    image VARCHAR(255),
    description TEXT,
    categorie INT NOT NULL,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    adresse VARCHAR(255),
    visibilite SMALLINT DEFAULT 0 NOT NULL,
    date_event DATE,
    heure_event TIME,
    FOREIGN KEY (iduser) REFERENCES Utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (categorie) REFERENCES Categorie(id)
);

CREATE TABLE Favoris (
    iduser INT NOT NULL,
    idpoint INT NOT NULL,
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (iduser, idpoint),
    FOREIGN KEY (iduser) REFERENCES Utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (idpoint) REFERENCES PointInteret(id) ON DELETE CASCADE
);

CREATE TABLE Commentaire (
    id SERIAL PRIMARY KEY,
    iduser INT NOT NULL,
    idpoint INT NOT NULL,
    commentaire TEXT NOT NULL,
    note INT CHECK (note BETWEEN 1 AND 5),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (iduser) REFERENCES Utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (idpoint) REFERENCES PointInteret(id) ON DELETE CASCADE
);

CREATE TABLE Agenda (
    id SERIAL PRIMARY KEY,
    iduser INT NOT NULL,
    idpoint INT,
    titre_evenement VARCHAR(200), -- au cas où si idpoint est null, ou pour renommer l'activité
    date_debut TIMESTAMP NOT NULL,
    date_fin TIMESTAMP NOT NULL,
    notes TEXT,
    FOREIGN KEY (iduser) REFERENCES Utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (idpoint) REFERENCES PointInteret(id) ON DELETE SET NULL,
    CHECK (date_fin >= date_debut) -- pour éviter les bugs de dates
);