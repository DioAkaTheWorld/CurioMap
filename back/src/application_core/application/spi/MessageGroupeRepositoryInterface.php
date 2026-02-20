<?php

namespace CurioMap\src\application_core\application\spi;

use CurioMap\src\application_core\domain\MessageGroupe;

interface MessageGroupeRepositoryInterface
{
    public function addMessage(int $idGroupe, int $idUser, string $message): MessageGroupe;
    public function getMessagesByGroupe(int $idGroupe): array;
    public function deleteMessage(int $idMessage): bool;
    public function canDeleteMessage(int $idMessage, int $idUser): bool;
}

