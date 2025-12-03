<?php

declare(strict_types=1);

namespace App\MatchMaker\Entity;

interface PlayerInterface
{
    public function getName(): string;

    public function getRatio(): float;

    public function updateRatioAgainst(PlayerInterface $player, int $result);
}
