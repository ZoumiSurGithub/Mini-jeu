<?php

namespace Zoumi\MiniGame\tasks\async;

use pocketmine\Player;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Internet;
use Zoumi\MiniGame\api\Jeton;
use Zoumi\MiniGame\api\Money;
use Zoumi\MiniGame\Manager;

class VoteAsyncTask extends AsyncTask {

    private $player;

    public function __construct(string $player)
    {
        $this->player = $player;
    }

    public function onRun()
    {
        $result = Internet::getURL("https://minecraftpocket-servers.com/api/?object=votes&element=claim&key=W1AmZGXN0SHjF1CttC8JAyU0dbopFESBCe&username=" . str_replace([" ", "_"], ["+", "+"], $this->player));
        if($result === "1") Internet::getURL("https://minecraftpocket-servers.com/api/?action=post&object=votes&element=claim&key=W1AmZGXN0SHjF1CttC8JAyU0dbopFESBCe&username=" . str_replace([" ", "_"], ["+", "+"], $this->player));
        $this->setResult($result);
    }

    public function onCompletion(Server $server):void
    {
        $player = $server->getPlayer($this->player);
        if ($player instanceof Player) {
            switch ($this->getResult()) {
                case 0:
                    $player->sendMessage(Manager::PREFIX_ALERT . "Vous n'avez pas encore voter pour §eSun§fParadise§f.");
                    break;
                case 1:
                    $player->sendMessage(Manager::PREFIX_INFOS . "Merci d'avoir voter pour §eSun§fParadise, vous venez d'obtenir §e100\u{E102}§f.");
                    Money::addMoney($player, 100);
                    break;
                default:
                    $player->sendMessage(Manager::PREFIX_ALERT . "Vous avez déjà voter pour §eSun§fParadise §caujourd'hui.");
                    break;
            }
        }
    }

}