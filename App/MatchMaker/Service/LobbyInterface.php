<?php

declare(strict_types=1);

namespace App\MatchMaker\Service;

use App\MatchMaker\Entity\PlayerInterface;
use App\MatchMaker\Entity\QueuingPlayerInterface;

interface LobbyInterface
{
    public function findOponents(QueuingPlayerInterface $player): array;

    public function addPlayer(PlayerInterface $player);

    public function addPlayers(PlayerInterface ...$players);
}
