<?php

namespace Zoumi\MiniGame\api;

use pocketmine\Player;
use pocketmine\Server;
use Zoumi\MiniGame\api\game\GameManager;
use Zoumi\MiniGame\DataBase;
use Zoumi\MiniGame\Main;
use Zoumi\MiniGame\tasks\async\MySQLAsyncTask;
use Zoumi\MiniGame\utils\Functions;

class Users {

    public static function createUser(Player $player){
        try {

            Server::getInstance()->getAsyncPool()->submitTask(new MySQLAsyncTask("INSERT INTO users (pseudo,`kill`,death,kd,gamemode,`level`,`xp`,`money`,`jeton`,`boost`) VALUES ('" . $player->getName() . "','0','0','0','0','0','0','0','0','0')"));
            Server::getInstance()->getAsyncPool()->submitTask(new MySQLAsyncTask("INSERT INTO link (pseudo,code,id) VALUES ('" . $player->getName() . "','" . Functions::genCode(10) . "','')"));

        }catch (\mysqli_sql_exception $exception){

        }
    }

    public static function playerExist(string $player): bool {
        try {
            $res = DataBase::getData()->query("SELECT pseudo FROM users WHERE pseudo='" . $player . "'");
            return $res->num_rows > 0;
        }catch (\mysqli_sql_exception $exception){

        }
    }

    public static function addKill(Player $player, int $kill = 1){
        try {

            $res = DataBase::getData()->query("SELECT * FROM users WHERE pseudo='" . $player->getName() . "'");

            $row = $res->fetch_array();

            $calc = $row["kill"] + $kill;

            Server::getInstance()->getAsyncPool()->submitTask(new MySQLAsyncTask("UPDATE users set `kill`='$calc' WHERE pseudo='" . $player->getName() . "'"));

        }catch (\mysqli_sql_exception $exception){

        }
    }

    public static function addDeath(Player $player, int $death = 1){
        try {

            $res = DataBase::getData()->query("SELECT * FROM users WHERE pseudo='" . $player->getName() . "'");

            $row = $res->fetch_array();

            $calc = $row["death"] + $death;

            Server::getInstance()->getAsyncPool()->submitTask(new MySQLAsyncTask("UPDATE users set `death`='$calc' WHERE pseudo='" . $player->getName() . "'"));

        }catch (\mysqli_sql_exception $exception){

        }
    }

    public static function setKd(Player $player){
        try {

            $res = DataBase::getData()->query("SELECT * FROM users WHERE pseudo='" . $player->getName() . "'");

            $row = $res->fetch_array();

            $calc = $row["death"] <= 0 ? $row["kill"] / 1 : $row["kill"] / $row["death"];

            DataBase::getData()->query("UPDATE `users` set kd='" . $calc . "' WHERE pseudo='" . $player . "'");
        } catch (\mysqli_sql_exception $e){
            echo $e->getMessage();
        }
    }

    public static function setGamemode(Player $player, int $gamemode = 0){
        try {

            Server::getInstance()->getAsyncPool()->submitTask(new MySQLAsyncTask("UPDATE users set gamemode='$gamemode' WHERE pseudo='" . $player->getName() . "'"));

        }catch (\mysqli_sql_exception $exception){

        }
    }

    public static function getGamemode(Player $player){
        try {

            $res = DataBase::getData()->query("SELECT * FROM users WHERE pseudo='" . $player->getName() . "'");

            $row = $res->fetch_array();

            return $row["gamemode"];

        }catch (\mysqli_sql_exception $exception){

        }
    }

    public static function getXP(Player $player): int {
        try {

            $res = DataBase::getData()->query("SELECT * FROM users WHERE pseudo='" . $player->getName() . "'");

            $row = $res->fetch_array();

            return $row["xp"];

        }catch (\mysqli_sql_exception $exception){

        }
    }

    public static function getLevel(Player $player): int {
        try {

            $res = DataBase::getData()->query("SELECT * FROM users WHERE pseudo='" . $player->getName() . "'");

            $row = $res->fetch_array();

            return $row["level"];

        }catch (\mysqli_sql_exception $exception){

        }
    }

    public static function addXP(Player $player, int $xp) {
        try {

            $res = DataBase::getData()->query("SELECT * FROM users WHERE pseudo='" . $player->getName() . "'");

            $row = $res->fetch_array();

            $calc = $row["xp"] + $xp;

            Main::getInstance()->getServer()->getAsyncPool()->submitTask(new MySQLAsyncTask("UPDATE users SET xp='$calc' WHERE pseudo='" . $player->getName() . "'"));

        }catch (\mysqli_sql_exception $exception){

        }
        if (!Functions::isInGame($player)){
            Main::$scoreboard[$player->getName()]
                ->setLine(3, "§6Niveau: §e" . Users::getLevel($player))
                ->setLine(4, "§6XP: §e" . Users::getXP($player) . "/" . Users::getXpForNextLevel($player))
                ->set();
        }
    }

    public static function addLevel(Player $player, int $level): int {
        try {

            $res = DataBase::getData()->query("SELECT * FROM users WHERE pseudo='" . $player->getName() . "'");

            $calc = $res["level"] + $level;

            Main::getInstance()->getServer()->getAsyncPool()->submitTask(new MySQLAsyncTask("UPDATE users SET `level`='$calc' WHERE pseudo='" . $player->getName() . "'"));

        }catch (\mysqli_sql_exception $exception){

        }
        if (!Functions::isInGame($player)){
            Main::$scoreboard[$player->getName()]
                ->setLine(3, "§6Niveau: §e" . Users::getLevel($player))
                ->setLine(4, "§6XP: §e" . Users::getXP($player) . "/" . Users::getXpForNextLevel($player))
                ->set();
        }
    }

    public static function resetXP(Player $player): int {
        try {

            Main::getInstance()->getServer()->getAsyncPool()->submitTask(new MySQLAsyncTask("UPDATE users SET xp='0' WHERE pseudo='" . $player->getName() . "'"));

        }catch (\mysqli_sql_exception $exception){

        }
        if (!Functions::isInGame($player)){
            Main::$scoreboard[$player->getName()]
                ->setLine(3, "§6Niveau: §e" . Users::getLevel($player))
                ->setLine(4, "§6XP: §e" . Users::getXP($player) . "/" . Users::getXpForNextLevel($player))
                ->set();
        }
    }

    public static function getMaxLevel(): int{
        return 500;
    }

    public static function getXpForNextLevel(Player $player): int{
        if (self::getLevel($player) === 0){
            return 500;
        }
        return self::getLevel($player) + 1 * 1.2;
    }

    public static function getCode(Player $player): string{
        try {

            $res = DataBase::getData()->query("SELECT * FROM link WHERE pseudo='" . $player->getName() . "'");

            $row = $res->fetch_array();

            return $row["code"];

        }catch (\mysqli_sql_exception $exception){

        }
    }

    public static function isLinked(Player $player): bool{
        try {

            $res = DataBase::getData()->query("SELECT * FROM link WHERE PSEUDO='" . $player->getName() . "'");

            $row = $res->fetch_array();

            if (!empty($row["id"])){
                return true;
            }

        }catch (\mysqli_sql_exception $exception){

        }
        return false;
    }

}