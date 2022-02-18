<?php

namespace Zoumi\MiniGame\commands\staff\gamemode;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use Zoumi\MiniGame\api\Users;
use Zoumi\MiniGame\Main;
use Zoumi\MiniGame\Manager;

class GMSTAFF extends Command {

    public function __construct(string $name, string $description = "", string $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player){
            if ($sender->hasPermission("use.gmstaff")){
                if (Users::getGamemode($sender) === "1"){
                    $sender->sendMessage(Manager::PREFIX_ALERT . "Vous êtes déjà en staff.");
                    Users::setGamemode($sender, 1);
                    Main::getInstance()->playerCache[$sender->getName()]["gamemode"] = 1;
                    return;
                }else{
                    $sender->setGamemode(1);
                    Users::setGamemode($sender, 1);
                    Main::getInstance()->playerCache[$sender->getName()]["gamemode"] = 1;
                    $sender->addTitle("§eGamemode", "Vous êtes désormais en staff.");
                    return;
                }
            }else{
                $sender->sendMessage(Manager::NOT_PERM);
                return;
            }
        }
    }

}