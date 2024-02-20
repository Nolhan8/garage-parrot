<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 32)]
    private ?string $firstName = null;

    #[ORM\Column(length: 64)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 64)]
    private ?string $role = null;

    #[ORM\Column(length: 32)]
    private ?string $username = null;
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }
    
    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    // Méthode de l'interface UserInterface
    public function getRoles(): array
    {
        $role = [];
    
        // Ajoutez des conditions pour attribuer un seul rôle en fonction de la propriété $role
        if ($this->role === 'admin') {
            $roles[] = 'ROLE_ADMIN';
        } elseif ($this->role === 'user') {
            $roles[] = 'ROLE_USER';
        }
    
        return $roles;
    }
    

    public function getSalt(): ?string
    {
        // Si vous utilisez l'encodage de mot de passe natif de Symfony, vous pouvez retourner null
        return null;
    }

    public function getUsername(): string
    {
        // Retourner le nom d'utilisateur de l'utilisateur
        return $this->username;
    }

    public function eraseCredentials(): void
    {
        // Supprimer les informations sensibles qui seraient stockées sur l'utilisateur
        // Cette méthode est requise par l'interface UserInterface, mais n'est pas utilisée dans ce cas
    }
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    // Implémentation de la méthode jsonSerialize()
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'password' => $this->password,
            // Ajoutez d'autres champs si nécessaire
        ];
    }
}
