<?php

declare(strict_types=1);

namespace App\MatchMaker\Service;

use App\MatchMaker\Entity\PlayerInterface;
use App\MatchMaker\Entity\QueuingPlayerInterface;

use App\MatchMaker\Service\LobbyInterface;

class Lobby implements LobbyInterface
{
    /** @var QueuingPlayerInterface[] */
    public $queuingPlayers = [];

    public function findOponents(QueuingPlayerInterface $player): array
    {
        $minLevel = round($player->getRatio() / 100);
        $maxLevel = $minLevel + $player->getRange();

        return array_filter($this->queuingPlayers, static function (QueuingPlayerInterface $potentialOponent) use ($minLevel, $maxLevel, $player) {
            $playerLevel = round($potentialOponent->getRatio() / 100);

            return $player !== $potentialOponent && ($minLevel <= $playerLevel) && ($playerLevel <= $maxLevel);
        });
    }

    public function addPlayer(PlayerInterface $player)
    {
        $this->queuingPlayers[] = new \App\MatchMaker\Entity\QueuingPlayer($player);
    }

    public function addPlayers(PlayerInterface ...$players)
    {
        foreach ($players as $player) {
            $this->addPlayer($player);
        }
    }
}
