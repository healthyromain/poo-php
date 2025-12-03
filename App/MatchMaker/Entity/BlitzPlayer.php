<?php

declare(strict_types=1);

namespace App\MatchMaker\Entity;

class BlitzPlayer extends Player
{
    public function __construct($name = 'anonymous', $ratio = 1200.0)
    {
        parent::__construct($name, $ratio);
    }

    protected function kFactor(): int
    {
        return 128;
    }
}
