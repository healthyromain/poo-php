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

exit(0);