<?php

namespace Zoumi\MiniGame\commands\all\remake;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use Zoumi\MiniGame\Manager;

class Liste extends Command {

    public function __construct(string $name, string $description = "", string $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $players = [];
        foreach (Server::getInstance()->getOnlinePlayers() as $player){
            $players[] = $player->getName();
        }
        $sender->sendMessage(Manager::PREFIX . "Il y a §b" . count(Server::getInstance()->getOnlinePlayers()) . " §fjoueur(s) sur §eSun§fParadise:\n§e" . ($players ? implode("§f, §e", $players) : "Aucun") . "§f.");
    }

}