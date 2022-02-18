<?php

namespace Zoumi\MiniGame\entities\top;

use pocketmine\entity\Animal;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use Zoumi\MiniGame\DataBase;

class Death extends Animal {

    public const NETWORK_ID = self::CHICKEN;

    public $width = 0.7;
    public $height = 0.7;

    private $time = 0;

    public function __construct(Level $level, CompoundTag $nbt)
    {
        parent::__construct($level, $nbt);

        $namedtag = "§l§6- §fTop 10 des joueurs ayant le plus de mort §6-\n\n";
        $res = DataBase::getData()->query("SELECT * FROM users ORDER BY `death` desc LIMIT 10;");
        $ret = [];
        foreach ($res->fetch_all() as $val){
            $ret[$val[0]] = $val[2];
        }
        $top = 1;
        foreach ($ret as $pseudo => $death){
            if ($top === 10){
                $namedtag .= "§e#§f{$top} §6{$pseudo} §favec §e{$death} §fmort";
                break;
            }
            $namedtag .= "§e#§f{$top} §6{$pseudo} §favec §e{$death} §fmort\n";
            $top++;
        }
        $this->setNameTag($namedtag);
        $this->setScale(0.001);
        $this->setImmobile(true);
        $this->setNameTagAlwaysVisible(true);
        $this->setScale(0.001);
        $this->setImmobile(true);
    }

    public function getName(): string
    {
        return "Death";
    }

    public function onUpdate(int $currentTick): bool
    {
        if (--$this->time <= 0){
            $namedtag = "§l§6- §fTop 10 des joueurs ayant le plus de mort §6-\n\n";
            $res = DataBase::getData()->query("SELECT * FROM users ORDER BY `death` desc LIMIT 10;");
            $ret = [];
            foreach ($res->fetch_all() as $val){
                $ret[$val[0]] = $val[2];
            }
            $top = 1;
            foreach ($ret as $pseudo => $death){
                if ($top === 10){
                    $namedtag .= "§e#§f{$top} §6{$pseudo} §favec §e{$death} §fmort";
                    break;
                }
                $namedtag .= "§e#§f{$top} §6{$pseudo} §favec §e{$death} §fmort\n";
                $top++;
            }
            $this->setNameTag($namedtag);
            $this->setScale(0.001);
            $this->setImmobile(true);
            $this->time = 20 * 60;
            return true;
        }
        return true;
    }

}