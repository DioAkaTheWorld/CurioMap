<?php

namespace CurioMap\src\application_core\domain;

use DateTime;

class MessageGroupe
{
    private ?int $id;
    private int $idGroupe;
    private int $idUser;
    private string $message;
    private DateTime $dateCreation;
    private ?string $nomUtilisateur;

    public function __construct(
        int $idGroupe,
        int $idUser,
        string $message,
        DateTime $dateCreation,
        ?int $id = null,
        ?string $nomUtilisateur = null
    ) {
        $this->id = $id;
        $this->idGroupe = $idGroupe;
        $this->idUser = $idUser;
        $this->message = $message;
        $this->dateCreation = $dateCreation;
        $this->nomUtilisateur = $nomUtilisateur;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdGroupe(): int
    {
        return $this->idGroupe;
    }

    public function getIdUser(): int
    {
        return $this->idUser;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getDateCreation(): DateTime
    {
        return $this->dateCreation;
    }

    public function getNomUtilisateur(): ?string
    {
        return $this->nomUtilisateur;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'idGroupe' => $this->idGroupe,
            'idUser' => $this->idUser,
            'message' => $this->message,
            'dateCreation' => $this->dateCreation->format('Y-m-d H:i:s'),
            'nomUtilisateur' => $this->nomUtilisateur
        ];
    }
}

