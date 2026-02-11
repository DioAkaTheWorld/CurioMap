<?php

namespace CurioMap\src\application_core\domain\entities;

use DateTime;

class Evenement{
    private ?int $id;
    private int $iduser;
    private ?int $idpoint;
    private ?string $titre_evenement;
    private DateTime $date_debut;
    private DateTime $date_fin;
    private ?string $notes;

    public function __construct(
        int $iduser,
        ?int $idpoint = null,
        ?string $titre_evenement = null,
        DateTime $date_debut,
        DateTime $date_fin,
        ?string $notes = null,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->iduser = $iduser;
        $this->idpoint = $idpoint;
        $this->titre_evenement = $titre_evenement;
        $this->date_debut = $date_debut;
        $this->date_fin = $date_fin;
        $this->notes = $notes;
    }

    public function getId(): ?int{
        return $this->id;
    }

    public function getIdUser(): int{
        return $this->iduser;
    }

    public function getIdPoint(): ?int{
        return $this->idpoint;
    }

    public function getTitreEvenement(): ?string{
        return $this->titre_evenement;
    }

    public function getDateDebut(): DateTime{
        return $this->date_debut;
    }

    public function getDateFin(): DateTime{
        return $this->date_fin;
    }

    public function getNotes(): ?string{
        return $this->notes;
    }

    public function setId(int $id): void{
        $this->id = $id;
    }

    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }
}