<?php

namespace Zoumi\MiniGame\entities;

use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\MoveActorAbsolutePacket;
use pocketmine\Player;
use pocketmine\Server;
use Zoumi\MiniGame\api\Cosmetique;
use Zoumi\MiniGame\items\Fireworks;
use Zoumi\MiniGame\utils\Functions;

class VillagerTest extends \pocketmine\entity\Villager {

    public function __construct(Level $level, CompoundTag $nbt)
    {
        parent::__construct($level, $nbt);
        $this->setNameTag("§3- §fEffect Test §3-");
        $this->setScoreTag("§7Clique droit pour changer d'effet.");
        $this->setNameTagAlwaysVisible(true);
        $this->setImmobile(true);
    }

    public function getName(): string
    {
        return "Villageois";
    }

    public function sendEffectKill(Player $player, VillagerTest $entity){
        if (Cosmetique::isEnable($player, "kill-thunder")){
            Functions::sendEffect($player, $entity, "minecraft:lightning_bolt");
            Functions::sendSound($player, "ambient.weather.thunder");
        }elseif (Cosmetique::isEnable($player, "kill-firework")){
            $fw = Item::get(Item::FIREWORKS);
            $fw->addExplosion(Fireworks::TYPE_HUGE_SPHERE, Fireworks::COLOR_WHITE, "", false, false);
            $fw->setFlightDuration(0.1);
            $level = Server::getInstance()->getDefaultLevel();
            $nbt = FireworksRocket::createBaseNBT(new Vector3($entity->getX(), $entity->getY(), $entity->getZ()), new Vector3(0.001, 0.05, 0.001), lcg_value() * 360, 90);
            $entity = FireworksRocket::createEntity("FireworksRocket", $level, $nbt, $fw);
            if ($entity instanceof FireworksRocket){
                $entity->spawnTo($player);
            }
        }elseif (Cosmetique::isEnable($player, "kill-creeper")){
            $fw = Item::get(Item::FIREWORKS);
            $fw->addExplosion(Fireworks::TYPE_CREEPER_HEAD, Fireworks::COLOR_GREEN, "", false, false);
            $fw->setFlightDuration(0.1);
            $level = Server::getInstance()->getDefaultLevel();
            $nbt = FireworksRocket::createBaseNBT(new Vector3($entity->getX(), $entity->getY(), $entity->getZ()), new Vector3(0.001, 0.05, 0.001), lcg_value() * 360, 90);
            $entity = FireworksRocket::createEntity("FireworksRocket", $level, $nbt, $fw);
            if ($entity instanceof FireworksRocket){
                $entity->spawnTo($player);
            }
        }elseif (Cosmetique::isEnable($player, "kill-explosion")){
            Functions::sendFakeExplosion($player, $entity);
        }
    }

    public function sendEffectDeath($player, $entity){
        if (Cosmetique::isEnable($player, "death-thunder")){
                Functions::sendEffect($player, $entity, "minecraft:lightning_bolt");
                Functions::sendSound($player, "ambient.weather.thunder");
        }elseif (Cosmetique::isEnable($player, "death-firework")){
            $fw = Item::get(Item::FIREWORKS);
            $fw->addExplosion(Fireworks::TYPE_HUGE_SPHERE, Fireworks::COLOR_WHITE, "", false, false);
            $fw->setFlightDuration(1);
            $level = Server::getInstance()->getDefaultLevel();
            $nbt = FireworksRocket::createBaseNBT(new Vector3($entity->getX(), $entity->getY(), $entity->getZ()), new Vector3(0.001, 0.05, 0.001), lcg_value() * 360, 90);
            $entity = FireworksRocket::createEntity("FireworksRocket", $level, $nbt, $fw);
            if ($entity instanceof FireworksRocket){
                $entity->spawnTo($player);
            }
        }elseif (Cosmetique::isEnable($player, "death-creeper")){
            $fw = Item::get(Item::FIREWORKS);
            $fw->addExplosion(Fireworks::TYPE_CREEPER_HEAD, Fireworks::COLOR_GREEN, "", false, false);
            $fw->setFlightDuration(1);
            $level = Server::getInstance()->getDefaultLevel();
            $nbt = FireworksRocket::createBaseNBT(new Vector3($entity->getX(), $entity->getY(), $entity->getZ()), new Vector3(0.001, 0.05, 0.001), lcg_value() * 360, 90);
            $entity = FireworksRocket::createEntity("FireworksRocket", $level, $nbt, $fw);
            if ($entity instanceof FireworksRocket){
                $entity->spawnTo($player);
            }
        }elseif (Cosmetique::isEnable($player, "death-explosion")){
            Functions::sendFakeExplosion($player, $entity);
        }
    }

    public function sendEffectJoin(Player $player){
        if (Cosmetique::isEnable($player, "join-thunder")){
            foreach ($player->getLevel()->getPlayers() as $pla) {
                Functions::sendEffect($pla, $player, "minecraft:lightning_bolt");
                Functions::sendSound($pla, "ambient.weather.thunder");
            }
        }elseif (Cosmetique::isEnable($player, "join-firework")){
            $fw = Item::get(Item::FIREWORKS);
            $fw->addExplosion(Fireworks::TYPE_HUGE_SPHERE, Fireworks::COLOR_WHITE, "", false, false);
            $fw->setFlightDuration(1);
            $level = Server::getInstance()->getDefaultLevel();
            $nbt = FireworksRocket::createBaseNBT(new Vector3(-440.5, 68, -373.5), new Vector3(0.001, 0.05, 0.001), lcg_value() * 360, 90);
            $entity = FireworksRocket::createEntity("FireworksRocket", $level, $nbt, $fw);
            if ($entity instanceof FireworksRocket){
                $entity->spawnToAll();
            }
        }elseif (Cosmetique::isEnable($player, "join-creeper")){
            $fw = Item::get(Item::FIREWORKS);
            $fw->addExplosion(Fireworks::TYPE_CREEPER_HEAD, Fireworks::COLOR_GREEN, "", false, false);
            $fw->setFlightDuration(1);
            $level = Server::getInstance()->getDefaultLevel();
            $nbt = FireworksRocket::createBaseNBT(new Vector3(-440.5, 68, -373.5), new Vector3(0.001, 0.05, 0.001), lcg_value() * 360, 90);
            $entity = FireworksRocket::createEntity("FireworksRocket", $level, $nbt, $fw);
            if ($entity instanceof FireworksRocket){
                $entity->spawnToAll();
            }
        }elseif (Cosmetique::isEnable($player, "join-explosion")){
            foreach ($player->getLevel()->getPlayers() as $pla) {
                Functions::sendFakeExplosion($pla, $player);
            }
        }
    }

    public function sendEffectQuit(Player $player){
        if (Cosmetique::isEnable($player, "quit-thunder")){
            foreach ($player->getLevel()->getPlayers() as $pla) {
                Functions::sendEffect($pla, $player, "minecraft:lightning_bolt");
                Functions::sendSound($pla, "ambient.weather.thunder");
            }
        }elseif (Cosmetique::isEnable($player, "quit-firework")){
            $fw = Item::get(Item::FIREWORKS);
            $fw->addExplosion(Fireworks::TYPE_HUGE_SPHERE, Fireworks::COLOR_WHITE, "", false, false);
            $fw->setFlightDuration(1);
            $level = Server::getInstance()->getDefaultLevel();
            $nbt = FireworksRocket::createBaseNBT(new Vector3($player->getX(), $player->getY(), $player->getZ()), new Vector3(0.001, 0.05, 0.001), lcg_value() * 360, 90);
            $entity = FireworksRocket::createEntity("FireworksRocket", $level, $nbt, $fw);
            if ($entity instanceof FireworksRocket){
                $entity->spawnToAll();
            }
        }elseif (Cosmetique::isEnable($player, "quit-creeper")){
            $fw = Item::get(Item::FIREWORKS);
            $fw->addExplosion(Fireworks::TYPE_CREEPER_HEAD, Fireworks::COLOR_GREEN, "", false, false);
            $fw->setFlightDuration(1);
            $level = Server::getInstance()->getDefaultLevel();
            $nbt = FireworksRocket::createBaseNBT(new Vector3($player->getX(), $player->getY(), $player->getZ()), new Vector3(0.001, 0.05, 0.001), lcg_value() * 360, 90);
            $entity = FireworksRocket::createEntity("FireworksRocket", $level, $nbt, $fw);
            if ($entity instanceof FireworksRocket){
                $entity->spawnToAll();
            }
        }elseif (Cosmetique::isEnable($player, "quit-explosion")){
            foreach ($player->getLevel()->getPlayers() as $pla) {
                Functions::sendFakeExplosion($pla, $player);
            }
        }
    }

}