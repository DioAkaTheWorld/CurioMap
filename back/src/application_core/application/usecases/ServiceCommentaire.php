<?php
namespace CurioMap\src\application_core\application\usecases;

use CurioMap\src\application_core\application\ports\api\ServiceCommentaireInterface;
use CurioMap\src\application_core\application\ports\spi\CommentaireRepositoryInterface;
use CurioMap\src\application_core\domain\entities\Commentaire;

class ServiceCommentaire implements ServiceCommentaireInterface {
    private CommentaireRepositoryInterface $commentaireRepository;

    public function __construct(CommentaireRepositoryInterface $commentaireRepository) {
        $this->commentaireRepository = $commentaireRepository;
    }

    public function createCommentaire(array $data): Commentaire {
        if (empty($data['iduser']) || empty($data['idpoint']) || empty($data['commentaire'])) {
            throw new \InvalidArgumentException("l'id de l'utilisateur, du point et le commentaire sont obligatoires");
        }

        if (isset($data['note'])) {
            $note = (int)$data['note'];
            if ($note < 1 || $note > 5) {
                throw new \InvalidArgumentException("La note doit être entre 1 et 5");
            }
        }

        $commentaire = new Commentaire(
            iduser: (int)$data['iduser'],
            idpoint: (int)$data['idpoint'],
            commentaire: $data['commentaire'],
            note: isset($data['note']) ? (int)$data['note'] : null
        );

        $id = $this->commentaireRepository->save($commentaire);

        return $this->commentaireRepository->findById($id);
    }

    public function getCommentairesByPoint(int $pointId): array {
        return $this->commentaireRepository->findByPointId($pointId);
    }

    public function getCommentairesByUser(int $userId): array {
        return $this->commentaireRepository->findByUserId($userId);
    }

    public function deleteCommentaire(int $id, int $userId): bool {
        $commentaire = $this->commentaireRepository->findById($id);

        if (!$commentaire) {
            throw new \InvalidArgumentException("Commentaire non trouvé");
        }

        if ($commentaire->getIdUser() !== $userId) {
            throw new \Exception("Vous n'êtes pas autorisé à supprimer ce commentaire");
        }

        return $this->commentaireRepository->delete($id);
    }
}

