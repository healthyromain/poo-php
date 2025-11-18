<?php

declare(strict_types=1);

const RESULT_WINNER = 1;
const RESULT_LOSER = -1;
const RESULT_DRAW = 0;
const RESULT_POSSIBILITIES = [RESULT_WINNER, RESULT_LOSER, RESULT_DRAW];

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
    public function probabilityAgainst(int $levelPlayerOne, int $againstLevelPlayerTwo): float
    {
        return 1 / (1 + (10 ** (($againstLevelPlayerTwo - $levelPlayerOne) / 400)));
    }

    public function setNewLevel(Player $playerOne, int $againstLevelPlayerTwo, int $playerOneResult)
    {
        if (!in_array($playerOneResult, RESULT_POSSIBILITIES, true)) {
            trigger_error(sprintf('Invalid result. Expected %s', implode(' or ', RESULT_POSSIBILITIES)));
            return;
        }

        $playerOne->level += (int) (32 * ($playerOneResult - $this->probabilityAgainst($playerOne->level, $againstLevelPlayerTwo)));
    }
}

$greg = new Player(400);
$jade = new Player(800);
$encounter = new Encounter();

echo sprintf(
    'Greg à %.2f%% chance de gagner face a Jade',
    $encounter->probabilityAgainst($greg->level, $jade->level) * 100
).PHP_EOL;

// Imaginons que Greg l'emporte tout de même.
$encounter->setNewLevel($greg, $jade->level, RESULT_WINNER);
$encounter->setNewLevel($jade, $greg->level, RESULT_LOSER);

echo sprintf(
    'les niveaux des joueurs ont évolués vers %s pour Greg et %s pour Jade',
    $greg->level,
    $jade->level
);

exit(0);
