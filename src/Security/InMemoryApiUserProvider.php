<?php

namespace App\Security;

use LogicException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Custom 'memory' user provider to provide in-memory API key users
 */
class InMemoryApiUserProvider implements UserProviderInterface
{
    /** @var User[] $user */
    private array $users;

    public function __construct(array $users = [])
    {
        foreach ($users as $username => $attributes) {
            $this->users[] = new User($username, $attributes['api_key'], ['ROLE_API']);
        }
    }

    public function getUsernameForApiKey(string $apiKey): ?string
    {
        foreach ($this->users as $user) {
            if ($apiKey === $user->getApiKey()) {
                return $user->getUsername();
            }
        }

        return null;
    }

    public function createUser(UserInterface $user): void
    {
        if (isset($this->users[strtolower($user->getUsername())])) {
            throw new LogicException('Another user with the same username already exists.');
        }

        $this->users[strtolower($user->getUsername())] = $user;
    }

    /**
     * {@inheritDoc}
     */
    public function loadUserByUsername(string $username): User
    {
        return new User($username, null, ['ROLE_API']);
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user): User
    {
        throw new UnsupportedUserException();
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }
}
