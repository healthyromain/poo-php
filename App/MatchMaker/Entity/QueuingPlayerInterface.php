<?php

declare(strict_types=1);

namespace App\MatchMaker\Entity;

interface QueuingPlayerInterface extends PlayerInterface
{
    public function getRange(): int;

    public function upgradeRange();
}
