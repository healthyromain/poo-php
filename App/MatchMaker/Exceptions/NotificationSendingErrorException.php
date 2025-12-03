<?php

declare(strict_types=1);

namespace App\MatchMaker\Exceptions;

use RuntimeException;

class NotificationSendingErrorException extends RuntimeException
{
    public $message = "Impossible d'envoyer la notification.";
}
