<?php

declare(strict_types=1);

spl_autoload_register(static function (string $fqcn) {
    $path = str_replace('\\', '/', $fqcn) . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

use App\MatchMaker\Entity\Player;
use App\MatchMaker\Entity\BlitzPlayer;
use App\MatchMaker\Service\Lobby;

// --- Test ---
$greg = new Player('greg', 400);
$jade = new Player('jade', 476);

$lobby = new Lobby();
$lobby->addPlayers($greg, $jade);

var_dump($lobby->findOponents($lobby->queuingPlayers[0]));

echo "\n--- BlitzPlayer Test ---\n";
$blitzAlice = new BlitzPlayer('Alice');
$blitzBob = new BlitzPlayer('Bob', 1250);

echo "Alice initial ratio: " . $blitzAlice->getRatio() . "\n";
echo "Bob initial ratio: " . $blitzBob->getRatio() . "\n";

$blitzAlice->updateRatioAgainst($blitzBob, 1);
$blitzBob->updateRatioAgainst($blitzAlice, 0);

echo "Alice ratio after win: " . $blitzAlice->getRatio() . "\n";
echo "Bob ratio after loss: " . $blitzBob->getRatio() . "\n";

// --- Exceptions demo (remplace trigger_error par des exceptions) ---
function sendEmail(string $text): bool
{
    // simulate success
    return true;
}

function sendNotification(string $text): bool
{
    // simulate failure
    throw new \App\MatchMaker\Exceptions\NotificationSendingErrorException();
}

function sendMessage(string $text): bool
{
    if (10 > strlen($text)) {
        throw new \App\MatchMaker\Exceptions\ShortTextException();
    }

    try {
        sendNotification($text);
    } catch (\App\MatchMaker\Exceptions\NotificationSendingErrorException $e) {
        // log or alert teams; continue to send email
        echo "Warning: notification failed: " . $e->getMessage() . "\n";
    } finally {
        // always attempt to send email
        sendEmail($text);
        return true;
    }
}

try {
    sendMessage('Hello, ici Greg "pappy" Boyington');
} catch (\App\MatchMaker\Exceptions\ShortTextException $e) {
    echo $e->getMessage() . "\n";
} catch (\App\MatchMaker\Exceptions\EmailSendingErrorException $e) {
    echo "Une erreur est survenue lors de l'envoi du message, nos équipes ont été prévenues, veuillez réessayer plus tard\n";
} catch (\Exception $e) {
    echo "Une erreur inattendue est survenue, nos équipes ont été prévenues, veuillez réessayer plus tard\n";
}

exit(0);