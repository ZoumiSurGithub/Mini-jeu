<?php

namespace Zoumi\MiniGame\commands\all;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use Zoumi\MiniGame\api\game\GameManager;
use Zoumi\MiniGame\api\game\PrivateGameManager;
use Zoumi\MiniGame\Main;
use Zoumi\MiniGame\Manager;
use Zoumi\MiniGame\utils\Functions;

class Code extends Command {

    public function __construct(string $name, string $description = "", string $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player){
            if ($sender->hasPermission(Manager::PERM_CREATE_PRIVATE_PARTIES)){
                if (Functions::getGameByHoster($sender->getName()) === false){
                    $sender->sendMessage(Manager::PREFIX_ALERT . "Vous n'avez aucune partie privée en cours.");
                    return;
                }else{
                    $sender->sendMessage(Manager::PREFIX_INFOS . "Le code de votre partie privée est §b" . Functions::getGameByHoster($sender->getName())->getCode() . "§f.");
                    return;
                }
            }else{
                $sender->sendMessage(Manager::NOT_PERM);
                return;
            }
        }
    }

}