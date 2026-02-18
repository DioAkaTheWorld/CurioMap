<?php
namespace CurioMap\src\application_core\application\usecases;

use CurioMap\src\application_core\application\ports\spi\UtilisateurRepositoryInterface;
use CurioMap\src\application_core\domain\entities\Utilisateur;

class ServiceUtilisateur {

    private UtilisateurRepositoryInterface $utilisateurRepository;

    public function __construct(UtilisateurRepositoryInterface $utilisateurRepository) {
        $this->utilisateurRepository = $utilisateurRepository;
    }

    public function register(string $nom, string $email, string $password, int $role = 0): Utilisateur {
        if ($this->utilisateurRepository->findByEmail($email)) {
            throw new \Exception("Email déjà utilisé");
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $utilisateur = new Utilisateur(null, $nom, $email, $hashedPassword, $role);
        return $this->utilisateurRepository->save($utilisateur);
    }


    public function login(string $email, string $password): Utilisateur {
        $utilisateur = $this->utilisateurRepository->findByEmail($email);
        if (!$utilisateur) {
            throw new \Exception("Utilisateur non trouvé");
        }
        if (!password_verify($password, $utilisateur->getPassword())) {
            throw new \Exception("Mot de passe incorrect");
        }
        return $utilisateur;
    }

    public function getById(int $id): ?Utilisateur {
        return $this->utilisateurRepository->findById($id);
    }
}
