<?php

namespace Zoumi\MiniGame\tasks\bedwars;

use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use Zoumi\MiniGame\api\game\Bedwars;
use Zoumi\MiniGame\Main;
use Zoumi\MiniGame\utils\Functions;

class GeneratorTask extends Task {

    /** @var string $id */
    private $id;
    /** @var array $gen */
    public static $gen = [];

    public function __construct(string $id)
    {
        $this->id = $id;
        self::$gen[$id] = [
            "Iron" => 3,
            "Gold" => 5,
            "Diamond" => 30,
            "Emerald" => 60
        ];
    }

    public function onRun(int $currentTick)
    {
        $id = $this->id;
        if (isset(Bedwars::$bedwars[$id])){
            if (--self::$gen[$id]["Iron"] === 0) {
                $pos = explode(";", Bedwars::$genPos["Iron"]);
                foreach ($pos as $po) {
                    $pos = explode(":", $po);
                    Functions::dropItem(new Vector3((float)$pos[0], (int)$pos[1], (float)$pos[2]), Item::get(Item::IRON_INGOT), $id);
                }
                self::$gen[$id]["Iron"] = 3;
            }
            if (--self::$gen[$id]["Gold"] === 0) {
                $pos = explode(";", Bedwars::$genPos["Iron"]);
                foreach ($pos as $po) {
                    $pos = explode(":", $po);
                    Functions::dropItem(new Vector3((float)$pos[0], (int)$pos[1], (float)$pos[2]), Item::get(Item::GOLD_INGOT), $id);
                }
                self::$gen[$id]["Gold"] = 5;
            }
            if (--self::$gen[$id]["Diamond"] === 0) {
                $pos = explode(";", Bedwars::$genPos["Diamond"]);
                foreach ($pos as $po) {
                    $pos = explode(":", $po);
                    Functions::dropItem(new Vector3((float)$pos[0], (int)$pos[1], (float)$pos[2]), Item::get(Item::DIAMOND), $id);
                }
                self::$gen[$id]["Diamond"] = 30;
            }
            if (--self::$gen[$id]["Emerald"] === 0) {
                $pos = explode(";", Bedwars::$genPos["Emerald"]);
                foreach ($pos as $po) {
                    $pos = explode(":", $po);
                    Functions::dropItem(new Vector3((float)$pos[0], (int)$pos[1], (float)$pos[2]), Item::get(Item::EMERALD), $id);
                }
                self::$gen[$id]["Emerald"] = 60;
            }

        }else return Main::getInstance()->getScheduler()->cancelTask($this->getTaskId());
    }

}