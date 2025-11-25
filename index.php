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

class Lobby
{
    /** @var array<QueuingPlayer> */
    public array $queuingPlayers = [];

    public function findOponents(QueuingPlayer $player): array
    {
        $minLevel = round($player->getRatio() / 100);
        $maxLevel = $minLevel + $player->getRange();

        return array_filter($this->queuingPlayers, static function (QueuingPlayer $potentialOponent) use ($minLevel, $maxLevel, $player) {
            $playerLevel = round($potentialOponent->getRatio() / 100);

            return $player !== $potentialOponent && ($minLevel <= $playerLevel) && ($playerLevel <= $maxLevel);
        });
    }

    public function addPlayer(Player $player): void
    {
        // Lorsqu’un joueur est ajouté, on crée un QueuingPlayer
        $this->queuingPlayers[] = new QueuingPlayer($player->getName(), $player->getRatio());
    }

    public function addPlayers(Player ...$players): void
    {
        foreach ($players as $player) {
            $this->addPlayer($player);
        }
    }
}

class Player
{
    protected string $name;
    protected float $ratio;

    public function __construct(string $name, float $ratio = 400.0)
    {
        $this->name = $name;
        $this->ratio = $ratio;
    }

    public function getName(): string
    {
        return $this->name;
    }

    private function probabilityAgainst(self $player): float
    {
        return 1 / (1 + (10 ** (($player->getRatio() - $this->getRatio()) / 400)));
    }

    public function updateRatioAgainst(self $player, int $result): void
    {
        $this->ratio += 32 * ($result - $this->probabilityAgainst($player));
    }

    public function getRatio(): float
    {
        return $this->ratio;
    }
}

/**
 * Classe QueuingPlayer qui hérite de Player
 * et ajoute la propriété range (portée de recherche d’adversaire)
 */
class QueuingPlayer extends Player
{
    protected int $range;

    public function __construct(string $name, float $ratio = 400.0, int $range = 1)
    {
        // On appelle le constructeur du parent pour initialiser name et ratio
        parent::__construct($name, $ratio);
        $this->range = $range;
    }

    public function getRange(): int
    {
        return $this->range;
    }
}

// --- Test ---
$greg = new Player('greg', 400);
$jade = new Player('jade', 476);

$lobby = new Lobby();
$lobby->addPlayers($greg, $jade);

// Résultat attendu : un QueuingPlayer avec range=1, name=jade, ratio=476
var_dump($lobby->findOponents($lobby->queuingPlayers[0]));

exit(0);