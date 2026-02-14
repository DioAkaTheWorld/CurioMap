<?php

namespace CurioMap\src\application_core\domain\entities;

class Categorie {
    private ?int $id;
    private string $libelle;
    private ?int $iduser;

    public function __construct(string $libelle, ?int $iduser = null) {
        $this->libelle = $libelle;
        $this->iduser = $iduser;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }

    public function getLibelle(): string {
        return $this->libelle;
    }

    public function getIdUser(): ?int {
        return $this->iduser;
    }
}
