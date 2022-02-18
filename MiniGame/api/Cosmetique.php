<?php

namespace Zoumi\MiniGame\api;

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\entity\Skin;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use Zoumi\MiniGame\commands\staff\Entity;
use Zoumi\MiniGame\entities\FireworksRocket;
use Zoumi\MiniGame\items\Fireworks;
use Zoumi\MiniGame\Main;
use Zoumi\MiniGame\Manager;
use Zoumi\MiniGame\utils\Functions;

class Cosmetique {

    public static function getConfig(): Config{
        return Main::getInstance()->cosmetique;
    }

    public const NOT_HAVE_PERM = "§f(§eSun§cAlert§f) §cVous ne possédez pas se cosmetique.";

    /* Effet */
    public const EFFECT_THUNDER = "use.effect.thunder";

    public const EFFECT_FIREWORK = "use.effect.firework";

    public const EFFECT_CREEPER = "use.effect.creeper";

    public const EFFECT_EXPLOSION = "use.effect.explosion";

    /* Join */
    public const JOIN_THUNDER = "use.join.thunder";

    public const JOIN_FIREWORK = "use.join.firework";

    public const JOIN_CREEPER = "use.join.creeper";

    public const JOIN_EXPLOSION = "use.join.explosion";

    /* Quit */
    public const QUIT_THUNDER = "use.quit.thunder";

    public const QUIT_FIREWORK = "use.quit.firework";

    public const QUIT_CREEPER = "use.quit.creeper";

    public const QUIT_EXPLOSION = "use.quit.explosion";

    /* Cape */
    /* Pays */
    public const CAPE_FRENCH = "use.cape.french";

    public const CAPE_ARGENTINA = "use.cape.argentina";

    public const CAPE_GUATEMALA = "use.cape.guatemala";

    public const CAPE_INDIA = "use.cape.india";

    public const CAPE_INDONESIA = "use.cape.indonesia";

    public const CAPE_IRELAND = "use.cape.ireland";

    public const CAPE_ISRAEL = "use.cape.israel";

    public const CAPE_ITALY = "use.cape.italy";

    public const CAPE_MEXICO = "use.cape.mexico";

    public const CAPE_USA = "use.cape.usa";

    public const CAPE_UK = "use.cape.uk";

    public const CAPE_NEW_ZEALAND = "use.cape.newzealand";

    /* Lunar */
    public const CAPE_LUNAR = "use.cape.lunar";

    /* Autres */
    public const CAPE_ANDROID = "use.cape.android";

    public const CAPE_BADLION = "use.cape.badlion";

    public const CAPE_NOEL = "use.cape.noel";

    public const CAPE_JAPONAIS = "use.cape.japonais";

    public const CAPE_DRAGON = "use.cape.dragon";

    public const CAPE_CROWN = "use.cape.crown";

    public const CAPE_DEADPOOL = "use.cape.deadpool";

    public const CAPE_RETRO = "use.cape.retro";

    public const CAPE_TWITCH = "use.cape.twitch";

    public const CAPE_YOUTUBE = "use.cape.youtube";

    public static function isEnable(Player $player, string $effect): bool{
        return self::getConfig()->exists($player->getName() . "-" . $effect);
    }

    public static function setCape(Player $player, string $directory){
        $skin = $player->getSkin();
        $player->setSkin(new Skin($skin->getSkinId(), $skin->getSkinData(), Main::getInstance()->PNGtoBYTES($directory . ".png"), $skin->getGeometryName(), $skin->getGeometryData()));
        $player->sendSkin();
    }

    public static function removeAllEffectKill(Player $player){
        if (self::getConfig()->exists($player->getName() . "-kill-thunder")){
            self::getConfig()->remove($player->getName() . "-kill-thunder");
            self::getConfig()->save();
        }
        if (self::getConfig()->exists($player->getName() . "-kill-firework")){
            self::getConfig()->remove($player->getName() . "-kill-firework");
            self::getConfig()->save();
        }
        if (self::getConfig()->exists($player->getName() . "-kill-explosion")){
            self::getConfig()->remove($player->getName() . "-kill-explosion");
            self::getConfig()->save();
        }
        if (self::getConfig()->exists($player->getName() . "-kill-creeper")){
            self::getConfig()->remove($player->getName() . "-kill-creeper");
            self::getConfig()->save();
        }
    }

    public static function removeAllEffectDeath(Player $player){
        if (self::getConfig()->exists($player->getName() . "-death-thunder")){
            self::getConfig()->remove($player->getName() . "-death-thunder");
            self::getConfig()->save();
        }
        if (self::getConfig()->exists($player->getName() . "-death-firework")){
            self::getConfig()->remove($player->getName() . "-death-firework");
            self::getConfig()->save();
        }
        if (self::getConfig()->exists($player->getName() . "-death-explosion")){
            self::getConfig()->remove($player->getName() . "-death-explosion");
            self::getConfig()->save();
        }
        if (self::getConfig()->exists($player->getName() . "-death-creeper")){
            self::getConfig()->remove($player->getName() . "-death-creeper");
            self::getConfig()->save();
        }
    }

    public static function removeAllEffectJoin(Player $player){
        if (self::getConfig()->exists($player->getName() . "-join-thunder")){
            self::getConfig()->remove($player->getName() . "-join-thunder");
            self::getConfig()->save();
        }
        if (self::getConfig()->exists($player->getName() . "-join-firework")){
            self::getConfig()->remove($player->getName() . "-join-firework");
            self::getConfig()->save();
        }
        if (self::getConfig()->exists($player->getName() . "-join-explosion")){
            self::getConfig()->remove($player->getName() . "-join-explosion");
            self::getConfig()->save();
        }
        if (self::getConfig()->exists($player->getName() . "-join-creeper")){
            self::getConfig()->remove($player->getName() . "-join-creeper");
            self::getConfig()->save();
        }
    }

    public static function removeAllEffectQuit(Player $player){
        if (self::getConfig()->exists($player->getName() . "-quit-thunder")){
            self::getConfig()->remove($player->getName() . "-quit-thunder");
            self::getConfig()->save();
        }
        if (self::getConfig()->exists($player->getName() . "-quit-firework")){
            self::getConfig()->remove($player->getName() . "-quit-firework");
            self::getConfig()->save();
        }
        if (self::getConfig()->exists($player->getName() . "-quit-explosion")){
            self::getConfig()->remove($player->getName() . "-quit-explosion");
            self::getConfig()->save();
        }
        if (self::getConfig()->exists($player->getName() . "-quit-creeper")){
            self::getConfig()->remove($player->getName() . "-quit-creeper");
            self::getConfig()->save();
        }
    }

    public static function sendEffectKill(Player $damager, Player $entity){
        if (Cosmetique::isEnable($damager, "kill-thunder")){
            Functions::sendEffect($damager, $entity, "minecraft:lightning_bolt");
            Functions::sendSound($damager, "ambient.weather.thunder");
        }elseif (Cosmetique::isEnable($damager, "kill-firework")){
            $fw = Item::get(Item::FIREWORKS);
            $fw->addExplosion(Fireworks::TYPE_HUGE_SPHERE, Fireworks::COLOR_WHITE, "", false, false);
            $fw->setFlightDuration(0.1);
            $level = Server::getInstance()->getDefaultLevel();
            $nbt = FireworksRocket::createBaseNBT(new Vector3($damager->getX(), $damager->getY(), $damager->getZ()), new Vector3(0.001, 0.05, 0.001), lcg_value() * 360, 90);
            $entity = FireworksRocket::createEntity("FireworksRocket", $level, $nbt, $fw);
            if ($entity instanceof FireworksRocket){
                $entity->spawnTo($damager);
            }
        }elseif (Cosmetique::isEnable($damager, "kill-creeper")){
            $fw = Item::get(Item::FIREWORKS);
            $fw->addExplosion(Fireworks::TYPE_CREEPER_HEAD, Fireworks::COLOR_GREEN, "", false, false);
            $fw->setFlightDuration(0.1);
            $level = Server::getInstance()->getDefaultLevel();
            $nbt = FireworksRocket::createBaseNBT(new Vector3($damager->getX(), $damager->getY(), $damager->getZ()), new Vector3(0.001, 0.05, 0.001), lcg_value() * 360, 90);
            $entity = FireworksRocket::createEntity("FireworksRocket", $level, $nbt, $fw);
            if ($entity instanceof FireworksRocket){
                $entity->spawnTo($damager);
            }
        }elseif (Cosmetique::isEnable($damager, "kill-explosion")){
            Functions::sendFakeExplosion($damager, $entity);
        }
    }

    public static function sendEffectDeath(Player $damager, Player $entity){
        if (Cosmetique::isEnable($damager, "death-thunder")){
            foreach ($damager->getLevel()->getPlayers() as $player) {
                Functions::sendEffect($player, $damager, "minecraft:lightning_bolt");
                Functions::sendSound($player, "ambient.weather.thunder");
            }
        }elseif (Cosmetique::isEnable($damager, "death-firework")){
            $fw = Item::get(Item::FIREWORKS);
            $fw->addExplosion(Fireworks::TYPE_HUGE_SPHERE, Fireworks::COLOR_WHITE, "", false, false);
            $fw->setFlightDuration(1);
            $level = Server::getInstance()->getDefaultLevel();
            $nbt = FireworksRocket::createBaseNBT(new Vector3($entity->getX(), $entity->getY(), $entity->getZ()), new Vector3(0.001, 0.05, 0.001), lcg_value() * 360, 90);
            $entity = FireworksRocket::createEntity("FireworksRocket", $level, $nbt, $fw);
            if ($entity instanceof FireworksRocket){
                $entity->spawnToAll();
            }
        }elseif (Cosmetique::isEnable($damager, "death-creeper")){
            $fw = Item::get(Item::FIREWORKS);
            $fw->addExplosion(Fireworks::TYPE_CREEPER_HEAD, Fireworks::COLOR_GREEN, "", false, false);
            $fw->setFlightDuration(1);
            $level = Server::getInstance()->getDefaultLevel();
            $nbt = FireworksRocket::createBaseNBT(new Vector3($entity->getX(), $entity->getY(), $entity->getZ()), new Vector3(0.001, 0.05, 0.001), lcg_value() * 360, 90);
            $entity = FireworksRocket::createEntity("FireworksRocket", $level, $nbt, $fw);
            if ($entity instanceof FireworksRocket){
                $entity->spawnToAll();
            }
        }elseif (Cosmetique::isEnable($damager, "death-explosion")){
            Functions::sendFakeExplosion($damager, $entity);
        }
    }

    public static function sendEffectJoin(Player $player){
        if (Cosmetique::isEnable($player, "join-thunder")){
            foreach ($player->getLevel()->getPlayers() as $pla) {
                Functions::sendEffect($pla, $player, "minecraft:lightning_bolt");
                Functions::sendSound($pla, "ambient.weather.thunder");
            }
        }elseif (Cosmetique::isEnable($player, "join-firework")){
            $fw = Item::get(Item::FIREWORKS);
            $fw->addExplosion(Fireworks::TYPE_HUGE_SPHERE, Fireworks::COLOR_WHITE, "", false, false);
            $fw->setFlightDuration(1);
            $level = Server::getInstance()->getDefaultLevel();
            $nbt = FireworksRocket::createBaseNBT(new Vector3(-440.5, 68, -373.5), new Vector3(0.001, 0.05, 0.001), lcg_value() * 360, 90);
            $entity = FireworksRocket::createEntity("FireworksRocket", $level, $nbt, $fw);
            if ($entity instanceof FireworksRocket){
                $entity->spawnToAll();
            }
        }elseif (Cosmetique::isEnable($player, "join-creeper")){
            $fw = Item::get(Item::FIREWORKS);
            $fw->addExplosion(Fireworks::TYPE_CREEPER_HEAD, Fireworks::COLOR_GREEN, "", false, false);
            $fw->setFlightDuration(1);
            $level = Server::getInstance()->getDefaultLevel();
            $nbt = FireworksRocket::createBaseNBT(new Vector3(-440.5, 68, -373.5), new Vector3(0.001, 0.05, 0.001), lcg_value() * 360, 90);
            $entity = FireworksRocket::createEntity("FireworksRocket", $level, $nbt, $fw);
            if ($entity instanceof FireworksRocket){
                $entity->spawnToAll();
            }
        }elseif (Cosmetique::isEnable($player, "join-explosion")){
            foreach ($player->getLevel()->getPlayers() as $pla) {
                Functions::sendFakeExplosion($pla, $player);
            }
        }
    }

    public static function sendEffectQuit(Player $player){
        if (Cosmetique::isEnable($player, "quit-thunder")){
            foreach ($player->getLevel()->getPlayers() as $pla) {
                Functions::sendEffect($pla, $player, "minecraft:lightning_bolt");
                Functions::sendSound($pla, "ambient.weather.thunder");
            }
        }elseif (Cosmetique::isEnable($player, "quit-firework")){
            $fw = Item::get(Item::FIREWORKS);
            $fw->addExplosion(Fireworks::TYPE_HUGE_SPHERE, Fireworks::COLOR_WHITE, "", false, false);
            $fw->setFlightDuration(1);
            $level = Server::getInstance()->getDefaultLevel();
            $nbt = FireworksRocket::createBaseNBT(new Vector3($player->getX(), $player->getY(), $player->getZ()), new Vector3(0.001, 0.05, 0.001), lcg_value() * 360, 90);
            $entity = FireworksRocket::createEntity("FireworksRocket", $level, $nbt, $fw);
            if ($entity instanceof FireworksRocket){
                $entity->spawnToAll();
            }
        }elseif (Cosmetique::isEnable($player, "quit-creeper")){
            $fw = Item::get(Item::FIREWORKS);
            $fw->addExplosion(Fireworks::TYPE_CREEPER_HEAD, Fireworks::COLOR_GREEN, "", false, false);
            $fw->setFlightDuration(1);
            $level = Server::getInstance()->getDefaultLevel();
            $nbt = FireworksRocket::createBaseNBT(new Vector3($player->getX(), $player->getY(), $player->getZ()), new Vector3(0.001, 0.05, 0.001), lcg_value() * 360, 90);
            $entity = FireworksRocket::createEntity("FireworksRocket", $level, $nbt, $fw);
            if ($entity instanceof FireworksRocket){
                $entity->spawnToAll();
            }
        }elseif (Cosmetique::isEnable($player, "quit-explosion")){
            foreach ($player->getLevel()->getPlayers() as $pla) {
                Functions::sendFakeExplosion($pla, $player);
            }
        }
    }


    /* Cosmetique */
    public static function sendCosmeticMenu(Player $player){
        $ui = new SimpleForm(function (Player $player, $data){
            if ($data === null){
                return;
            }
            switch ($data){
                case 0:
                    break;
                case 1:
                    self::sendMyCosmetic($player);
                    break;
            }
        });
        $ui->setTitle(Manager::FORM_TITLE);
        $ui->addButton("Acheter");
        $ui->addButton("Voir mes cosmetiques");
        $ui->sendToPlayer($player);
    }

    public static function sendMyCosmetic(Player $player){
        $ui = new SimpleForm(function (Player $player, $data){
            if ($data === null){
                return;
            }
            switch ($data){
                case 0:
                    self::sendSelectEffectKill($player);
                    break;
                case 1:
                    self::sendSelectEffectDeath($player);
                    break;
                case 2:
                    self::sendSelectJoin($player);
                    break;
                case 3:
                    self::sendSelectQuit($player);
                    break;
            }
        });
        $ui->setTitle(Manager::FORM_TITLE);
        $ui->addButton("Effet de kill.");
        $ui->addButton("Effet de mort.");
        $ui->addButton("Lorsque vous rejoignez le serveur.");
        $ui->addButton("Lorsque vous quittez le serveur.");
        $ui->sendToPlayer($player);
    }

    public static function sendSelectJoin(Player $player){
        $ui = new SimpleForm(function (Player $player, $data){
            if ($data === null){
                return;
            }
            switch ($data){
                case 0:
                    if ($player->hasPermission(Cosmetique::JOIN_THUNDER)){
                        Cosmetique::removeAllEffectJoin($player);
                        $config = Cosmetique::getConfig();
                        $config->set($player->getName() . "-join-thunder", true);
                        $config->save();
                        $player->sendMessage(Manager::PREFIX_INFOS . "Vous venez d'activer l'effet de join §bÉclair§f.");
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 1:
                    if ($player->hasPermission(Cosmetique::JOIN_FIREWORK)){
                        Cosmetique::removeAllEffectJoin($player);
                        $config = Cosmetique::getConfig();
                        $config->set($player->getName() . "-join-firework", true);
                        $config->save();
                        $player->sendMessage(Manager::PREFIX_INFOS . "Vous venez d'activer l'effet de join §bFeux d'artifice§f.");
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 2:
                    if ($player->hasPermission(Cosmetique::JOIN_EXPLOSION)){
                        Cosmetique::removeAllEffectJoin($player);
                        $config = Cosmetique::getConfig();
                        $config->set($player->getName() . "-join-explosion", true);
                        $config->save();
                        $player->sendMessage(Manager::PREFIX_INFOS . "Vous venez d'activer l'effet de join §bExplosion§f.");
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 3:
                    if ($player->hasPermission(Cosmetique::JOIN_CREEPER)){
                        Cosmetique::removeAllEffectJoin($player);
                        $config = Cosmetique::getConfig();
                        $config->set($player->getName() . "-join-creeper", true);
                        $config->save();
                        $player->sendMessage(Manager::PREFIX_INFOS . "Vous venez d'activer l'effet de join §bCreeper§f.");
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 4:
                    Cosmetique::removeAllEffectJoin($player);
                    $player->sendMessage(Manager::PREFIX_INFOS . "Vous n'aurez plus d'effet de join.");
                    break;
            }
        });
        $ui->setTitle(Manager::FORM_TITLE);
        $ui->setContent("§f- §7Que voulez-vous sélectionner ?");
        if ($player->hasPermission(Cosmetique::JOIN_THUNDER)){
            $ui->addButton("§aÉclair");
        }else{
            $ui->addButton("§cÉclair");
        }
        if ($player->hasPermission(Cosmetique::JOIN_FIREWORK)){
            $ui->addButton("§aFeux d'artifice");
        }else{
            $ui->addButton("§cFeux d'artifice");
        }
        if ($player->hasPermission(Cosmetique::JOIN_EXPLOSION)){
            $ui->addButton("§aExplosion");
        }else{
            $ui->addButton("§cExplosion");
        }
        if ($player->hasPermission(Cosmetique::JOIN_CREEPER)){
            $ui->addButton("§aCreeper");
        }else{
            $ui->addButton("§cCreeper");
        }
        $ui->addButton("Retirer l'effet de join.");
        $ui->sendToPlayer($player);
    }

    public static function sendSelectQuit(Player $player){
        $ui = new SimpleForm(function (Player $player, $data){
            if ($data === null){
                return;
            }
            switch ($data){
                case 0:
                    if ($player->hasPermission(Cosmetique::QUIT_THUNDER)){
                        Cosmetique::removeAllEffectQuit($player);
                        $config = Cosmetique::getConfig();
                        $config->set($player->getName() . "-quit-thunder", true);
                        $config->save();
                        $player->sendMessage(Manager::PREFIX_INFOS . "Vous venez d'activer l'effet de quit §bÉclair§f.");
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 1:
                    if ($player->hasPermission(Cosmetique::QUIT_FIREWORK)){
                        Cosmetique::removeAllEffectQuit($player);
                        $config = Cosmetique::getConfig();
                        $config->set($player->getName() . "-quit-firework", true);
                        $config->save();
                        $player->sendMessage(Manager::PREFIX_INFOS . "Vous venez d'activer l'effet de quit §bFeux d'artifice§f.");
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 2:
                    if ($player->hasPermission(Cosmetique::JOIN_EXPLOSION)){
                        Cosmetique::removeAllEffectQuit($player);
                        $config = Cosmetique::getConfig();
                        $config->set($player->getName() . "-quit-explosion", true);
                        $config->save();
                        $player->sendMessage(Manager::PREFIX_INFOS . "Vous venez d'activer l'effet de quit §bExplosion§f.");
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 3:
                    if ($player->hasPermission(Cosmetique::QUIT_CREEPER)){
                        Cosmetique::removeAllEffectQuit($player);
                        $config = Cosmetique::getConfig();
                        $config->set($player->getName() . "-quit-creeper", true);
                        $config->save();
                        $player->sendMessage(Manager::PREFIX_INFOS . "Vous venez d'activer l'effet de quit §bCreeper§f.");
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 4:
                    Cosmetique::removeAllEffectQuit($player);
                    $player->sendMessage(Manager::PREFIX_INFOS . "Vous n'aurez plus d'effet de quit.");
                    break;
            }
        });
        $ui->setTitle(Manager::FORM_TITLE);
        $ui->setContent("§f- §7Que voulez-vous sélectionner ?");
        if ($player->hasPermission(Cosmetique::QUIT_THUNDER)){
            $ui->addButton("§aÉclair");
        }else{
            $ui->addButton("§cÉclair");
        }
        if ($player->hasPermission(Cosmetique::QUIT_FIREWORK)){
            $ui->addButton("§aFeux d'artifice");
        }else{
            $ui->addButton("§cFeux d'artifice");
        }
        if ($player->hasPermission(Cosmetique::QUIT_EXPLOSION)){
            $ui->addButton("§aExplosion");
        }else{
            $ui->addButton("§cExplosion");
        }
        if ($player->hasPermission(Cosmetique::QUIT_CREEPER)){
            $ui->addButton("§aCreeper");
        }else{
            $ui->addButton("§cCreeper");
        }
        $ui->addButton("Retirer l'effet de join.");
        $ui->sendToPlayer($player);
    }

    public static function sendSelectEffectDeath(Player $player){
        $ui = new SimpleForm(function (Player $player, $data){
            if ($data === null){
                return;
            }
            switch ($data){
                case 0:
                    if ($player->hasPermission(Cosmetique::EFFECT_THUNDER)){
                        Cosmetique::removeAllEffectDeath($player);
                        $config = Cosmetique::getConfig();
                        $config->set($player->getName() . "-death-thunder", true);
                        $config->save();
                        $player->sendMessage(Manager::PREFIX_INFOS . "Vous venez d'activer l'effet de mort §bÉclair§f.");
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 1:
                    if ($player->hasPermission(Cosmetique::EFFECT_FIREWORK)){
                        Cosmetique::removeAllEffectDeath($player);
                        $config = Cosmetique::getConfig();
                        $config->set($player->getName() . "-death-firework", true);
                        $config->save();
                        $player->sendMessage(Manager::PREFIX_INFOS . "Vous venez d'activer l'effet de mort §bFeux d'artifice§f.");
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 2:
                    if ($player->hasPermission(Cosmetique::EFFECT_EXPLOSION)){
                        Cosmetique::removeAllEffectDeath($player);
                        $config = Cosmetique::getConfig();
                        $config->set($player->getName() . "-death-explosion", true);
                        $config->save();
                        $player->sendMessage(Manager::PREFIX_INFOS . "Vous venez d'activer l'effet de mort §bExplosion§f.");
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 3:
                    if ($player->hasPermission(Cosmetique::EFFECT_CREEPER)){
                        Cosmetique::removeAllEffectDeath($player);
                        $config = Cosmetique::getConfig();
                        $config->set($player->getName() . "-death-creeper", true);
                        $config->save();
                        $player->sendMessage(Manager::PREFIX_INFOS . "Vous venez d'activer l'effet de mort §bCreeper§f.");
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 4:
                    Cosmetique::removeAllEffectDeath($player);
                    $player->sendMessage(Manager::PREFIX_INFOS . "Vous n'aurez plus d'effet de mort.");
                    break;
            }
        });
        $ui->setTitle(Manager::FORM_TITLE);
        $ui->setContent("§f- §7Quel effet de mort voulez-vous sélectionner ?");
        if ($player->hasPermission(Cosmetique::EFFECT_THUNDER)){
            $ui->addButton("§aÉclair");
        }else{
            $ui->addButton("§cÉclair");
        }
        if ($player->hasPermission(Cosmetique::EFFECT_FIREWORK)){
            $ui->addButton("§aFeux d'artifice");
        }else{
            $ui->addButton("§cFeux d'artifice");
        }
        if ($player->hasPermission(Cosmetique::EFFECT_EXPLOSION)){
            $ui->addButton("§aExplosion");
        }else{
            $ui->addButton("§cExplosion");
        }
        if ($player->hasPermission(Cosmetique::EFFECT_CREEPER)){
            $ui->addButton("§aCreeper");
        }else{
            $ui->addButton("§cCreeper");
        }
        $ui->addButton("Retirer l'effet de mort.");
        $ui->sendToPlayer($player);
    }

    public static function sendSelectEffectKill(Player $player){
        $ui = new SimpleForm(function (Player $player, $data){
            if ($data === null){
                return;
            }
            switch ($data){
                case 0:
                    if ($player->hasPermission(Cosmetique::EFFECT_THUNDER)){
                        Cosmetique::removeAllEffectKill($player);
                        $config = Cosmetique::getConfig();
                        $config->set($player->getName() . "-kill-thunder", true);
                        $config->save();
                        $player->sendMessage(Manager::PREFIX_INFOS . "Vous venez d'activer l'effet de kill §bÉclair§f.");
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 1:
                    if ($player->hasPermission(Cosmetique::EFFECT_FIREWORK)){
                        Cosmetique::removeAllEffectKill($player);
                        $config = Cosmetique::getConfig();
                        $config->set($player->getName() . "-kill-firework", true);
                        $config->save();
                        $player->sendMessage(Manager::PREFIX_INFOS . "Vous venez d'activer l'effet de kill §bFeux d'artifice§f.");
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 2:
                    if ($player->hasPermission(Cosmetique::EFFECT_EXPLOSION)){
                        Cosmetique::removeAllEffectKill($player);
                        $config = Cosmetique::getConfig();
                        $config->set($player->getName() . "-kill-explosion", true);
                        $config->save();
                        $player->sendMessage(Manager::PREFIX_INFOS . "Vous venez d'activer l'effet de kill §bExplosion§f.");
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 3:
                    if ($player->hasPermission(Cosmetique::EFFECT_CREEPER)){
                        Cosmetique::removeAllEffectKill($player);
                        $config = Cosmetique::getConfig();
                        $config->set($player->getName() . "-kill-creeper", true);
                        $config->save();
                        $player->sendMessage(Manager::PREFIX_INFOS . "Vous venez d'activer l'effet de kill §bCreeper§f.");
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 4:
                    Cosmetique::removeAllEffectKill($player);
                    $player->sendMessage(Manager::PREFIX_INFOS . "Vous n'aurez plus d'effet de kill.");
                    break;
            }
        });
        $ui->setTitle(Manager::FORM_TITLE);
        $ui->setContent("§f- §7Quel effet de kill voulez-vous sélectionner ?");
        if ($player->hasPermission(Cosmetique::EFFECT_THUNDER)){
            $ui->addButton("§aÉclair");
        }else{
            $ui->addButton("§cÉclair");
        }
        if ($player->hasPermission(Cosmetique::EFFECT_FIREWORK)){
            $ui->addButton("§aFeux d'artifice");
        }else{
            $ui->addButton("§cFeux d'artifice");
        }
        if ($player->hasPermission(Cosmetique::EFFECT_EXPLOSION)){
            $ui->addButton("§aExplosion");
        }else{
            $ui->addButton("§cExplosion");
        }
        if ($player->hasPermission(Cosmetique::EFFECT_CREEPER)){
            $ui->addButton("§aCreeper");
        }else{
            $ui->addButton("§cCreeper");
        }
        $ui->addButton("Retirer l'effet de kill.");
        $ui->sendToPlayer($player);
    }

    public static function sendCategoryCape(Player $player){
        $ui = new SimpleForm(function (Player $player, $data){
            if ($data === null){
                return;
            }
            switch ($data){
                case 0:
                    Cosmetique::setCape($player, "");
                    $player->sendMessage(Manager::PREFIX_INFOS . "Vous avez bien retirer votre cape.");
                    break;
                case 1:
                    self::sendPaysCape($player);
                    break;
                case 2:
                    self::sendLunarCape($player);
                    break;
                case 3:
                    self::sendOthersCape($player);
                    break;
            }
        });
        $ui->setTitle(Manager::FORM_TITLE);
        $ui->addButton("Retirer la cape actuel");
        $ui->addButton("Pays");
        $ui->addButton("Lunar");
        $ui->addButton("Autres");
        $ui->sendToPlayer($player);
    }

    public static function sendOthersCape(Player $player){
        $ui = new SimpleForm(function (Player $player, $data){
            if ($data === null){
                return;
            }
            switch ($data){
                case 0:
                    if ($player->hasPermission(Cosmetique::CAPE_ANDROID)){
                        Cosmetique::setCape($player, Main::getInstance()->getDataFolder() . "cape/Android");
                        $player->sendMessage(Manager::PREFIX_INFOS . "Votre cape est désormais la cape Android.");
                        return;
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 1:
                    if ($player->hasPermission(Cosmetique::CAPE_BADLION)){
                        Cosmetique::setCape($player, Main::getInstance()->getDataFolder() . "cape/Badlion");
                        $player->sendMessage(Manager::PREFIX_INFOS . "Votre cape est désormais la cape Badlion.");
                        return;
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 2:
                    if ($player->hasPermission(Cosmetique::CAPE_NOEL)){
                        Cosmetique::setCape($player, Main::getInstance()->getDataFolder() . "cape/ChristmasTree");
                        $player->sendMessage(Manager::PREFIX_INFOS . "Votre cape est désormais la cape Noël.");
                        return;
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 3:
                    if ($player->hasPermission(Cosmetique::CAPE_JAPONAIS)){
                        Cosmetique::setCape($player, Main::getInstance()->getDataFolder() . "cape/CherryTree");
                        $player->sendMessage(Manager::PREFIX_INFOS . "Votre cape est désormais la cape Japonais.");
                        return;
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 4:
                    if ($player->hasPermission(Cosmetique::CAPE_DRAGON)){
                        Cosmetique::setCape($player, Main::getInstance()->getDataFolder() . "cape/Dragon");
                        $player->sendMessage(Manager::PREFIX_INFOS . "Votre cape est désormais la cape Dragon.");
                        return;
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 5:
                    if ($player->hasPermission(Cosmetique::CAPE_CROWN)){
                        Cosmetique::setCape($player, Main::getInstance()->getDataFolder() . "cape/Crown");
                        $player->sendMessage(Manager::PREFIX_INFOS . "Votre cape est désormais la cape Couronne.");
                        return;
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 6:
                    if ($player->hasPermission(Cosmetique::CAPE_DEADPOOL)){
                        Cosmetique::setCape($player, Main::getInstance()->getDataFolder() . "cape/Deadpool");
                        $player->sendMessage(Manager::PREFIX_INFOS . "Votre cape est désormais la cape Deadpool.");
                        return;
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 7:
                    if ($player->hasPermission(Cosmetique::CAPE_RETRO)){
                        Cosmetique::setCape($player, Main::getInstance()->getDataFolder() . "cape/Vaporwave");
                        $player->sendMessage(Manager::PREFIX_INFOS . "Votre cape est désormais la cape Retro.");
                        return;
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 8:
                    if ($player->hasPermission(Cosmetique::CAPE_TWITCH)){
                        Cosmetique::setCape($player, Main::getInstance()->getDataFolder() . "cape/Twitch");
                        $player->sendMessage(Manager::PREFIX_INFOS . "Votre cape est désormais la cape Twitch.");
                        return;
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 9:
                    if ($player->hasPermission(Cosmetique::CAPE_YOUTUBE)){
                        Cosmetique::setCape($player, Main::getInstance()->getDataFolder() . "cape/YouTube");
                        $player->sendMessage(Manager::PREFIX_INFOS . "Votre cape est désormais la cape Youtube.");
                        return;
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
            }
        });
        $ui->setTitle(Manager::FORM_TITLE);
        if ($player->hasPermission(Cosmetique::CAPE_ANDROID)){
            $ui->addButton("§aAndroid");
        }else{
            $ui->addButton("§cAndroid");
        }
        if ($player->hasPermission(Cosmetique::CAPE_BADLION)){
            $ui->addButton("§aBadlion");
        }else{
            $ui->addButton("§cBadlion");
        }
        if ($player->hasPermission(Cosmetique::CAPE_NOEL)){
            $ui->addButton("§aNoël");
        }else{
            $ui->addButton("§cNoël");
        }
        if ($player->hasPermission(Cosmetique::CAPE_JAPONAIS)){
            $ui->addButton("§aJaponais");
        }else{
            $ui->addButton("§cJaponais");
        }
        if ($player->hasPermission(Cosmetique::CAPE_DRAGON)){
            $ui->addButton("§aDragon");
        }else{
            $ui->addButton("§cDragon");
        }
        if ($player->hasPermission(Cosmetique::CAPE_CROWN)){
            $ui->addButton("§aCouronne");
        }else{
            $ui->addButton("§cCouronne");
        }
        if ($player->hasPermission(Cosmetique::CAPE_DEADPOOL)){
            $ui->addButton("§aDeadPool");
        }else{
            $ui->addButton("§cDeadPool");
        }
        if ($player->hasPermission(Cosmetique::CAPE_RETRO)){
            $ui->addButton("§aRetro");
        }else{
            $ui->addButton("§cRetro");
        }
        if ($player->hasPermission(Cosmetique::CAPE_TWITCH)){
            $ui->addButton("§aTwitch");
        }else{
            $ui->addButton("§cTwitch");
        }
        if ($player->hasPermission(Cosmetique::CAPE_YOUTUBE)){
            $ui->addButton("§aYoutube");
        }else{
            $ui->addButton("§cYoutube");
        }
        $ui->sendToPlayer($player);
    }

    public static function sendPaysCape(Player $player){
        $ui = new SimpleForm(function (Player $player, $data){
            if ($data === null){
                return;
            }
            switch ($data){
                case 0:
                    if ($player->hasPermission(Cosmetique::CAPE_FRENCH)){
                        Cosmetique::setCape($player, Main::getInstance()->getDataFolder() . "cape/FranceFlag");
                        $player->sendMessage(Manager::PREFIX_INFOS . "Votre cape est désormais la cape Française.");
                        return;
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 1:
                    if ($player->hasPermission(Cosmetique::CAPE_ARGENTINA)){
                        Cosmetique::setCape($player, Main::getInstance()->getDataFolder() . "cape/ArgentinaFlag");
                        $player->sendMessage(Manager::PREFIX_INFOS . "Votre cape est désormais la cape Argentine.");
                        return;
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 2:
                    if ($player->hasPermission(Cosmetique::CAPE_GUATEMALA)){
                        Cosmetique::setCape($player, Main::getInstance()->getDataFolder() . "cape/GuatemalaFlag");
                        $player->sendMessage(Manager::PREFIX_INFOS . "Votre cape est désormais la cape du Guatemala.");
                        return;
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 3:
                    if ($player->hasPermission(Cosmetique::CAPE_INDIA)){
                        Cosmetique::setCape($player, Main::getInstance()->getDataFolder() . "cape/IndiaFlag");
                        $player->sendMessage(Manager::PREFIX_INFOS . "Votre cape est désormais la cape de l'Inde.");
                        return;
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 4:
                    if ($player->hasPermission(Cosmetique::CAPE_INDONESIA)){
                        Cosmetique::setCape($player, Main::getInstance()->getDataFolder() . "cape/IndonesiaFlag");
                        $player->sendMessage(Manager::PREFIX_INFOS . "Votre cape est désormais la cape de l'Indonesie.");
                        return;
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 5:
                    if ($player->hasPermission(Cosmetique::CAPE_IRELAND)){
                        Cosmetique::setCape($player, Main::getInstance()->getDataFolder() . "cape/IrelandFlag");
                        $player->sendMessage(Manager::PREFIX_INFOS . "Votre cape est désormais la cape de l'Ireland.");
                        return;
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 6:
                    if ($player->hasPermission(Cosmetique::CAPE_ISRAEL)){
                        Cosmetique::setCape($player, Main::getInstance()->getDataFolder() . "cape/IsraelFlag");
                        $player->sendMessage(Manager::PREFIX_INFOS . "Votre cape est désormais la cape de l'Israel.");
                        return;
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 7:
                    if ($player->hasPermission(Cosmetique::CAPE_ITALY)){
                        Cosmetique::setCape($player, Main::getInstance()->getDataFolder() . "cape/ItalyFlag");
                        $player->sendMessage(Manager::PREFIX_INFOS . "Votre cape est désormais la cape de l'Italie.");
                        return;
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 8:
                    if ($player->hasPermission(Cosmetique::CAPE_MEXICO)){
                        Cosmetique::setCape($player, Main::getInstance()->getDataFolder() . "cape/MexicoFlag");
                        $player->sendMessage(Manager::PREFIX_INFOS . "Votre cape est désormais la cape du Mexique.");
                        return;
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 9:
                    if ($player->hasPermission(Cosmetique::CAPE_USA)){
                        Cosmetique::setCape($player, Main::getInstance()->getDataFolder() . "cape/USAFlag");
                        $player->sendMessage(Manager::PREFIX_INFOS . "Votre cape est désormais la cape des États-Unis.");
                        return;
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 10:
                    if ($player->hasPermission(Cosmetique::CAPE_UK)){
                        Cosmetique::setCape($player, Main::getInstance()->getDataFolder() . "cape/UKFlag");
                        $player->sendMessage(Manager::PREFIX_INFOS . "Votre cape est désormais la cape de l'Angleterre.");
                        return;
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 11:
                    if ($player->hasPermission(Cosmetique::CAPE_NEW_ZEALAND)){
                        Cosmetique::setCape($player, Main::getInstance()->getDataFolder() . "cape/NewZealandFlag");
                        $player->sendMessage(Manager::PREFIX_INFOS . "Votre cape est désormais la cape des Nouvelles Zélande.");
                        return;
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
            }
        });
        $ui->setTitle(Manager::FORM_TITLE);
        if ($player->hasPermission(Cosmetique::CAPE_FRENCH)) {
            $ui->addButton("§aFrançais");
        }else{
            $ui->addButton("§cFrançais");
        }
        if ($player->hasPermission(Cosmetique::CAPE_ARGENTINA)) {
            $ui->addButton("§aArgentine");
        }else{
            $ui->addButton("§cArgentine");
        }
        if ($player->hasPermission(Cosmetique::CAPE_GUATEMALA)) {
            $ui->addButton("§aGuatemala");
        }else{
            $ui->addButton("§cGuatemala");
        }
        if ($player->hasPermission(Cosmetique::CAPE_INDIA)) {
            $ui->addButton("§aInde");
        }else{
            $ui->addButton("§cInde");
        }
        if ($player->hasPermission(Cosmetique::CAPE_INDONESIA)) {
            $ui->addButton("§aIndonesie");
        }else{
            $ui->addButton("§cIndonesie");
        }
        if ($player->hasPermission(Cosmetique::CAPE_IRELAND)) {
            $ui->addButton("§aIreland");
        }else{
            $ui->addButton("§cIreland");
        }
        if ($player->hasPermission(Cosmetique::CAPE_ISRAEL)) {
            $ui->addButton("§aIsrael");
        }else{
            $ui->addButton("§cIsrael");
        }
        if ($player->hasPermission(Cosmetique::CAPE_ITALY)) {
            $ui->addButton("§aItalie");
        }else{
            $ui->addButton("§cItalie");
        }
        if ($player->hasPermission(Cosmetique::CAPE_MEXICO)) {
            $ui->addButton("§aMexique");
        }else{
            $ui->addButton("§cMexique");
        }
        if ($player->hasPermission(Cosmetique::CAPE_USA)) {
            $ui->addButton("§aUSA");
        }else{
            $ui->addButton("§cUSA");
        }
        if ($player->hasPermission(Cosmetique::CAPE_UK)) {
            $ui->addButton("§aAngleterre");
        }else{
            $ui->addButton("§cAngleterre");
        }
        if ($player->hasPermission(Cosmetique::CAPE_NEW_ZEALAND)) {
            $ui->addButton("§aNouvelle Zélande");
        }else{
            $ui->addButton("§cNouvelle Zélande");
        }
        $ui->sendToPlayer($player);
    }

    public static function sendLunarCape(Player $player){
        $ui = new SimpleForm(function (Player $player, $data){
            if ($data === null){
                return;
            }
            switch ($data){
                case 0:
                    if ($player->hasPermission(Cosmetique::CAPE_LUNAR)){
                        Cosmetique::setCape($player, Main::getInstance()->getDataFolder() . "cape/Lunar");
                        $player->sendMessage(Manager::PREFIX_INFOS . "Votre cape est désormais la cape de Lunar.");
                        return;
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 1:
                    if ($player->hasPermission(Cosmetique::CAPE_LUNAR)){
                        Cosmetique::setCape($player, Main::getInstance()->getDataFolder() . "cape/LunarRed");
                        $player->sendMessage(Manager::PREFIX_INFOS . "Votre cape est désormais la cape de Lunar Rouge.");
                        return;
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 2:
                    if ($player->hasPermission(Cosmetique::CAPE_LUNAR)){
                        Cosmetique::setCape($player, Main::getInstance()->getDataFolder() . "cape/LunarYellow");
                        $player->sendMessage(Manager::PREFIX_INFOS . "Votre cape est désormais la cape de Lunar Jaune.");
                        return;
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 3:
                    if ($player->hasPermission(Cosmetique::CAPE_LUNAR)){
                        Cosmetique::setCape($player, Main::getInstance()->getDataFolder() . "cape/LunarGreen");
                        $player->sendMessage(Manager::PREFIX_INFOS . "Votre cape est désormais la cape de Lunar Vert.");
                        return;
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 4:
                    if ($player->hasPermission(Cosmetique::CAPE_LUNAR)){
                        Cosmetique::setCape($player, Main::getInstance()->getDataFolder() . "cape/LunarBlue");
                        $player->sendMessage(Manager::PREFIX_INFOS . "Votre cape est désormais la cape de Lunar Bleu.");
                        return;
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 5:
                    if ($player->hasPermission(Cosmetique::CAPE_LUNAR)){
                        Cosmetique::setCape($player, Main::getInstance()->getDataFolder() . "cape/LunarOrange");
                        $player->sendMessage(Manager::PREFIX_INFOS . "Votre cape est désormais la cape de Lunar Orange.");
                        return;
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
                case 6:
                    if ($player->hasPermission(Cosmetique::CAPE_LUNAR)){
                        Cosmetique::setCape($player, Main::getInstance()->getDataFolder() . "cape/LunarPurple");
                        $player->sendMessage(Manager::PREFIX_INFOS . "Votre cape est désormais la cape de Lunar Violet.");
                        return;
                    }else{
                        $player->sendMessage(Cosmetique::NOT_HAVE_PERM);
                        return;
                    }
                    break;
            }
        });
        $ui->setTitle(Manager::FORM_TITLE);
        if ($player->hasPermission(Cosmetique::CAPE_LUNAR)) {
            $ui->addButton("§aLunar");
            $ui->addButton("§aLunar Rouge");
            $ui->addButton("§aLunar Jaune");
            $ui->addButton("§aLunar Vert");
            $ui->addButton("§aLunar Bleu");
            $ui->addButton("§aLunar Orange");
            $ui->addButton("§aLunar Violet");
        }else{
            $ui->addButton("§cLunar");
            $ui->addButton("§cLunar Rouge");
            $ui->addButton("§cLunar Jaune");
            $ui->addButton("§cLunar Vert");
            $ui->addButton("§cLunar Bleu");
            $ui->addButton("§cLunar Orange");
            $ui->addButton("§cLunar Violet");
        }
        $ui->sendToPlayer($player);
    }

}