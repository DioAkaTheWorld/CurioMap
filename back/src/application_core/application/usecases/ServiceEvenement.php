<?php
namespace CurioMap\src\application_core\application\usecases;

use CurioMap\src\application_core\application\ports\api\ServiceEvenementInterface;
use CurioMap\src\application_core\application\ports\spi\EvenementRepositoryInterface;
use CurioMap\src\application_core\domain\entities\Evenement;
use DateTime;
use InvalidArgumentException;

class ServiceEvenement implements ServiceEvenementInterface{
    private EvenementRepositoryInterface $evenementRepository;

    public function __construct(EvenementRepositoryInterface $evenementRepository){
        $this->evenementRepository = $evenementRepository;
    }

    public function creeEvent(array $data): Evenement{
        $dateStrDebut=$data['dateDebut'] ?? null;
        $dateStrFin=$data['dateFin'] ?? null;
        //vérif des données
        if (empty($data['dateDebut']) || empty($data['dateFin'])){
            throw new InvalidArgumentException("Les dates de début et de fin sont obligatoires.");
        }
        if (empty($data['iduser'])){
             throw new InvalidArgumentException("L'utilisateur est obligatoire.");
        }
        try {
            $dateDebut = new DateTime($dateStrDebut);
            $dateFin = new DateTime($dateStrFin);
        } catch (\Exception $e){
            throw new InvalidArgumentException("Format de date invalide.");
        }
        if ($dateFin < $dateDebut){
            throw new InvalidArgumentException("La date de fin ne peut pas être antérieure à la date de début.");
        }

        //création de l'entité
        $event = new Evenement(
            iduser: (int)$data['iduser'], // On force le type
            idpoint: !empty($data['idpoint']) ? (int)$data['idpoint'] : null,
            titre_evenement: $data['titre_evenement'] ?? null,
            date_debut: $dateDebut,
            date_fin: $dateFin,
            notes: $data['notes'] ?? null
        );

        $id = $this->evenementRepository->save($event);
        $event->setId($id);

        return $event;
    }

    public function getUserEvents(int $userId): array{
        return $this->evenementRepository->findByUser($userId);
    }
}
