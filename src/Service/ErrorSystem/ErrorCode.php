<?php

namespace App\Service\ErrorSystem;

enum ErrorCode: int implements Translatable
{
    // Common
    case SERVER_ERROR = 100;
    case INVALID_PARAMS = 200;

    // Token
    case TOKEN_NOT_FOUND = 1000;

    // User
    case USER_NOT_FOUND = 2000;

    // Authentication
    case INVALID_CREDENTIALS = 3000;
    case AUTHENTICATION_REQUIRED = 3001;

    public function translateCode(): string
    {
        return match($this) {
            self::SERVER_ERROR => 'server_error',
            self::INVALID_PARAMS => 'invalid_params',
            self::USER_NOT_FOUND => 'user_not_found',
            self::INVALID_CREDENTIALS => 'invalid_credentials',
            self::AUTHENTICATION_REQUIRED => 'authentication_required',
            self::TOKEN_NOT_FOUND => 'token_not_found',
        };
    }
}