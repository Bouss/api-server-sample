<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Bounded list of API users. This is app configuration: no need to persist these data in a business database
 */
class User implements UserInterface
{
    public function __construct(
        private string $username,
        private ?string $apiKey,
        private array $roles
    ) {}

    /**
     * {@inheritDoc}
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * {@inheritDoc}
     */
    public function getPassword(): ?string
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function eraseCredentials(): void
    {
    }
}
