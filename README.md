# CURIOMAP
Bienvenue dans ce projet tutoré.

C’est une application qui permet de plannifier, enregistrer et partager des points d’intérêt comme des lieux culturels, sportifs ou de loisirs.

Les utilisateurs peuvent découvrir des événements, ajouter leurs propres points, les classer et consulter les avis de la communauté.

L’objectif est de faciliter la découverte et le partage de lieux et d’activités autour de soi.

C'est un projet web Fullstack composé de :
- Frontend : VueJs 3 + Vite (avec Leaflet pour la cartographie)
- Backend : API REST en PHP 8.2 (Slim Framework 4)
- Base de données : PostgreSQL
- Infrastructure : Docker & Docker Compose

### Membres du groupe :
- CADET Mattéo
- DIEUDONNE Quentin
- DELABORDE Baptiste
- OZEN Burak
- GLORIAN Ruben

## Installation
Il suffit d'installer les dépendances via composer dans le back :
```bash
cd back
```

```bash
composer install
```

## Démarrage du projet
Toujours être dans le back :
```bash
cd back
```

```bash
docker compose up -d --build
```

## Lancer le serveur de développement Frontend

Être dans le front :
```bash
cd front
```
```bash
cd ../front
```

Lancement
```bash
npm run dev
```

## Liens utiles
Appli en locale : http://localhost:5173/map

BDs : http://localhost:2026/

## A faire

Il manque les images et tout à mettre dans le formulaire d'ajout d'un point.