<?php

namespace Zoumi\MiniGame\entities\ctf;

use pocketmine\entity\Animal;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use Zoumi\MiniGame\DataBase;

class FlagRed extends Animal {

    public const NETWORK_ID = self::CHICKEN;

    public $width = 0.7;
    public $height = 0.7;

    public function __construct(Level $level, CompoundTag $nbt)
    {
        parent::__construct($level, $nbt);

        $this->setNameTag("§l§fDrapeau §cRouge");
        $this->setNameTagAlwaysVisible(true);
        $this->setScale(0.001);
        $this->setImmobile(true);
    }

    public function getName(): string
    {
        return "FlagRed";
    }

    public function onUpdate(int $currentTick): bool
    {
        return true;
    }

}