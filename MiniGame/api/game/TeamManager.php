<?php

namespace Zoumi\MiniGame\api\game;

use pocketmine\Player;
use pocketmine\Server;
use Zoumi\MiniGame\Manager;
use Zoumi\MiniGame\utils\Functions;

class TeamManager {

    private $team;

    private $maxTeam;

    public function initTeam(array $value){
        $this->team = $value;
    }

    public function setMaxTeam(int $count){
        $this->maxTeam = $count;
    }

    public function getMaxTeam(): int{
        return $this->maxTeam;
    }

    public function getPoint(string $team): int{
        return $this->team[$team]["points"];
    }

    public function addPoint(string $team, int $points){
        $actus = $this->team[$team]["points"];
        $this->team[$team]["points"] = $actus + $points;
    }

    public function getPlayerList(string $team): string
    {
        return "§7" . (implode("§f, §7", $this->team[$team]["players"]) ?? "Aucun.") . ".";
    }

    public function getPlayerCount(string $team): int{
        $count = 0;
        if (!empty($this->team[$team]["players"])){
            return count($this->team[$team]["players"]);
        }
        return $count;
    }

    public function getMaxPlayerCount(string $team): int{
        return $this->team[$team]["maxPlayers"];
    }

    public function setTeamForPlayer(Player $player, string $team){
        if ($this->getPlayerCount($team) < $this->getMaxPlayerCount($team)){
            $this->team[$team]["players"][$player->getName()] = $player->getName();
        }else{
            $player->sendMessage(Manager::PREFIX_ALERT . "Cette team est complete choisissez en une autre !");
            return;
        }
    }

    public function setRandomTeamForNewPlayer(Player $player){
        if ($this->getMaxTeam() === 2) {
            if ($this->getPlayerCount("Rouge") < $this->getPlayerCount("Bleu") && $this->getPlayerCount("Rouge") < $this->getMaxPlayerCount("Rouge")) {
                $this->setTeamForPlayer($player, "Rouge");
                return;
            }
            if ($this->getPlayerCount("Bleu") < $this->getPlayerCount("Rouge") && $this->getPlayerCount("Bleu") < $this->getMaxPlayerCount("Bleu")) {
                $this->setTeamForPlayer($player, "Bleu");
                return;
            }
            if ($this->getPlayerCount("Rouge") < $this->getMaxPlayerCount("Rouge")) {
                $this->setTeamForPlayer($player, "Rouge");
                return;
            }
            if ($this->getPlayerCount("Bleu") < $this->getMaxPlayerCount("Bleu")) {
                $this->setTeamForPlayer($player, "Bleu");
                return;
            }
        }elseif ($this->getMaxTeam() === 4){
            if ($this->getPlayerCount("Rouge") < $this->getPlayerCount("Bleu") &&
                $this->getPlayerCount("Rouge") < $this->getPlayerCount("Jaune") &&
                $this->getPlayerCount("Rouge") < $this->getPlayerCount("Vert") &&
                $this->getPlayerCount("Rouge") < $this->getMaxPlayerCount("Rouge")) {
                $this->setTeamForPlayer($player, "Rouge");
                return;
            }
            if ($this->getPlayerCount("Bleu") < $this->getPlayerCount("Rouge") &&
                $this->getPlayerCount("Bleu") < $this->getPlayerCount("Jaune") &&
                $this->getPlayerCount("Bleu") < $this->getPlayerCount("Vert") &&
                $this->getPlayerCount("Bleu") < $this->getMaxPlayerCount("Rouge")) {
                $this->setTeamForPlayer($player, "Bleu");
                return;
            }
            if ($this->getPlayerCount("Jaune") < $this->getPlayerCount("Rouge") &&
                $this->getPlayerCount("Jaune") < $this->getPlayerCount("Bleu") &&
                $this->getPlayerCount("Jaune") < $this->getPlayerCount("Vert") &&
                $this->getPlayerCount("Jaune") < $this->getMaxPlayerCount("Rouge")) {
                $this->setTeamForPlayer($player, "Jaune");
                return;
            }
            if ($this->getPlayerCount("Vert") < $this->getPlayerCount("Rouge") &&
                $this->getPlayerCount("Vert") < $this->getPlayerCount("Bleu") &&
                $this->getPlayerCount("Vert") < $this->getPlayerCount("Jaune") &&
                $this->getPlayerCount("Vert") < $this->getMaxPlayerCount("Rouge")) {
                $this->setTeamForPlayer($player, "Vert");
                return;
            }
            if ($this->getPlayerCount("Rouge") < $this->getMaxPlayerCount("Rouge")) {
                $this->setTeamForPlayer($player, "Rouge");
                return;
            }
            if ($this->getPlayerCount("Bleu") < $this->getMaxPlayerCount("Bleu")) {
                $this->setTeamForPlayer($player, "Bleu");
                return;
            }
            if ($this->getPlayerCount("Jaune") < $this->getMaxPlayerCount("Jaune")) {
                $this->setTeamForPlayer($player, "Jaune");
                return;
            }
            if ($this->getPlayerCount("Vert") < $this->getMaxPlayerCount("Vert")) {
                $this->setTeamForPlayer($player, "Vert");
                return;
            }
        }
    }

    public function isInTeam(Player $player): bool{
        if (isset($this->team["Rouge"]["players"][$player->getName()])){
            return true;
        }elseif (isset($this->team["Bleu"]["players"][$player->getName()])){
            return true;
        }elseif (isset($this->team["Jaune"]["players"][$player->getName()])){
            return true;
        }elseif (isset($this->team["Vert"]["players"][$player->getName()])){
            return true;
        }
        return false;
    }

    public function getTeamOfPlayer(Player $player, bool $color = false){
        if (isset($this->team["Rouge"]["players"][$player->getName()])){
            if ($color){
                return "§cRouge";
            }
            return "Rouge";
        }elseif (isset($this->team["Bleu"]["players"][$player->getName()])){
            if ($color){
                return "§1Bleu";
            }
            return "Bleu";
        }elseif (isset($this->team["Jaune"]["players"][$player->getName()])){
            if ($color){
                return "§eJaune";
            }
            return "Jaune";
        }elseif (isset($this->team["Vert"]["players"][$player->getName()])){
            if ($color){
                return "§2Vert";
            }
            return "Vert";
        }
        return false;
    }

    public function getColorOfTeam(string $team){
        if ($team === "Rouge"){
            return "§c";
        }elseif ($team === "Bleu"){
            return "§1";
        }elseif ($team === "Jaune"){
            return "§e";
        }elseif ($team === "Vert"){
            return "§2";
        }
        return false;
    }

    public function removePlayerInTeam(Player $player, string $team){
        unset($this->team[$team]["players"][$player->getName()]);
    }

    public function sendMessageForTeam(string $team, Player $player, string $message)
    {
        foreach ($this->team[$team]["players"] as $pla => $value) {
            $p = Server::getInstance()->getPlayerExact($pla);
            if ($p instanceof Player) {
                $p->sendMessage("§7[" . $this->getTeamOfPlayer($p, true) . "§7] §7" . $player->getName() . " §f- " . $this->getColorOfTeam($this->getTeamOfPlayer($player)) . $message);
            }
        }
    }

    public function sendTitle(string $team, string $title, string $subtitle = "")
    {
        foreach ($this->team[$team]["players"] as $pla => $value) {
            $p = Server::getInstance()->getPlayerExact($pla);
            if ($p instanceof Player) {
                $p->addTitle($title, $subtitle);
            }
        }
    }

    public function sendSubTitle(string $team, string $subtitle)
    {
        foreach ($this->team[$team]["players"] as $pla => $value) {
            $p = Server::getInstance()->getPlayerExact($pla);
            if ($p instanceof Player) {
                $p->addSubTitle($subtitle);
            }
        }
    }

    public function sendSound(string $team, string $sound)
    {
        foreach ($this->team[$team]["players"] as $pla => $value) {
            $p = Server::getInstance()->getPlayerExact($pla);
            if ($p instanceof Player) {
                Functions::sendSound($p, $sound);
            }
        }
    }

    public function isBedAlive(string $team): string
    {
        if ($this->team[$team]["bedAlive"] === true){
            return "§a✓";
        }elseif(count($this->team[$team]["players"]) > 0){
            return "§6" . count($this->team[$team]["players"]);
        }else{
            return "§c✖";
        }
    }

    public function setBedAlive(string $team, bool $value){
        $this->team[$team]["bedAlive"] = $value;
    }

    public function playerIsAlive(string $team, Player $player): bool
    {
        if (in_array($player->getName(), $this->team[$team]["players"])){
            return true;
        }
        return false;
    }

    public function getPointForWin(): int{
        if ($this->getPoint("Rouge") === 2 && $this->getPoint("Bleu") === 0){
            return 3;
        }elseif ($this->getPoint("Bleu") === 2 && $this->getPoint("Rouge") === 0){
            return 3;
        }elseif ($this->getPoint("Rouge")  === 2 && $this->getPoint("Bleu") === $this->getPoint("Rouge")  - 1){
            return $this->getPoint("Rouge") + 2;
        }elseif ($this->getPoint("Bleu") === 2 && $this->getPoint("Rouge") === $this->getPoint("Bleu") - 1){
            return $this->getPoint("Bleu") + 2;
        }
        return 3;
    }

    public function getPlayersListByTeamArray(string $team): array{
        $res = [];
        foreach ($this->team[$team]["players"] as $player){
            $res[] = $player;
        }
        return $res;
    }

}