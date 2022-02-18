<?php

namespace Zoumi\MiniGame\commands\staff;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use Zoumi\MiniGame\utils\Functions;

class PlaySound extends Command {

    public function __construct(string $name, string $description = "", string $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player){
            if ($sender->hasPermission("use.playsound")){
                if (isset($args[0])){
                    Functions::sendSound($sender, $args[0]);
                    return;
                }
            }
        }
    }

}