<?php
namespace CurioMap\src\application_core\application\ports\api\dtos;

class ProfileDTO {
    public int $id;
    public string $nom;
    public string $email;
    public ?int $role;

    public function __construct(int $id, string $nom, string $email, ?int $role = 0)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->email = $email;
        $this->role = $role;
    }
}
