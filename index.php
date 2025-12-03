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

namespace App\MatchMaker\Service {

    use App\MatchMaker\Entity\AbstractPlayer;
    use App\MatchMaker\Entity\QueuingPlayer;

    class Lobby
    {
        /** @var \App\MatchMaker\Entity\QueuingPlayer[] */
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

}

namespace App\MatchMaker\Entity {

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

        abstract public function getName(): string;

        abstract public function getRatio(): float;

        abstract protected function probabilityAgainst(AbstractPlayer $player): float;

        abstract public function updateRatioAgainst(AbstractPlayer $player, int $result);

        /**
         * K-factor used to scale rating changes. Can be overridden by subclasses.
         *
         * @return int
         */
        protected function kFactor()
        {
            return 32;
        }
    }

    class Player extends AbstractPlayer
    {
        public function getName(): string
        {
            return $this->name;
        }

        protected function probabilityAgainst(AbstractPlayer $player): float
        {
            return 1 / (1 + (10 ** (($player->getRatio() - $this->getRatio()) / 400)));
        }

        public function updateRatioAgainst(AbstractPlayer $player, int $result)
        {
            $this->ratio += $this->kFactor() * ($result - $this->probabilityAgainst($player));
        }

        public function getRatio(): float
        {
            return $this->ratio;
        }
    }

    class QueuingPlayer extends Player
    {
        /** @var int */
        protected $range;

        public function __construct(AbstractPlayer $player, $range = 1)
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

    class BlitzPlayer extends Player
    {
        public function __construct($name = 'anonymous', $ratio = 1200.0)
        {
            parent::__construct($name, $ratio);
        }

        public function kFactor()
        {
            return 128;
        }

        public function updateRatioAgainst(AbstractPlayer $player, int $result)
        {
            $this->ratio += $this->kFactor() * ($result - $this->probabilityAgainst($player));
        }
    }

}

namespace {
    // --- Test ---
    $greg = new \App\MatchMaker\Entity\Player('greg', 400);
    $jade = new \App\MatchMaker\Entity\Player('jade', 476);

    $lobby = new \App\MatchMaker\Service\Lobby();
    $lobby->addPlayers($greg, $jade);

    var_dump($lobby->findOponents($lobby->queuingPlayers[0]));

    echo "\n--- BlitzPlayer Test ---\n";
    $blitzAlice = new \App\MatchMaker\Entity\BlitzPlayer('Alice');
    $blitzBob = new \App\MatchMaker\Entity\BlitzPlayer('Bob', 1250);

    echo "Alice initial ratio: " . $blitzAlice->getRatio() . "\n";
    echo "Bob initial ratio: " . $blitzBob->getRatio() . "\n";

    $blitzAlice->updateRatioAgainst($blitzBob, 1);
    $blitzBob->updateRatioAgainst($blitzAlice, 0);

    echo "Alice ratio after win: " . $blitzAlice->getRatio() . "\n";
    echo "Bob ratio after loss: " . $blitzBob->getRatio() . "\n";

    exit(0);
}