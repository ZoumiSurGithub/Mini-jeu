<?php

namespace Zoumi\MiniGame\commands\staff;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Server;
use Zoumi\MiniGame\Manager;

class Entity extends Command {

    public function __construct(string $name, string $description = "", string $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender->hasPermission("use.entity")){
            if (!isset($args[0])){
                $sender->sendMessage(Manager::PREFIX_ALERT . "Veuillez faire /entity death|kill|kd|money.");
                return;
            }
            if (strtolower($args[0]) === "death"){
                $nbt = \pocketmine\entity\Entity::createBaseNBT(new Position(-469.5, 67, -377.5, Server::getInstance()->getLevelByName("spawn")), null, 0, 0);
                $entity = \pocketmine\entity\Entity::createEntity("Death", Server::getInstance()->getDefaultLevel(), $nbt);
                $entity->spawnToAll();
                return;
            }elseif (strtolower($args[0]) === "kill"){
                $nbt = \pocketmine\entity\Entity::createBaseNBT(new Position(-467.5, 67, -381.5, Server::getInstance()->getLevelByName("spawn")), null, 0, 0);
                $entity = \pocketmine\entity\Entity::createEntity("Kill", Server::getInstance()->getDefaultLevel(), $nbt);
                $entity->spawnToAll();
                return;
            }elseif (strtolower($args[0]) === "kd"){
                $nbt = \pocketmine\entity\Entity::createBaseNBT(new Position(-470.5, 67, -373.5, Server::getInstance()->getLevelByName("spawn")), null, 0, 0);
                $entity = \pocketmine\entity\Entity::createEntity("Kd", Server::getInstance()->getDefaultLevel(), $nbt);
                $entity->spawnToAll();
                return;
            }elseif (strtolower($args[0]) === "money"){
                $nbt = \pocketmine\entity\Entity::createBaseNBT(new Position(-469.5, 67, -369.5, Server::getInstance()->getLevelByName("spawn")), null, 0, 0);
                $entity = \pocketmine\entity\Entity::createEntity("Money", Server::getInstance()->getDefaultLevel(), $nbt);
                $entity->spawnToAll();
                return;
            }
        }
    }

}