<?php

namespace Zoumi\MiniGame\api;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use Zoumi\MiniGame\DataBase;
use Zoumi\MiniGame\Main;
use Zoumi\MiniGame\tasks\async\MySQLAsyncTask;

class Jeton {


    public static function addJeton(Player $player, int $jeton){
        try {
            $res = DataBase::getData()->query("SELECT * from users WHERE pseudo='" . $player->getName() . "'");

            $calc = $res->fetch_array()["jeton"] + $jeton;

            $res->close();

            Main::getInstance()->getServer()->getAsyncPool()->submitTask(new MySQLAsyncTask("UPDATE `users` set jeton=" . $calc . " WHERE pseudo='" . $player->getName() . "'"));
        } catch (\mysqli_sql_exception $e) {

        }
        if (!GameManager::isInGame($player)){
            Main::$scoreboard[$player->getName()]->setLine(2, "§6Jeton: §e" . Jeton::getJeton($player))->set();
        }
    }

    public static function removeJeton(Player $player, float $jeton): bool
    {
        try {
            $res = DataBase::getData()->query("SELECT * from users WHERE pseudo='" . $player->getName() . "'");

            $calc = $res->fetch_array()["jeton"] - $jeton;

            Main::getInstance()->getServer()->getAsyncPool()->submitTask(new MySQLAsyncTask("UPDATE users set jeton='$calc' WHERE pseudo='" . $player->getName() . "'"));

            $res->close();

        } catch (\mysqli_sql_exception $e) {
            echo $e->getMessage();
        }
        if (!GameManager::isInGame($player)){
            Main::$scoreboard[$player->getName()]->setLine(2, "§6Jeton: §e" . Jeton::getJeton($player))->set();
        }
        return true;
    }

    /**
     * @param string $player
     * @return mixed
     */
    public static function getJeton(Player $player){
        try {

            $res = DataBase::getData()->query("SELECT * from `users` WHERE pseudo='" . $player->getName() . "'");

            $row = $res->fetch_array();

            if ($res->num_rows > 0) {
                return $row['jeton'];
            }

            $res->close();

        } catch (\mysqli_sql_exception $e) {
            echo $e->getMessage();
        }
    }

    public static function getTop(Player $player)
    {
        try{

            $res = DataBase::getData()->query("SELECT * FROM users ORDER BY money desc LIMIT 10;");
            $ret = [];
            foreach ($res->fetch_all() as $val){
                $ret[$val[0]] = $val[8];
            }
            $player->sendMessage("§6- §fTop 10 des joueurs ayant le plus de jetons §6-");
            $top = 1;
            foreach ($ret as $pseudo => $money){
                $player->sendMessage("§f#§e$top §f- §6$pseudo §favec §e{$money} §fjetons");
                $top++;
            }
            return $ret;
        }catch (\mysqli_sql_exception $mySQLErrorException){

        }
    }

    public static function getTopConsole(CommandSender $player)
    {
        try{

            $res = DataBase::getData()->query("SELECT * FROM users ORDER BY jeton desc LIMIT 10;");
            $ret = [];
            foreach ($res->fetch_all() as $val){
                $ret[$val[0]] = $val[8];
            }
            $player->sendMessage("§6- §fTop 10 des joueurs ayant le plus de jetons §6-");
            $top = 1;
            foreach ($ret as $pseudo => $money){
                $player->sendMessage("§f#§e$top §f- §6$pseudo §favec §e$money §fjetons");
                $top++;
            }
            return $ret;
        }catch (\mysqli_sql_exception $mySQLErrorException){

        }
    }


}