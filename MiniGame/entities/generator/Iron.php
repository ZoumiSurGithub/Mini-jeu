<?php

namespace Zoumi\MiniGame\entities\generator;

use pocketmine\entity\Animal;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use Zoumi\MiniGame\api\Bedwars;
use Zoumi\MiniGame\DataBase;
use Zoumi\MiniGame\tasks\bedwars\GeneratorTask;
use Zoumi\MiniGame\utils\Functions;

class Iron extends Animal {

    public const NETWORK_ID = self::CHICKEN;

    public $width = 0.7;
    public $height = 0.7;

    private $time = 20 * 3;

    private $timeGold = 20 * 6;

    public function __construct(Level $level, CompoundTag $nbt)
    {
        parent::__construct($level, $nbt);

        $this->setNameTag("§7- §fGénérateur de §7Fer §fet d'§eOr §7-");
        $this->setImmobile(true);
        $this->setNameTagAlwaysVisible(true);
        $this->setScale(0.001);
        $this->setImmobile(true);
    }

    public function getName(): string
    {
        return "Generator Iron And Gold";
    }

    public function onUpdate(int $currentTick): bool
    {
        return true;
    }

}