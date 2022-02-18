<?php

namespace Zoumi\MiniGame\commands\all;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use Zoumi\MiniGame\api\GameManager;
use Zoumi\MiniGame\api\NavigatorManager;
use Zoumi\MiniGame\Manager;

class Navigator extends Command {

    public function __construct(string $name, string $description = "", string $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player){
            if (!GameManager::isInGame($sender)){
                NavigatorManager::sendNavigator($sender);
                $sender->sendMessage(Manager::PREFIX_INFOS . "Les items de navigation vous on été donner.");
                return;
            }else{
                $sender->sendMessage(Manager::PREFIX_ALERT . "Impossible de faire ceci lors d'une partie.");
                return;
            }
        }
    }

}