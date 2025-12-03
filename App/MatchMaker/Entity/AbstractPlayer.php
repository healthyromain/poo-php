<?php

declare(strict_types=1);

namespace App\MatchMaker\Entity;

abstract class AbstractPlayer
{
    /** @var string */
    protected $name;
    /** @var float */
    protected $ratio;

    public function __construct(string $name = 'anonymous', float $ratio = 400.0)
    {
        $this->name = $name;
        $this->ratio = $ratio;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRatio(): float
    {
        return $this->ratio;
    }

    protected function probabilityAgainst(AbstractPlayer $player): float
    {
        return 1 / (1 + (10 ** (($player->getRatio() - $this->getRatio()) / 400)));
    }

    /**
     * K-factor used to scale rating changes. Can be overridden by subclasses.
     *
     * @return int
     */
    protected function kFactor(): int
    {
        return 32;
    }

    public function updateRatioAgainst(AbstractPlayer $player, int $result)
    {
        $this->ratio += $this->kFactor() * ($result - $this->probabilityAgainst($player));
    }
}
