<?php

namespace CurioMap\src\application_core\domain\entities;

class Utilisateur{
    private int $id;
    private string $nom;
    private string $email;
    private string $password;
    private ?int $role;

    public function construct__(
        int $id,
        string $nom,
        string $email,
        string $password,
        int $role
    ){
        $this->id=$id;
        $this->email=$email;
        $this->nom=$nom;
        $this->password=$password;
        $this->role=$role;
    }

    public function getId(): int{
        return $this->id;
    }

    public function getNom(): string{
        return $this->nom;
    }

    public function getEmail(): string{
        return $this->email;
    }

    public function getPassword(): string{
        return $this->password;
    }

    public function getRole(): int{
        return $this->role;
    }
}