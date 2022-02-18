<?php

namespace Zoumi\MiniGame\commands\all;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use Zoumi\MiniGame\api\GameManager;
use Zoumi\MiniGame\Manager;
use Zoumi\MiniGame\utils\Functions;

class Spawn extends Command {

    public function __construct(string $name, string $description = "", string $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player){
            if (!GameManager::isInGame($sender)){
                Functions::goToSpawn($sender);
                $sender->sendMessage(Manager::PREFIX_INFOS . "Vous venez d'être téléporter au spawn.");
                return;
            }else{
                $sender->sendMessage(Manager::ALREADY_IN_GAME);
                return;
            }
        }
    }

}