<?php

namespace Zoumi\MiniGame\api;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use Zoumi\MiniGame\api\game\GameManager;
use Zoumi\MiniGame\DataBase;
use Zoumi\MiniGame\Main;
use Zoumi\MiniGame\tasks\async\MySQLAsyncTask;

class Money {

    public static function addMoney(Player $player, int $money){
        try {
            $res = DataBase::getData()->query("SELECT * from users WHERE pseudo='" . $player->getName() . "'");

            $calc = $res->fetch_array()["money"] + $money;

            $res->close();

            Main::getInstance()->getServer()->getAsyncPool()->submitTask(new MySQLAsyncTask("UPDATE `users` set money=" . $calc . " WHERE pseudo='" . $player->getName() . "'"));
        } catch (\mysqli_sql_exception $e) {

        }
        if (!Functions::isInGame($player)){
            Main::$scoreboard[$player->getName()]->setLine(1, "§6Money: §e" . Money::getMoney($player))->set();
        }
    }

    public static function removeMoney(Player $player, float $money): bool
    {
        try {
            $res = DataBase::getData()->query("SELECT * from users WHERE pseudo='" . $player->getName() . "'");

            $calc = $res->fetch_array()["money"] - $money;

            Main::getInstance()->getServer()->getAsyncPool()->submitTask(new MySQLAsyncTask("UPDATE users set money='$calc' WHERE pseudo='" . $player->getName() . "'"));

            $res->close();

        } catch (\mysqli_sql_exception $e) {
            echo $e->getMessage();
        }
        if (!Functions::isInGame($player)){
            Main::$scoreboard[$player->getName()]->setLine(1, "§6Money: §e" . Money::getMoney($player))->set();
        }
        return true;
    }

    /**
     * @param string $player
     * @return mixed
     */
    public static function getMoney(Player $player){
        try {

            $res = DataBase::getData()->query("SELECT * from `users` WHERE pseudo='" . $player->getName() . "'");

            $row = $res->fetch_array();

            if ($res->num_rows > 0) {
                return $row['money'];
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
                $ret[$val[0]] = $val[7];
            }
            $player->sendMessage("§6- §fTop 10 des joueurs ayant le plus de \u{E102}§6-");
            $top = 1;
            foreach ($ret as $pseudo => $money){
                $player->sendMessage("§f#§e$top §f- §6$pseudo §favec §e{$money}§f\u{E102}");
                $top++;
            }
            return $ret;
        }catch (\mysqli_sql_exception $mySQLErrorException){

        }
    }

    public static function getTopConsole(CommandSender $player)
    {
        try{

            $res = DataBase::getData()->query("SELECT * FROM users ORDER BY money desc LIMIT 10;");
            $ret = [];
            foreach ($res->fetch_all() as $val){
                $ret[$val[0]] = $val[1];
            }
            $player->sendMessage("§6- §fTop 10 des joueurs ayant le plus de coins §6-");
            $top = 1;
            foreach ($ret as $pseudo => $money){
                $player->sendMessage("§f#§e$top §f- §6$pseudo §favec §e$money §fcoins");
                $top++;
            }
            return $ret;
        }catch (\mysqli_sql_exception $mySQLErrorException){

        }
    }

}