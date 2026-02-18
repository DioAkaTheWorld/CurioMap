<?php
namespace CurioMap\src\application_core\application\usecases;

use CurioMap\src\application_core\application\ports\api\ServiceGroupeInterface;
use CurioMap\src\application_core\application\ports\spi\GroupeRepositoryInterface;
use CurioMap\src\application_core\domain\entities\Groupe;

class ServiceGroupe implements ServiceGroupeInterface {
    private GroupeRepositoryInterface $groupeRepository;

    public function __construct(GroupeRepositoryInterface $groupeRepository) {
        $this->groupeRepository = $groupeRepository;
    }

    public function creerGroupe(string $nom, string $description, int $idCreateur): Groupe {
        //Génération d'un code unique
        $code = strtoupper(substr(uniqid(), -6));
        $groupe = new Groupe($nom, $idCreateur, $description, $code);

        $id = $this->groupeRepository->save($groupe);
        $groupe->setId($id);

        //ajouter le créateur comme membre automatiquement
        $this->groupeRepository->ajouterMembre($id, $idCreateur);

        return $groupe;
    }

    public function rejoindreGroupe(string $codeInvitation, int $userId): Groupe {
        $groupe = $this->groupeRepository->findByCode($codeInvitation);

        if (!$groupe) {
            throw new \Exception("Code d'invitation invalide");
        }

        if ($this->groupeRepository->isMembre($groupe->getId(), $userId)) {
            throw new \Exception("Vous êtes déjà membre de ce groupe");
        }

        $this->groupeRepository->ajouterMembre($groupe->getId(), $userId);

        return $groupe;
    }

    public function getGroupesUtilisateur(int $userId): array {
        return $this->groupeRepository->findAllByUser($userId);
    }

    public function quitterGroupe(int $groupeId, int $userId): void {
        $groupe = $this->groupeRepository->findById($groupeId);

        if (!$groupe) {
            throw new \Exception("Groupe non trouvé");
        }

        //Si c'est le créateur, on supprime le groupe
        if ($groupe->getIdCreateur() === $userId) {
             throw new \Exception("Le créateur ne peut pas quitter le groupe, il doit le supprimer (fonctionnalité non implémentée)");
             //Pour l'instant on empeche juste, il faudrait supp le groupe, à faire après
        }

        if (!$this->groupeRepository->isMembre($groupeId, $userId)) {
            throw new \Exception("Vous ne faites pas partie de ce groupe");
        }

        $this->groupeRepository->retirerMembre($groupeId, $userId);
    }
}
