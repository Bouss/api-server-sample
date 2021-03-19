<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class ApiKeyAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * {@inheritDoc}
     */
    public function supports(Request $request): bool
    {
        return $request->headers->has('X-API-KEY');
    }

    /**
     * {@inheritDoc}
     */
    public function getCredentials(Request $request)
    {
        return $request->headers->get('X-API-KEY');
    }

    /**
     * {@inheritDoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        /** @var string|null $apiKey */
        $apiKey = $credentials;

        if (null === $apiKey) {
            return null;
        }

        /** @var InMemoryApiUserProvider $userProvider */
        $username = $userProvider->getUsernameForApiKey($apiKey);

        if (null === $username) {
            throw new CustomUserMessageAuthenticationException('Unknown API key: ' . $apiKey);
        }

        return $userProvider->loadUserByUsername($username);
    }

    /**
     * {@inheritDoc}
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?JsonResponse
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        $data = [
            'error' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * {@inheritDoc}
     */
    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
    {
        $data = [
            'error' => 'Authentication required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * {@inheritDoc}
     */
    public function supportsRememberMe(): bool
    {
        return false;
    }
}
