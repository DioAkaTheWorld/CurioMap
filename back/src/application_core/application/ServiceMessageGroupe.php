<?php

namespace CurioMap\src\application_core\application;

use CurioMap\src\application_core\application\spi\MessageGroupeRepositoryInterface;
use CurioMap\src\application_core\domain\MessageGroupe;

class ServiceMessageGroupe
{
    private MessageGroupeRepositoryInterface $messageGroupeRepository;

    public function __construct(MessageGroupeRepositoryInterface $messageGroupeRepository)
    {
        $this->messageGroupeRepository = $messageGroupeRepository;
    }


    public function addMessage(int $idGroupe, int $idUser, string $message): MessageGroupe
    {
        if (empty(trim($message))) {
            throw new \InvalidArgumentException("Le message ne peut pas être vide");
        }

        return $this->messageGroupeRepository->addMessage($idGroupe, $idUser, $message);
    }


    public function getMessagesByGroupe(int $idGroupe): array
    {
        return $this->messageGroupeRepository->getMessagesByGroupe($idGroupe);
    }


    public function deleteMessage(int $idMessage, int $idUser): bool
    {
        if (!$this->messageGroupeRepository->canDeleteMessage($idMessage, $idUser)) {
            throw new \Exception("Vous ne pouvez supprimer que vos propres messages");
        }

        return $this->messageGroupeRepository->deleteMessage($idMessage);
    }
}

