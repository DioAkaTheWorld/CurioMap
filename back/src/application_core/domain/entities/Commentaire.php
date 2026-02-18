<?php

namespace CurioMap\src\application_core\domain\entities;

use DateTime;

class Commentaire {
    private ?int $id;
    private int $iduser;
    private int $idpoint;
    private string $commentaire;
    private ?int $note;
    private DateTime $dateCreation;

    public function __construct(
        int $iduser,
        int $idpoint,
        string $commentaire,
        ?int $note = null,
        ?DateTime $dateCreation = null,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->iduser = $iduser;
        $this->idpoint = $idpoint;
        $this->commentaire = $commentaire;
        $this->note = $note;
        $this->dateCreation = $dateCreation ?? new DateTime();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getIdUser(): int {
        return $this->iduser;
    }

    public function getIdPoint(): int {
        return $this->idpoint;
    }

    public function getCommentaire(): string {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): void {
        $this->commentaire = $commentaire;
    }

    public function getNote(): ?int {
        return $this->note;
    }

    public function setNote(?int $note): void {
        $this->note = $note;
    }

    public function getDateCreation(): DateTime {
        return $this->dateCreation;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'iduser' => $this->iduser,
            'idpoint' => $this->idpoint,
            'commentaire' => $this->commentaire,
            'note' => $this->note,
            'date_creation' => $this->dateCreation->format('Y-m-d H:i:s')
        ];
    }
}

