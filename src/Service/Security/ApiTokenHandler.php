<?php

namespace App\Service\Security;

use App\Service\ApiToken\ApiTokenProviderInterface;
use App\Service\Exception\InvalidCredentialsException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class ApiTokenHandler implements AccessTokenHandlerInterface
{
    private ApiTokenProviderInterface $apiTokenProvider;

    public function __construct(ApiTokenProviderInterface $apiTokenProvider)
    {
        $this->apiTokenProvider = $apiTokenProvider;
    }

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        try {
            $token = $this->apiTokenProvider->getToken($accessToken);
        } catch (InvalidCredentialsException) {
            throw new BadCredentialsException('Invalid API token');
        }

        return new UserBadge($token->userId);
    }
}