<?php
namespace CurioMap\src\application_core\application\ports\api\dtos;

class CredentialsDTO {
    public string $nom;
    public string $email;
    public string $password;

    public function __construct(string $email, string $password, string $nom = '')
    {
        $this->email = $email;
        $this->password = $password;
        $this->nom = $nom;
    }
}
