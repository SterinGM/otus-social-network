<?php

namespace App\Service\Security;

use App\Repository\ApiTokenRepository;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class ApiTokenHandler implements AccessTokenHandlerInterface
{
    private ApiTokenRepository $repository;

    public function __construct(ApiTokenRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        $apiToken = $this->repository->getByToken($accessToken);

        if (!$apiToken) {
            throw new BadCredentialsException('Invalid API token');
        }

        return new UserBadge($apiToken->getUserId());
    }
}