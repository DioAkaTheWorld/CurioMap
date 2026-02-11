<?php
namespace CurioMap\src\application_core\domain\entities;

class User {

    private ?int $id;
    private string $nom;
    private string $email;
    private string $motdepasse;
    private int $role;


    public function __construct(?int $id, string $nom, string $email, string $motdepasse, int $role = 0) {
        $this->id = $id;
        $this->nom = $nom;
        $this->email = $email;
        $this->motdepasse = $motdepasse; 
        $this->role = $role;
    }

    // getter
    public function getId(): ?int { return $this->id; }

    public function getNom(): string {return $this->nom;}

	public function getEmail(): string {return $this->email;}

	public function getMotdepasse(): string {return $this->motdepasse;}

	public function getRole(): int {return $this->role;}

    // setter
    public function setNom(string $nom): void {$this->nom = $nom;}

	public function setEmail(string $email): void {$this->email = $email;}

	public function setMotdepasse(string $motdepasse): void {$this->motdepasse = $motdepasse;}

	public function setRole(int $role): void {$this->role = $role;}


}

	