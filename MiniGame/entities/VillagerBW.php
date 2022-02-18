<?php

namespace Zoumi\MiniGame\entities;

use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\MoveActorAbsolutePacket;
use pocketmine\Server;

class VillagerBW extends \pocketmine\entity\Villager {

    public function __construct(Level $level, CompoundTag $nbt)
    {
        parent::__construct($level, $nbt);
        $this->setNameTagAlwaysVisible(true);
        $this->setNameTag("§6- §fShop §6-");
        $this->setScoreTag("§7[Tape moi§7]");
        $this->setImmobile(true);
    }

    public function getName(): string
    {
        return "Villageois";
    }

}