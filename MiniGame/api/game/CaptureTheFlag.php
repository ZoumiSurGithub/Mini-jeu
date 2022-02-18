<?php

namespace Zoumi\MiniGame\api\game;

use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\Server;
use Zoumi\MiniGame\Main;
use Zoumi\MiniGame\Manager;
use Zoumi\MiniGame\tasks\async\CopyWorld;
use Zoumi\MiniGame\utils\Functions;

class CaptureTheFlag {

    /** @var array $ctf */
    public static $ctf = [];
    /** @var array $players */
    public static $players = [];
    /** @var array $scoreboard */
    public static $scoreboard = [];
    /** @var int $count */
    public static $count = 0;
    /** @var string[] $pos */
    public static $pos = array(
        "Rouge" => "-597.5:31:-299.5;-595.5:31:-297.5;-593.5:31:-295.5;-591.5:31:-293.5;-589.5:31:-291.5;-587.5:31:-289.5",
        "Bleu" => "-458.5:35:-498.5;-456.5:35:-496.5;-454.5:35:-494.5;-452.5:35:-492.5;-450.5:35:-490.5;-448.5:35:-488.5"
    );

    public static function createCaptureTheFlag(Player $player, bool $private){
        $count = self::$count = self::$count + 1;
        $countRoom = Main::$waitingRoomCount = Main::$waitingRoomCount + 1;
        $code = Functions::genCode(10);
        self::$ctf["CaptureTheFlag$count"] = [
            "players" => [$player->getName() => $player->getName()],
            "name" => "CaptureTheFlag$count",
            "waitingRoom" => "WaitingRoomGP$countRoom",
            "status" => "§6en attente",
            "hasFlagRed" => "Aucun.",
            "hasFlagBlue" => "Aucun.",
            "maxPoints" => 10,
            "playersCount" => 1,
            "maxPlayers" => 12,
            "playersPerTeam" => 6,
            "isPrivate" => $private,
            "code" => $code
        ];
        TeamManager::$team["CaptureTheFlag$count"] = [
            "Rouge" => [
                "players" => [],
                "points" => 0,
                "maxPlayers" => 6
            ],
            "Bleu" => [
                "players" => [],
                "points" => 0,
                "maxPlayers" => 6
            ],
            "maxTeam" => 2
        ];
        self::$players[$player->getName()] = "CaptureTheFlag$count";
        Webhook::sendGameLogs("> Une partie de capture du drapeau vient d'être lancer par **" . $player->getName() . "**, l'id de la partie est **CaptureTheFlag{$count}**.");
        $player->sendMessage(Manager::CREATE_PARTIES);
        NavigatorManager::sendWool($player);
        if ($private){
            PrivateGameManager::$private[$player->getName()] = [
                "type" => "CaptureTheFlag",
                "game" => "CaptureTheFlag$count",
                "code" => $code
            ];
            $player->sendMessage(Manager::VIEW_CODE);
        }
        Main::getInstance()->getServer()->getAsyncPool()->submitTask(new CopyWorld("WaitingRoomGP$countRoom", "CaptureTheFlag", "CaptureTheFlag$count"));
    }

    public static function addPlayer(string $id, Player $player){
        if (isset(self::$ctf[$id])){
            self::$ctf[$id]["players"][$player->getName()] = $player->getName();
            self::$ctf[$id]["playersCount"] = self::$ctf[$id]["playersCount"] + 1;
            self::$players[$player->getName()] = $id;
            foreach (Server::getInstance()->getLevelByName(self::$ctf[$id]["name"])->getPlayers() as $p) {
                if ($p instanceof Player) {
                    $p->sendMessage("§d" . $player->getName() . " §fvient de rejoindre la partie. §7(" . self::getPlayersCount($id) . "/" . self::getMaxPlayersCount($id) . ")");
                }
            }
            foreach (Server::getInstance()->getLevelByName(self::$ctf[$id]["waitingRoom"])->getPlayers() as $p) {
                if ($p instanceof Player) {
                    $p->sendMessage("§d" . $player->getName() . " §fvient de rejoindre la partie. §7(" . self::getPlayersCount($id) . "/" . self::getMaxPlayersCount($id) . ")");
                }
            }
            if (self::$ctf[$id]["status"] === "§6en attente") {
                $player->sendMessage(Manager::ANONCEUR . "Téléportation vers la salle d'attente.");
                $player->teleport(Server::getInstance()->getLevelByName(self::$ctf[$id]["waitingRoom"])->getSafeSpawn());
                NavigatorManager::sendWool($player);
            } elseif (self::$ctf[$id]["status"] === "§aen cours") {
                TeamManager::setRandomTeamForNewPlayer($id, $player);
                $pos = explode(";", self::$pos[TeamManager::getTeamOfPlayer($id, $player)]);
                $pos = explode(":", $pos[0]);
                $player->teleport(new Position((float)$pos[0], (int)$pos[1], (float)$pos[2], Server::getInstance()->getLevelByName(self::$ctf[$id]["name"])));
                self::sendStuff($player);
                $player->addTitle("§l§aCombattez !");
            }
        }
    }

    public static function removePlayer(string $id, Player $player){
        if (isset(self::$ctf[$id])){
            unset(self::$ctf[$id]["players"][$player->getName()]);
            unset(self::$players[$player->getName()]);
            self::$ctf[$id]["playersCount"] = self::getPlayersCount($id) - 1;
            if (TeamManager::isInTeam($id, $player)) {
                TeamManager::removePlayerInTeam($id, $player, TeamManager::getTeamOfPlayer($id, $player));
            }
            foreach (Server::getInstance()->getLevelByName(self::$ctf[$id]["name"])->getPlayers() as $p){
                if ($p instanceof Player){
                    $p->sendMessage("§d" . $player->getName() . " §fvient de quitter la partie. §7(" . self::getPlayersCount($id) . "/" . self::getMaxPlayersCount($id) . ")");
                }
            }
            foreach (Server::getInstance()->getLevelByName(self::$ctf[$id]["waitingRoom"])->getPlayers() as $p){
                if ($p instanceof Player){
                    $p->sendMessage("§d" . $player->getName() . " §fvient de quitter la partie. §7(" . self::getPlayersCount($id) . "/" . self::getMaxPlayersCount($id) . ")");
                }
            }
            if (self::$ctf[$id]["playersCount"] < 1){
                self::stopCaptureTheFlag($id, "aucun joueur dans cette partie");
            }else{
                Functions::goToSpawn($player);
            }
        }
    }

    public static function getGameInWaiting(): int{
        $count = 0;
        foreach (self::$ctf as $id => $value){
            if (self::$ctf[$id]["status"] === "§6en attente"){
                $count++;
            }
        }
        return $count;
    }

    public static function getPartyInPlaying(): int{
        $count = 0;
        foreach (self::$ctf as $id => $value){
            if (self::$ctf[$id]["status"] === "§aen cours"){
                $count++;
            }
        }
        return $count;
    }

    public static function getPlayersList($id): string{
        if (isset(self::$ctf[$id])) {
            $players = [];
            foreach (self::$ctf[$id]["players"] as $player => $point){
                $players[] = $player;
            }
            return "§7" . implode("§f, §7", $players) . "§f";
        }
        return "";
    }

    public static function getPlayersCount(string $id): int{
        if (isset(self::$ctf[$id])){
            return self::$ctf[$id]["playersCount"];
        }
        return 0;
    }

    public static function getMaxPlayersCount(string $id): int{
        if (isset(self::$ctf[$id])){
            return self::$ctf[$id]["maxPlayers"];
        }
        return 0;
    }

    public static function hasFlagRed(string $id){
        if (isset(self::$ctf[$id])){
            if (self::$ctf[$id]["hasFlagRed"] === "Aucun."){
                return "Aucun.";
            }
            return self::$ctf[$id]["hasFlagRed"];
        }
    }

    public static function hasFlagBlue(string $id){
        if (isset(self::$ctf[$id])){
            if (self::$ctf[$id]["hasFlagBlue"] === "Aucun."){
                return "Aucun.";
            }
            return self::$ctf[$id]["hasFlagBlue"];
        }
    }

    public static function sendStuff(Player $player){
        $player->getArmorInventory()->clearAll();
        $player->getInventory()->clearAll();
        /* Armure */
        $helmet = Item::get(Item::IRON_HELMET);
        $chestplate = Item::get(Item::IRON_CHESTPLATE);
        $leggings = Item::get(Item::IRON_LEGGINGS);
        $boots = Item::get(Item::IRON_BOOTS);
        $helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
        $chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 3));
        $leggings->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
        $boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
        $player->getArmorInventory()->setHelmet($helmet);
        $player->getArmorInventory()->setChestplate($chestplate);
        $player->getArmorInventory()->setLeggings($leggings);
        $player->getArmorInventory()->setBoots($boots);
        /* Inventaire */
        $sword = Item::get(Item::IRON_SWORD);
        $sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHARPNESS), 3));
        $sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::KNOCKBACK), 1));
        $player->getInventory()->setItem(0, $sword);
        /** Effets */
        $player->addEffect(new EffectInstance(Effect::getEffect(Effect::SPEED), 20 * 999999, 0, false));
    }

    public static function stopCaptureTheFlag(string $id, string $reason){
        foreach (Server::getInstance()->getLevelByName(self::$ctf[$id]["waitingRoom"])->getPlayers() as $player){
            $player->sendMessage(Manager::ANONCEUR . "Téléportation au spawn.");
            Functions::goToSpawn($player);
        }
        Server::getInstance()->getLevelByName(self::$ctf[$id]["waitingRoom"])->unload(true);
        Functions::removeDir(Server::getInstance()->getDataPath() . "/worlds/" . self::$ctf[$id]["waitingRoom"]);
        foreach (Server::getInstance()->getLevelByName(self::$ctf[$id]["name"])->getPlayers() as $player){
            $player->sendMessage(Manager::ANONCEUR . "Téléportation au spawn.");
            Functions::goToSpawn($player);
        }
        Server::getInstance()->getLevelByName(self::$ctf[$id]["name"])->unload(true);
        Functions::removeDir(Server::getInstance()->getDataPath() . "/worlds/" . self::$ctf[$id]["name"]);
        foreach (self::$ctf[$id]["players"] as $player => $points){
            $p = Server::getInstance()->getPlayerExact($player);
            if ($p instanceof Player){
                $p->sendMessage(Manager::ANONCEUR . "La partie est terminer. Raison: §7{$reason}§f.");
            }
        }
        foreach (self::$scoreboard as $player => $value){
            unset(self::$scoreboard[$player]);
        }
        foreach (self::$players as $player => $ctf){
            if ($ctf === $id){
                unset(self::$players[$player]);
            }
        }
        foreach (PrivateGameManager::$private as $player => $value){
            if (PrivateGameManager::$private[$player]["game"] === $id){
                unset(PrivateGameManager::$private[$player]);
            }
        }
        Webhook::sendGameLogs("> Le Capture du drapeau ayant pour id **" . $id . "** vient d'être stopper pour la raison **$reason**.");
        unset(self::$ctf[$id]);
    }

    public static function sendMessageForAll(string $id, Player $player, string $message){
        if (isset(self::$ctf[$id])) {
            foreach (self::$ctf[$id]["players"] as $pla => $pla) {
                $p = Server::getInstance()->getPlayerExact($pla);
                if ($p instanceof Player) {
                    $p->sendMessage("§7[" . TeamManager::getTeamOfPlayer($id, $player, true) . "§7] " . $player->getName() . " §f- §7" . $message);
                }
            }
        }
    }

    public static function isInGame(Player $player): bool{
        if (isset(self::$players[$player->getName()])){
            return true;
        }
        return false;
    }

    public static function getStatus(string $id): string{
        return self::$ctf[$id]["status"];
    }

    public static function getId(Player $player): string{
        if (self::isInGame($player)){
            return self::$players[$player->getName()];
        }
    }

    public static function setHasFlag(string $id, string $team, string $value){
        self::$ctf[$id]["hasFlag$team"] = $value;
    }

}