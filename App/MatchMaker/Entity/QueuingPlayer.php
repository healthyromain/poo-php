<?php

declare(strict_types=1);

namespace App\MatchMaker\Entity;

use App\MatchMaker\Entity\QueuingPlayerInterface;

class QueuingPlayer extends Player implements QueuingPlayerInterface
{
    /** @var int */
    protected $range;

    public function __construct(PlayerInterface $player, $range = 1)
    {
        parent::__construct($player->getName(), $player->getRatio());
        $this->range = $range;
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
