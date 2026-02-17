<?php

namespace CurioMap\src\application_core\domain\entities;

use DateTime;

class PointInteret{
    private ?int $id;
    private int $iduser;
    private string $titre;
    private ?string $image;
    private ?string $description;
    private int $categorie;
    private DateTime $date;
    private float $latitude;
    private float $longitude;
    private ?string $adresse;
    private int $visibilite;
    private ?DateTime $dateDebut;
    private ?DateTime $dateFin;

    public function __construct(
        int $iduser,
        string $titre,
        int $categorie,
        float $latitude,
        float $longitude,
        ?string $image = null,
        ?string $description = null,
        ?string $adresse = null,
        int $visibilite = 0,
        ?DateTime $date = null,
        ?DateTime $dateDebut = null,
        ?DateTime $dateFin = null,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->iduser = $iduser;
        $this->titre = $titre;
        $this->image = $image;
        $this->description = $description;
        $this->categorie = $categorie;
        $this->date = $date ?? new DateTime();
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->adresse = $adresse;
        $this->visibilite = $visibilite;
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
    }

    //toutes les fonctions utile pour plus tard, yen a beaucoup mais au moins c'est fait
    public function getId(): ?int{
        return $this->id;
    }

    public function getIdUser(): int{
        return $this->iduser;
    }

    public function getTitre(): string{
        return $this->titre;
    }

    public function getImage(): ?string{
        return $this->image;
    }

    public function getDescription(): ?string{
        return $this->description;
    }

    public function getCategorie(): int{
        return $this->categorie;
    }

    public function getDate(): DateTime{
        return $this->date;
    }

    public function getLatitude(): float{
        return $this->latitude;
    }

    public function getLongitude(): float{
        return $this->longitude;
    }

    public function getAdresse(): ?string{
        return $this->adresse;
    }

    public function getVisibilite(): int{
        return $this->visibilite;
    }

    public function getDateDebut(): ?DateTime{
        return $this->dateDebut;
    }

    public function getDateFin(): ?DateTime{
        return $this->dateFin;
    }

    public function setId(int $id): void{
        $this->id = $id;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            iduser: $data['iduser'],
            titre: $data['titre'],
            categorie: $data['categorie'],
            latitude: (float)$data['latitude'],
            longitude: (float)$data['longitude'],
            image: $data['image'],
            description: $data['description'],
            adresse: $data['adresse'],
            visibilite: $data['visibilite'],
            date: new DateTime($data['date']),
            dateDebut: !empty($data['date_debut']) ? new DateTime($data['date_debut']) : null,
            dateFin: !empty($data['date_fin']) ? new DateTime($data['date_fin']) : null,
            id: $data['id']
        );
    }
}