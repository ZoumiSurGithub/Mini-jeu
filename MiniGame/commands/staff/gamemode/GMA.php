<?php

namespace Zoumi\MiniGame\commands\staff\gamemode;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use Zoumi\MiniGame\api\Users;
use Zoumi\MiniGame\Main;
use Zoumi\MiniGame\Manager;

class GMA extends Command {

    public function __construct(string $name, string $description = "", string $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player){
            if ($sender->hasPermission("use.gma")){
                if ($sender->getGamemode() === 2){
                    $sender->sendMessage(Manager::PREFIX_ALERT . "Vous êtes déjà en aventure.");
                    Users::setGamemode($sender, 0);
                    Main::getInstance()->playerCache[$sender->getName()]["gamemode"] = 0;
                    return;
                }else{
                    $sender->setGamemode(2);
                    Users::setGamemode($sender, 0);
                    Main::getInstance()->playerCache[$sender->getName()]["gamemode"] = 0;
                    $sender->addTitle("§eGamemode", "Vous êtes désormais en aventure.");
                    return;
                }
            }else{
                $sender->sendMessage(Manager::NOT_PERM);
                return;
            }
        }
    }

}