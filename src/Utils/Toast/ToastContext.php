<?php

declare(strict_types=1);

namespace App\Utils\Toast;

/**
 * values can only contain A-z0-9 and -
 */
enum ToastContext: string
{
    case Login = 'toast-login';
    case Registration = 'toast-registration';
    case ForgottenPasswordRequest = 'forgotten-password-request';
    case PasswordReset = 'password-reset';
}
