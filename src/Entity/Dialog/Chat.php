<?php

namespace App\Entity\Dialog;

use App\Repository\Dialog\ChatRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChatRepository::class)]
class Chat
{
    #[ORM\Id]
    #[ORM\Column(type: Types::GUID)]
    private string $id;

    #[ORM\Column(type: Types::JSON)]
    private array $userIds = [];

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getUserIds(): array
    {
        return $this->userIds;
    }

    /**
     * @param string[] $userIds
     */
    public function setUserIds(array $userIds): static
    {
        $this->userIds = $userIds;

        return $this;
    }
}
