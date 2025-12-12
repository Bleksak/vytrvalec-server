<?php

declare(strict_types=1);

namespace App\Utils\Toast;

/**
 * values can only contain A-z0-9 and -
 */
enum ToastType: string
{
    case Success = 'success';
    case Info = 'info';
    case Warning = 'warning';
    case Error = 'error';
}
