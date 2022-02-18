<?php

namespace Zoumi\MiniGame\commands\all;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use Zoumi\MiniGame\api\GameManager;
use Zoumi\MiniGame\Manager;

class Leave extends Command {

    public function __construct(string $name, string $description = "", string $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player){
            if (GameManager::isInGame($sender)) {
                GameManager::leavePlayer($sender);
                $sender->sendMessage(Manager::PREFIX_INFOS . "Vous avez bien quitter la partie dans laquel vous êtiez.");
                return;
            }else{
                $sender->sendMessage(Manager::PREFIX_ALERT . "Vous êtes dans aucune partie.");
                return;
            }
        }
    }

}