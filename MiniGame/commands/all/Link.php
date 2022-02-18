<?php

namespace Zoumi\MiniGame\commands\all;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use Zoumi\MiniGame\api\Users;
use Zoumi\MiniGame\Manager;

class Link extends Command {

    public function __construct(string $name, string $description = "", string $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player){
            if (!Users::isLinked($sender)){
                $sender->sendMessage(Manager::PREFIX_ALERT . "Ne partager pas votre code aux autres !");
                $sender->sendMessage(Manager::PREFIX_INFOS . "Votre code est §b". Users::getCode($sender) . "§f.");
                return;
            }else{
                $sender->sendMessage(Manager::PREFIX_INFOS . "Votre compte est déjà lié à un compte discord.");
                return;
            }
        }
    }

}