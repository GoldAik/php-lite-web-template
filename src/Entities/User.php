<?php

declare(strict_types = 1);

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity, ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\Column, ORM\GeneratedValue]
    private int|null $id = null;

    #[ORM\Column]
    private string $username;

    #[ORM\Column]
    private string $email;

    #[ORM\Column(name: 'password_hash')]
    private string $passwordHash;

    #[ORM\Column(name: 'created_at')]
    private \DateTime $createdAt;

    #[ORM\Column(name: 'updated_at')]
    private \DateTime $updatedAt;


    public function getData(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'passwordHash' => $this->passwordHash,
        ];
    }
}