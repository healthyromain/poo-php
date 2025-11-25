<?php

/*
 * This file is part of the OpenClassRoom PHP Object Course.
 *
 * (c) Grégoire Hébert <contact@gheb.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

final class Lobby
{
    /** @var array<QueuingPlayer> */
    public $queuingPlayers = [];

    public function findOponents(QueuingPlayer $player): array
    {
        $minLevel = round($player->getRatio() / 100);
        $maxLevel = $minLevel + $player->getRange();

        return array_filter($this->queuingPlayers, static function (QueuingPlayer $potentialOponent) use ($minLevel, $maxLevel, $player) {
            $playerLevel = round($potentialOponent->getRatio() / 100);

            return $player !== $potentialOponent && ($minLevel <= $playerLevel) && ($playerLevel <= $maxLevel);
        });
    }

    public function addPlayer(AbstractPlayer $player)
    {
        $this->queuingPlayers[] = new QueuingPlayer($player->getName(), $player->getRatio());
    }

    public function addPlayers(AbstractPlayer ...$players)
    {
        foreach ($players as $player) {
            $this->addPlayer($player);
        }
    }
}

abstract class AbstractPlayer
{
    /** @var string */
    protected $name;
    /** @var float */
    protected $ratio;

    public function __construct(string $name, float $ratio = 400.0)
    {
        $this->name = $name;
        $this->ratio = $ratio;
    }

    public function getName(): string
    {
        return $this->name;
    }

    private function probabilityAgainst(AbstractPlayer $player): float
    {
        return 1 / (1 + (10 ** (($player->getRatio() - $this->getRatio()) / 400)));
    }

    /**
     * Get the K-factor for Elo rating calculation.
     * Override this method to change the speed of ratio evolution.
     * 
     * @return int The K-factor (default: 32)
     */
    protected function getKFactor(): int
    {
        return 32;
    }

    public function updateRatioAgainst(AbstractPlayer $player, int $result)
    {
        $this->ratio += $this->getKFactor() * ($result - $this->probabilityAgainst($player));
    }

    public function getRatio(): float
    {
        return $this->ratio;
    }
}

final class Player extends AbstractPlayer
{
}

final class QueuingPlayer extends AbstractPlayer
{
    /** @var int */
    protected $range;

    public function __construct(string $name, float $ratio = 400.0, int $range = 1)
    {
        parent::__construct($name, $ratio);
        $this->range = $range;
    }

    public function getRange(): int
    {
        return $this->range;
    }
}

final class BlitzPlayer extends AbstractPlayer
{
    public function __construct(string $name, float $ratio = 1200.0)
    {
        parent::__construct($name, $ratio);
    }

    /**
     * Override K-factor for 4x faster ratio evolution
     * 
     * @return int K-factor of 128 (4 * 32)
     */
    protected function getKFactor(): int
    {
        return 128;
    }
}

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