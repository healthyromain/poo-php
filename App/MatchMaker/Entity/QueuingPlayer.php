<?php

declare(strict_types=1);

namespace App\MatchMaker\Entity;

use App\MatchMaker\Entity\QueuingPlayerInterface;
use App\MatchMaker\Entity\PlayerInterface;

class QueuingPlayer implements QueuingPlayerInterface
{
    /** @var PlayerInterface */
    protected $player;

    /** @var int */
    protected $range;

    public function __construct(PlayerInterface $player, $range = 1)
    {
        $this->player = $player;
        $this->range = $range;
    }

    public function getName(): string
    {
        return $this->player->getName();
    }

    public function getRatio(): float
    {
        return $this->player->getRatio();
    }

    public function updateRatioAgainst(PlayerInterface $player, int $result)
    {
        $this->player->updateRatioAgainst($player, $result);
    }

    public function getRange(): int
    {
        return $this->range;
    }

    public function upgradeRange()
    {
        $this->range = min($this->range + 1, 40);
    }
}
