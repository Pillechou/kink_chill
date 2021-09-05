<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $discordID;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $discordUsername;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDiscordID(): ?string
    {
        return $this->discordID;
    }

    public function setDiscordID(?string $discordID): self
    {
        $this->discordID = $discordID;

        return $this;
    }

    public function getDiscordUsername(): ?string
    {
        return $this->discordUsername;
    }

    public function setDiscordUsername(?string $discordUsername): self
    {
        $this->discordUsername = $discordUsername;

        return $this;
    }
}
