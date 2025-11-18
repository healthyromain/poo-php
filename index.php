<?php

declare(strict_types=1);

class Player
{
    /** @var int */
    public $level = 0;

    public function __construct(int $level = 0)
    {
        $this->level = $level;
    }
}

class Encounter
{
    const RESULT_WINNER = 1;
    const RESULT_LOSER = -1;
    const RESULT_DRAW = 0;
    const RESULT_POSSIBILITIES = [1, -1, 0];

    public static function probabilityAgainst(int $levelPlayerOne, int $againstLevelPlayerTwo): float
    {
        return 1 / (1 + (10 ** (($againstLevelPlayerTwo - $levelPlayerOne) / 400)));
    }

    public static function setNewLevel(Player $playerOne, int $againstLevelPlayerTwo, int $playerOneResult)
    {
        if (!in_array($playerOneResult, self::RESULT_POSSIBILITIES, true)) {
            trigger_error(sprintf('Invalid result. Expected %s', implode(' or ', self::RESULT_POSSIBILITIES)));
            return;
        }

        $playerOne->level += (int) (32 * ($playerOneResult - self::probabilityAgainst($playerOne->level, $againstLevelPlayerTwo)));
    }
}


$greg = new Player(400);
$jade = new Player(800);

echo sprintf(
    'Greg à %.2f%% chance de gagner face a Jade',
    Encounter::probabilityAgainst($greg->level, $jade->level) * 100
).PHP_EOL;

// Imaginons que Greg l'emporte tout de même.
Encounter::setNewLevel($greg, $jade->level, Encounter::RESULT_WINNER);
Encounter::setNewLevel($jade, $greg->level, Encounter::RESULT_LOSER);

echo sprintf(
    'les niveaux des joueurs ont évolués vers %s pour Greg et %s pour Jade',
    $greg->level,
    $jade->level
);

exit(0);
