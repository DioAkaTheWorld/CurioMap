<?php
namespace CurioMap\src\application_core\domain\entities;

class Groupe {
    private ?int $id;
    private string $nom;
    private ?string $description;
    private int $idCreateur;
    private ?string $codeInvitation;

    public function __construct(string $nom, int $idCreateur, ?string $description = null, ?string $codeInvitation = null, ?int $id = null) {
        $this->nom = $nom;
        $this->idCreateur = $idCreateur;
        $this->description = $description;
        $this->codeInvitation = $codeInvitation;
        $this->id = $id;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function getIdCreateur(): int {
        return $this->idCreateur;
    }

    public function getCodeInvitation(): ?string {
        return $this->codeInvitation;
    }

    public function setCodeInvitation(string $code): void {
        $this->codeInvitation = $code;
    }
}
