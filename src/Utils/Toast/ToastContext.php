<?php

declare(strict_types=1);

namespace App\Utils\Toast;

enum ToastContext: string
{
    case Login = 'toast-login';
    case Registration = 'toast-registration';
}
