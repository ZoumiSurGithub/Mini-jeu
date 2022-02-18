<?php

namespace Zoumi\MiniGame\api\game;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\Server;
use Zoumi\MiniGame\api\NavigatorManager;
use Zoumi\MiniGame\api\Webhook;
use Zoumi\MiniGame\Main;
use Zoumi\MiniGame\Manager;
use Zoumi\MiniGame\tasks\async\CopyWorld;
use Zoumi\MiniGame\utils\Functions;

class HikabrainInstance extends GameManager{

    public static $count = 0;

    /** @var array $scoreboard */
    public static $scoreboard = [];

    private $waitingRoom;

    public function __construct(Player $player, string $type, bool $private)
    {
        $player->sendMessage(Manager::CREATE_PARTIES);
        /* Base */
        $count = self::$count = self::$count + 1;
        $this->id = "Hikabrain-{$type}-{$count}";
        $countRoom = Main::$waitingRoomCount = Main::$waitingRoomCount + 1;
        $this->waitingRoom = "WaitingRoom$countRoom";
        /* Joueur maximum */
        $maxPlayers = explode("v", $type);
        if ($maxPlayers[0] === "1" && $maxPlayers[1] === "1"){
            $maxPlayers = 2;
            $minPlayers = 2;
        }else {
            $maxPlayers = $maxPlayers[0] * $maxPlayers[1];
            $minPlayers = $maxPlayers / 2;
        }
        $maxPlayers = intval($maxPlayers);
        /* Initialisation des données */
        $this->setGameType("Hikabrain");
        $this->setHoster($player);
        $this->setMaxPlayersCount($maxPlayers);
        $this->setMinPlayersCount($minPlayers);
        $this->setPlayersCount(1);
        $this->setStatus("§6en attente");
        $this->setType($type);
        /* Private */
        $this->setPrivate($private);
        if ($private){
            $this->setCode($this->genCode());
            $player->sendMessage(Manager::VIEW_CODE);
        }
        /* Initialisation des équipes */
        $this->setMaxTeam(2);
        $this->initTeam([
            "Rouge" => [
                "players" => [],
                "points" => 0,
                "maxPlayers" => $maxPlayers / 2
            ],
            "Bleu" => [
                "players" => [],
                "points" => 0,
                "maxPlayers" => $maxPlayers / 2
            ]
        ]);
        $this->addPlayer($player, $player->getName());
        /* Logs */
        Webhook::sendGameLogs("> Un hikabrain vient d'être créer ayant pour id **Hikabrain-{$type}-{$count}** __**{$type}**__.");
        Server::getInstance()->getAsyncPool()->submitTask(new CopyWorld($this->waitingRoom, "Hikabrain", $this->id, $player->getName()));
    }

    public function getId(): string{
        return $this->id;
    }

    public function getWaitingRoom(): string{
        return $this->waitingRoom;
    }

    public function addNewPlayer(Player $player){
        $this->setPlayersCount($this->getPlayersCount() + 1);
        $this->addPlayer($player, 0);
        if ($this->getStatus() === "§6en attente") {
            if ($this->getType() !== "1v1"){
                NavigatorManager::sendWool($player);
            }
            $player->sendMessage(Manager::ANONCEUR . "Téléportation vers la salle d'attente.");
            $player->teleport(Server::getInstance()->getLevelByName($this->getWaitingRoom())->getSafeSpawn());
        } elseif ($this->getStatus() === "§aen cours") {
            $this->setRandomTeamForNewPlayer($player);
            $pos = explode(":", Manager::POS_HIKABRAIN[$this->getTeamOfPlayer($player)]);
            $pos = explode(":", $pos[0]);
            $player->teleport(new Position($pos[0], $pos[1], $pos[2], Server::getInstance()->getLevelByName($this->getId())));
            $this->sendStuff($player);
            $player->addTitle("§l§aCombattez !");
        }
    }

    public function removeCurrentPlayer(Player $player){
        $this->setPlayersCount($this->getPlayersCount() - 1);
        $this->removePlayer($player);
    }

    public function sendStuff(Player $player){
        /* Armure */
        $helmet = Item::get(Item::LEATHER_HELMET);
        $chestplate = Item::get(Item::LEATHER_CHESTPLATE);
        $leggings = Item::get(Item::LEATHER_LEGGINGS);
        $boots = Item::get(Item::LEATHER_BOOTS);
        $helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 1));
        $chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 1));
        $leggings->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 1));
        $boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 1));
        $player->getArmorInventory()->setHelmet($helmet);
        $player->getArmorInventory()->setChestplate($chestplate);
        $player->getArmorInventory()->setLeggings($leggings);
        $player->getArmorInventory()->setBoots($boots);
        /* Inventaire */
        for ($i = 0;$i < 36;$i++){
            $player->getInventory()->setItem($i, Item::get(Item::SANDSTONE, 0, 64));
        }
        $sword = Item::get(Item::IRON_SWORD);
        $sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHARPNESS), 3));
        $sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::KNOCKBACK), 1));
        $player->getInventory()->setItem(0, $sword);
        $pickaxe = Item::get(Item::IRON_PICKAXE);
        $pickaxe->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::EFFICIENCY), 3));
        $player->getInventory()->setItem(1, $pickaxe);
        $gapp = Item::get(Item::GOLDEN_APPLE, 0, 64);
        $player->getInventory()->setItem(2, $gapp);
    }

    public function stop(string $reason){
        $idLevel = Server::getInstance()->getLevelByName($this->getId());
        $waitingLevel = Server::getInstance()->getLevelByName($this->getWaitingRoom());
        foreach ($idLevel->getPlayers() as $player){
            Functions::goToSpawn($player);
        }
        $idLevel->unload(true);
        foreach ($waitingLevel->getPlayers() as $player){
            Functions::goToSpawn($player);
        }
        foreach ($this->getPlayersListArray() as $player){
            $p = Server::getInstance()->getPlayerExact($player);
            if ($p instanceof SPlayer){
                $p->setGame(null);
            }
        }
        $waitingLevel->unload(true);
        Functions::removeDir(Server::getInstance()->getDataPath() . "/worlds/" . $this->getId());
        Functions::removeDir(Server::getInstance()->getDataPath() . "/worlds/" . $this->getWaitingRoom());
        $this->broadcastMessage(Manager::ANONCEUR . "La partie est terminer. Raison: §7{$reason}§f.");
        Webhook::sendGameLogs("> Le jeu d'arme ayant pour id **{$this->getId()}** vient d'être stopper pour la raison **{$reason}**.");
        unset(Main::$game["GunPlay"][$this->getHoster()->getName()]);
    }

}