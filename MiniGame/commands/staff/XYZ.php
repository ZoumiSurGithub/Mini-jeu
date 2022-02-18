<?php

namespace Zoumi\MiniGame\commands\staff;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use Zoumi\MiniGame\Main;
use Zoumi\MiniGame\Manager;

class XYZ extends Command {

    public function __construct(string $name, string $description = "", string $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player){
            if ($sender->hasPermission("use.xyz")){
                if (isset(Main::getInstance()->xyz[$sender->getName()])){
                    $sender->sendMessage(Manager::PREFIX_INFOS . "Vous ne verrez plus les coordonnées.");
                    unset(Main::getInstance()->xyz[$sender->getName()]);
                    return;
                }else{
                    $sender->sendMessage(Manager::PREFIX_INFOS . "Vous verrez désormais les cooronnées.");
                    Main::getInstance()->xyz[] = $sender->getName();
                    return;
                }
            }else{
                $sender->sendMessage(Manager::NOT_PERM);
                return;
            }
        }
    }

}