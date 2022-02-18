<?php

namespace Zoumi\MiniGame\api;

use pocketmine\item\Item;
use pocketmine\Player;

class NavigatorManager {

    public static function sendCompass(Player $player){
        $item = Item::get(Item::COMPASS);
        $item->setCustomName("§r§l§5MINI §dJEUX");
        $player->getInventory()->setItem(4, $item);
    }

    public static function sendLilyPad(Player $player){
        $item = Item::get(Item::LILY_PAD);
        $item->setCustomName("§r§l§aJUMP");
        $player->getInventory()->setItem(0, $item);
    }

    public static function sendBlazePowder(Player $player){
        $item = Item::get(Item::BLAZE_POWDER);
        $item->setCustomName("§r§l§eCOSMETIQUE");
        $player->getInventory()->setItem(8, $item);
    }

    public static function sendMinecartWithChest(Player $player){
        $item = Item::get(Item::MINECART_WITH_CHEST);
        $item->setCustomName("§r§l§cENTRER UN CODE");
        $player->getInventory()->setItem(7, $item);
    }

    public static function sendNavigator(Player $player){
        $player->getArmorInventory()->clearAll();
        $player->getInventory()->clearAll();
        NavigatorManager::sendCompass($player);
        NavigatorManager::sendLilyPad($player);
        NavigatorManager::sendBlazePowder($player);
        NavigatorManager::sendMinecartWithChest($player);
    }

    public static function sendWool(Player $player){
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $item = Item::get(Item::WOOL);
        $item->setCustomName("§l§fALEATOIRE");
        $player->getInventory()->setItem(4, $item);
    }

    public static function sendWoolBlue(Player $player){
        $item = Item::get(Item::WOOL, 11);
        $item->setCustomName("§l§1BLEU");
        $player->getInventory()->setItem(4, $item);
    }

    public static function sendWoolRed(Player $player){
        $item = Item::get(Item::WOOL, 14);
        $item->setCustomName("§l§cROUGE");
        $player->getInventory()->setItem(4, $item);
    }

    public static function sendWoolYellow(Player $player){
        $item = Item::get(Item::WOOL, 4);
        $item->setCustomName("§l§fALEATOIRE");
        $player->getInventory()->setItem(4, $item);
    }

    public static function sendWoolGreen(Player $player){
        $item = Item::get(Item::WOOL, 5);
        $item->setCustomName("§l§2VERT");
        $player->getInventory()->setItem(4, $item);
    }

    public static function sendNavigatorForSelectTeam(Player $player){
        $player->getArmorInventory()->clearAll();
        $player->getInventory()->clearAll();
        self::sendWool($player);
    }

}