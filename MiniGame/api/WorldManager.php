<?php

namespace Zoumi\MiniGame\api;

class WorldManager {

    /** @var array $world */
    public static $world = [];

    public static function createWorld(string $level, bool $place, bool $break, bool $interact, bool $fall, bool $damageByEntity, bool $dropItem, bool $anti2v1){
        self::$world[$level] = [
            "place" => $place,
            "break" => $break,
            "interact" => $interact,
            "fall" => $fall,
            "damageByEntity" => $damageByEntity,
            "dropItem" => $dropItem,
            "anti2v1" => $anti2v1
        ];
    }

    public static function getPlace(string $level) : bool{
        return self::$world[$level]["place"];
    }

    public static function getBreak(string $level) : bool{
        return self::$world[$level]["break"];
    }

    public static function getInteract(string $level) : bool{
        return self::$world[$level]["interact"];
    }

    public static function getFall(string $level) : bool{
        return self::$world[$level]["fall"];
    }

    public static function getDamageByEntity(string $level) : bool{
        return self::$world[$level]["damageByEntity"];
    }

    public static function getDropItem(string $level) : bool{
        return self::$world[$level]["dropItem"];
    }

    public static function getAnti2v1(string $level) : bool{
        return self::$world[$level]["anti2v1"];
    }

}