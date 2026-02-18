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
        $groupe = new Groupe($nom, $idCreateur, $description);
        $id = $this->groupeRepository->save($groupe);
        $groupe->setId($id);

        //ajouter le créateur comme membre automatiquement
        $this->groupeRepository->ajouterMembre($id, $idCreateur);

        return $groupe;
    }
}
