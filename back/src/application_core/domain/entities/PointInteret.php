<?php

namespace CurioMap\src\back\application_core\domain\entities;

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
    }

    //Toutes les fonctions utile pour plus tard, yen a beaucoup mais au moins c'est fait
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
}