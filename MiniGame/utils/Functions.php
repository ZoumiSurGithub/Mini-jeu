<?php

namespace Zoumi\MiniGame\utils;

use pocketmine\entity\Entity;
use pocketmine\entity\object\ItemEntity;
use pocketmine\item\Item;
use pocketmine\level\particle\HugeExplodeSeedParticle;
use pocketmine\level\Position;
use pocketmine\level\sound\GenericSound;
use pocketmine\math\Vector3;
use pocketmine\nbt\BigEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Player;
use pocketmine\Server;
use Zoumi\MiniGame\api\game\GameManager;
use Zoumi\MiniGame\api\game\GunPlay;
use Zoumi\MiniGame\api\Jeton;
use Zoumi\MiniGame\api\Money;
use Zoumi\MiniGame\api\NavigatorManager;
use Zoumi\MiniGame\api\Scoreboard;
use Zoumi\MiniGame\api\Users;
use Zoumi\MiniGame\entities\VillagerTest;
use Zoumi\MiniGame\Main;
use Zoumi\MiniGame\Manager;

class Functions {

    /** @var array $villagerTest */
    public static $villagerTest = [];

    public static function copyWorld(string $level, string $name): bool
    {
        @mkdir("/home/zoumi/lobby1/" . "worlds/$name/");
        @mkdir("/home/zoumi/lobby1/" . "worlds/$name/region/");
        copy("/home/zoumi/lobby1/" . "worlds/" . $level . "/level.dat", "/home/zoumi/lobby1/" . "worlds/$name/level.dat");
        $levelPath = "/home/zoumi/lobby1/" . "worlds/" . $level . "/level.dat";
        $levelPath = "/home/zoumi/lobby1/" . "worlds/$name/level.dat";

        $nbt = new BigEndianNBTStream();
        $levelData = $nbt->readCompressed(file_get_contents($levelPath));
        $levelData = $levelData->getCompoundTag("Data");
        $oldName = $levelData->getString("LevelName");
        $levelData->setString("LevelName", $name);
        $nbt = new BigEndianNBTStream();
        file_put_contents($levelPath, $nbt->writeCompressed(new CompoundTag("", [$levelData])));
        self::copy_directory("/home/zoumi/lobby1/" . "/worlds/" . $level . "/region/", "/home/zoumi/lobby1/" . "/worlds/$name/region/");
        return true;
    }

    private static function copy_directory($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    self::copy_directory($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    public static function removeDir($strDirectory){
        $handle = opendir($strDirectory);
        while(false !== ($entry = readdir($handle))){
            if($entry != '.' && $entry != '..'){
                if(is_dir($strDirectory.'/'.$entry)){
                    Functions::removeDir($strDirectory.'/'.$entry);
                }
                elseif(is_file($strDirectory.'/'.$entry)){
                    unlink($strDirectory.'/'.$entry);
                }
            }
        }
        rmdir($strDirectory.'/'.$entry);
        closedir($handle);
    }

    public static function goToSpawn(Player $player){
        $player->getArmorInventory()->clearAll();
        $player->getInventory()->clearAll();
        $player->removeAllEffects();
        $player->setHealth($player->getMaxHealth());
        $player->setFood($player->getMaxFood());
        $player->teleport(new Position(-433.5, 68, -373.5, Server::getInstance()->getLevelByName("spawn")));
        NavigatorManager::sendNavigator($player);
        if (Main::getInstance()->playerCache[$player->getName()]["gamemode"] === "0"){
            $player->setGamemode(0);
        }
        /*
        GameManager::removeScoreboard($player);
        */
        $scoreboard = Main::$scoreboard[$player->getName()] = new Scoreboard($player);
        $scoreboard
            ->setLine(0, "          ")
            ->setLine(1, "§6Money: §e" . Money::getMoney($player) . "\u{E102}")
            ->setLine(2, "§6Jeton: §e" . Jeton::getJeton($player))
            ->setLine(3, "§6Niveau: §e" . Users::getLevel($player))
            ->setLine(4, "§6XP: §e" . Users::getXP($player) . "/" . Users::getXpForNextLevel($player))
            ->setLine(5, "                  ")
            ->setLine(6, "§7sunparadise-mc.eu")
            ->set();
    }

    public static function sendEffect($damager, $entity, string $effect){
        $pk = new AddActorPacket();
        $pk->type = $effect;
        $pk->entityRuntimeId = Entity::$entityCount++;
        $pk->position = $entity->asVector3();
        $damager->sendDataPacket($pk);
    }

    public static function sendSound($player, string $sound){
        $pk = new PlaySoundPacket();
        $pk->soundName = $sound;
        $pk->x = $player->getX();
        $pk->y = $player->getY();
        $pk->z = $player->getZ();
        $pk->pitch = 1;
        $pk->volume = 1;
        $player->sendDataPacket($pk);
    }

    public static function genCode(int $max): string{
        $letters = 'ABCDEFGHIJKLMNOPRSTUVWXYZabcdefghijklmopqrstuvwxyz0123456789';
        $code = '';
        for ($i = 0;$i < $max;$i++){
            $code .= $letters[rand(0, strlen($letters) - 1)];
        }
        return $code;
    }

    public static function dropItem(Vector3 $vector3, Item $item, string $id){
        $itemTag = $item->nbtSerialize();
        $itemTag->setName("Item");
        if(!$item->isNull()){
            $nbt = Entity::createBaseNBT($vector3);
            $nbt->setShort("Health", 5);
            $nbt->setShort("PickupDelay", 10);
            $nbt->setTag($itemTag);
            $itemEntity = Entity::createEntity("Item", Server::getInstance()->getLevelByName($id), $nbt);

            if($itemEntity instanceof ItemEntity){
                $itemEntity->spawnToAll();

                return $itemEntity;
            }
        }
    }

    public static function sendFakeExplosion($damager, $entity){
        $source = (new Vector3($entity->x, $entity->y, $entity->z))->floor();
        $entity->getLevel()->addParticle(new HugeExplodeSeedParticle($source), [$damager]);
        self::sendSound($damager, "random.explode");
    }

    public static function sendPnjTest(Player $player){
        $nbt = VillagerTest::createBaseNBT(new Position(-452.5, 65, -390.5, $player->getLevel()));
        $entity = VillagerTest::createEntity("VillagerTest", $player->getLevel(), $nbt);
        $entity->spawnTo($player);
        self::$villagerTest[$player->getName()] = [
            "effect" => "none",
            "entityId" => $entity->getId()
        ];
    }

    public static function removePnjTest(Player $player){
        if (isset(self::$villagerTest[$player->getName()])){
            Server::getInstance()->getDefaultLevel()->getEntity(self::$villagerTest[$player->getName()]["entityId"]);
            unset(self::$villagerTest[$player->getName()]);
        }
    }

    public static function getAllGunPlay(): array{
        $res = [];
        foreach (Main::$game as $id => $object){
            if ($object instanceof GunPlay){
                $res[$id] = $object;
            }
        }
        return $res;
    }

    public static function addXp(Player $player, int $xp){
        if (Users::getLevel($player) < Users::getMaxLevel()){
            Users::addXP($player, $xp);
            $player->sendPopup("§e+$xp §fd'xp");
            if (Users::getXP($player) >= Users::getXpForNextLevel($player)){
                $money = (100 * 2) * Users::getLevel($player) / 2;
                Users::resetXP($player);
                Users::addLevel($player, 1);
                Money::addMoney($player, $money);
                $player->sendMessage(Manager::PREFIX_INFOS . "Vous venez de passer niveau §e" . Users::getLevel($player) . " §f! Vous venez de gagner §e{$money}\u{E102}");
            }
        }
    }

    public static function isInGame(Player $player): bool{
        if (isset(Main::$players[$player->getName()])){
            return true;
        }
        return false;
    }

    public static function getGameByHoster(string $hoster){
        foreach (Main::$game as $game => $host){
            if (!empty($host)) {
                foreach ($host as $player => $value) {
                    if ($hoster === $player) {
                        return $value;
                    }
                }
            }
        }
        return false;
    }

    public static function getIdByPlayer(Player $player): string {
        return Main::$players[$player->getName()]["id"];
    }

    public static function isGameIs(Player $player, string $game): bool{
        if (isset(Main::$players[$player->getName()])) {
            if (Main::$players[$player->getName()]["type"] === $game) {
                return true;
            }
        }
        return false;
    }

    public static function getHoster(Player $player): string{
        return Main::$players[$player->getName()]["hoster"];
    }

}