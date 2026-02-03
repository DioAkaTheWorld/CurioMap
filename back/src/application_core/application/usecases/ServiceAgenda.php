<?php
namespace CurioMap\src\application_core\application\usecases;

use CurioMap\src\application_core\application\ports\api\ServiceAgendaInterface;
use CurioMap\src\application_core\application\ports\spi\AgendaRepositoryInterface;
use CurioMap\src\application_core\domain\entities\Agenda;
use DateTime;
use InvalidArgumentException;

class ServiceAgenda implements ServiceAgendaInterface{
    private AgendaRepositoryInterface $agendaRepository;

    public function __construct(AgendaRepositoryInterface $agendaRepository){
        $this->agendaRepository = $agendaRepository;
    }

    public function creeEvent(array $data): Agenda{
        //vérif des données
        if (empty($data['date_debut']) || empty($data['date_fin'])){
            throw new InvalidArgumentException("Les dates de début et de fin sont obligatoires.");
        }
        if (empty($data['iduser'])){
             throw new InvalidArgumentException("L'utilisateur est obligatoire.");
        }
        try {
            $dateDebut = new DateTime($data['date_debut']);
            $dateFin = new DateTime($data['date_fin']);
        } catch (\Exception $e){
            throw new InvalidArgumentException("Format de date invalide.");
        }
        if ($dateFin < $dateDebut){
            throw new InvalidArgumentException("La date de fin ne peut pas être antérieure à la date de début.");
        }

        //création de l'entité
        $agenda = new Agenda(
            iduser: (int)$data['iduser'], // On force le type
            idpoint: !empty($data['idpoint']) ? (int)$data['idpoint'] : null,
            titre_evenement: $data['titre_evenement'] ?? null,
            date_debut: $dateDebut,
            date_fin: $dateFin,
            notes: $data['notes'] ?? null
        );

        $id = $this->agendaRepository->save($agenda);
        $agenda->setId($id);

        return $agenda;
    }

    public function getUserEvents(int $userId): array{
        return $this->agendaRepository->findByUser($userId);
    }
}
