<?php

declare(strict_types=1);

namespace App\MatchMaker\Service;

use App\MatchMaker\Entity\AbstractPlayer;
use App\MatchMaker\Entity\QueuingPlayer;

class Lobby
{
    /** @var QueuingPlayer[] */
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
        $this->queuingPlayers[] = new QueuingPlayer($player);
    }

    public function addPlayers(AbstractPlayer ...$players)
    {
        foreach ($players as $player) {
            $this->addPlayer($player);
        }
    }
}
