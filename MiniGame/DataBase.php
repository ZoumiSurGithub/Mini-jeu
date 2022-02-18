<?php

namespace Zoumi\MiniGame;

class DataBase {

    public static function getData(): \mysqli
    {
        return new \mysqli("127.0.0.1", "zoumi", "z8xzdc3VGHh856Fk4fHAp5WtDKF3585x", "minigame", 3306);
    }

    public static function setupTable(): void{
        DataBase::getData()->query("CREATE TABLE IF NOT EXISTS `minigame`.`users` ( `pseudo` VARCHAR(55) NOT NULL , `kill` INT NOT NULL , `death` INT NOT NULL , `kd` FLOAT NOT NULL , `gamemode` INT NOT NULL , `level` INT NOT NULL , `xp` INT NOT NULL , `money` INT NOT NULL , `jeton` INT NOT NULL , `boost` INT NOT NULL , PRIMARY KEY (`pseudo`)) ENGINE = InnoDB;");
        DataBase::getData()->query("CREATE TABLE IF NOT EXISTS `minigame`.`link` ( `pseudo` VARCHAR(55) NOT NULL , `code` VARCHAR(10) NOT NULL , `id` VARCHAR(55) NOT NULL , PRIMARY KEY(`pseudo`)) ENGINE = InnoDB;");
    }

}