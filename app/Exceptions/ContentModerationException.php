<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class ContentModerationException extends Exception
{
    public function __construct(
        string $message = 'La imagen contiene contenido no permitido. No se permiten desnudos ni contenido sexual.'
    ) {
        parent::__construct($message);
    }
}
