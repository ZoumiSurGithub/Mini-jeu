<?php

namespace Zoumi\MiniGame\commands\all;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use Zoumi\MiniGame\tasks\async\VoteAsyncTask;

class Vote extends Command {

    public function __construct(string $name, string $description = "", string $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player){
            Server::getInstance()->getAsyncPool()->submitTask(new VoteAsyncTask($sender->getName()));
        }
    }

}