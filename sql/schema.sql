CREATE TABLE Utilisateur (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    motdepasse VARCHAR(255) NOT NULL,
    role smallint DEFAULT '0' NOT NULL
);

CREATE TABLE Categorie (
    id INT PRIMARY KEY AUTO_INCREMENT,
    libelle VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE PointInteret (
    id INT PRIMARY KEY AUTO_INCREMENT,
    iduser INT NOT NULL,
    titre VARCHAR(200) NOT NULL,
    image VARCHAR(255),
    description TEXT,
    categorie INT NOT NULL,
    date DATETIME DEFAULT CURRENT_TIMESTAMP,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    adresse VARCHAR(255),
    visibilite SMALLINT DEFAULT '0' NOT NULL,
    FOREIGN KEY (iduser) REFERENCES Utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (categorie) REFERENCES Categorie(id)
);

CREATE TABLE Favoris (
    iduser INT NOT NULL,
    idpoint INT NOT NULL,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (iduser, idpoint),
    FOREIGN KEY (iduser) REFERENCES Utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (idpoint) REFERENCES PointInteret(id) ON DELETE CASCADE
);

CREATE TABLE Commentaire (
    id INT PRIMARY KEY AUTO_INCREMENT,
    iduser INT NOT NULL,
    idpoint INT NOT NULL,
    commentaire TEXT NOT NULL,
    note INT CHECK (note BETWEEN 1 AND 5),
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (iduser) REFERENCES Utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (idpoint) REFERENCES PointInteret(id) ON DELETE CASCADE
);