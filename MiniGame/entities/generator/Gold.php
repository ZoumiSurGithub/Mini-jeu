<?php

namespace Zoumi\MiniGame\entities\generator;

use pocketmine\entity\Animal;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use Zoumi\MiniGame\api\Bedwars;
use Zoumi\MiniGame\DataBase;
use Zoumi\MiniGame\utils\Functions;

class Gold extends Animal {

    public const NETWORK_ID = self::CHICKEN;

    public $width = 0.7;
    public $height = 0.7;

    private $time = 20 * 30;

    public function __construct(Level $level, CompoundTag $nbt)
    {
        parent::__construct($level, $nbt);

        $this->setNameTag("§e- §fGénérateur d'§eOr §e-");
        $this->setImmobile(true);
        $this->setNameTagAlwaysVisible(true);
        $this->setScale(0.001);
        $this->setImmobile(true);
    }

    public function getName(): string
    {
        return "Generator Gold";
    }

    public function onUpdate(int $currentTick): bool
    {
        if (--$this->time === 0){
            $pos = explode(";", Bedwars::$genPos['Gold']);
            foreach ($pos as $po){
                $p = explode(":", $po);
                Functions::dropItem(new Vector3((float)$p[0], (int)$p[1], (float)$p[2]), Item::get(Item::GOLD_INGOT, 0, 1), $this->getLevel()->getFolderName());
            }
            $this->time = 20 * 30;
        }else{
            $this->setNameTag("§e- §fProchain spawn dans §7" . ($this->time / 20) . " §fseconde §e-");
        }
        return true;
    }

}