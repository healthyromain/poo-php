<?php

declare(strict_types=1);

namespace App\MatchMaker\Exceptions;

use RuntimeException;

class EmailSendingErrorException extends RuntimeException
{
    public $message = "Impossible d'envoyer l'email.";
}
