<?php

namespace Zoumi\MiniGame\api\game;

use http\Client\Curl\User;
use pocketmine\Player;
use pocketmine\Server;
use Zoumi\MiniGame\api\Money;
use Zoumi\MiniGame\api\Users;
use Zoumi\MiniGame\Main;
use Zoumi\MiniGame\Manager;
use Zoumi\MiniGame\utils\Functions;

class GameManager extends TeamManager implements PrivateGameManager {

    public static $dropdown = [
        "GunPlay" => ["v4", "v8", "v12"],
        "Hikabrain" => ["1v1", "2v2", "3v3", "4v4"],
        "CaptureTheFlag" => ["2v2", "4v4", "6v6"]
    ];

    protected $id;

    private $playersCount;

    private $maxPlayers;

    private $minPlayers;

    private $type;

    private $status;

    private $hoster;

    protected $gameType;

    private $time;

    private $private = [];

    public $players;

    public function getGameType(): string{
        return $this->gameType;
    }

    public function setGameType(string $value){
        $this->gameType = $value;
    }

    public function getId(): string{
        return $this->id;
    }

    public function getMaxPlayers(): int {
        return $this->maxPlayers;
    }

    public function getMinPlayers(): int {
        return $this->minPlayers;
    }

    public function getPlayersListArray(): array{
        $res = [];
        foreach ($this->players as $player => $value) {
            $res[] = $player;
        }
        return $res;
    }

    public function getListOfPlayers(): string{
        return implode(", ", $this->getPlayersListArray());
    }

    public function getPlayersCount(): int{
        return $this->playersCount ?? 0;
    }

    public function setPlayersCount(int $new): void{
        $this->playersCount = $new;
    }

    public function setMaxPlayersCount(int $new){
        $this->maxPlayers = $new;
    }

    public function setMinPlayersCount(int $new){
        $this->minPlayers = $new;
    }

    public function getType(): string{
        return $this->type;
    }

    public function getStatus(): string{
        return $this->status;
    }

    public function setStatus(string $new): void{
        $this->status = $new;
    }

    public function addPlayer(Player $player, $value){
        $this->players[$player->getName()] = $value;
        Main::$players[$player->getName()] = [
            "type" => $this->getGameType(),
            "id" => $this->id,
            "hoster" => $this->getHoster()->getName()
        ];
        $this->broadcastMessage("§5» §d" . $player->getName() . " §fvient de rejoindre la partie. §7(" . $this->getPlayersCount() . "/" . $this->getMaxPlayers() . ")");
    }

    public function removePlayer(Player $player){
        unset($this->players[$player->getName()]);
        unset(Main::$players[$player->getName()]);
        $this->broadcastMessage("§5» §d" . $player->getName() . " §fvient de quitter la partie. §7(" . $this->getPlayersCount() . "/" . $this->getMaxPlayers() . ")");
    }

    public function broadcastMessage(string $message){
        foreach (self::getPlayersListArray() as $player){
            $p = Server::getInstance()->getPlayerExact($player);
            if ($p instanceof Player){
                $p->sendMessage($message);
            }
        }
    }

    public function getHoster(): Player{
        return $this->hoster;
    }

    public function setHoster(Player $player){
        $this->hoster = $player;
    }

    public function initTime(int $time){
        $this->time = time() + 60 * $time;
    }

    public function getTime(){
        return $this->time;
    }

    public function setType(string $type){
        $this->type = $type;
    }

    public function addPrize(Player $player, int $xp, int $coins){
        Functions::addXp($player, $xp);
        Money::addMoney($player, $coins);
        $player->sendMessage(Manager::ANONCEUR . "Bien joué, vous avez gagner la partie ! Récompense: §e+{$xp} d'xp et +{$coins}\u{E102}§f.");
    }

    public static function partyExist(string $id){
        foreach (Main::$players as $player => $object){
            if ($object->id === $id){
                return true;
            }
        }
        return false;
    }

    public function isPrivate(): bool
    {
        return $this->private["value"];
    }

    public function setPrivate(bool $value)
    {
        $this->private["value"] = $value;
    }

    public function getCode(): string
    {
        return $this->private["code"];
    }

    public function genCode(): string
    {
        return Functions::genCode(10);
    }

    public function setCode(string $code)
    {
        $this->private["code"] = $this->genCode();
    }
}