<?php

declare(strict_types=1);

namespace App\MatchMaker\Exceptions;

use RuntimeException;

class ShortTextException extends RuntimeException
{
    public $message = 'Le texte fourni est trop court.';
}
