<?php

namespace Zoumi\MiniGame\listeners;

use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\Form;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\event\Listener;
use pocketmine\Player;
use Zoumi\MiniGame\api\game\Bedwars;
use Zoumi\MiniGame\api\game\CaptureTheFlag;
use Zoumi\MiniGame\api\Cosmetique;
use Zoumi\MiniGame\api\game\GameManager;
use Zoumi\MiniGame\api\game\GunPlay;
use Zoumi\MiniGame\api\game\Hikabrain;
use Zoumi\MiniGame\api\game\HikabrainInstance;
use Zoumi\MiniGame\api\game\SPlayer;
use Zoumi\MiniGame\api\NavigatorManager;
use Zoumi\MiniGame\api\game\PrivateGameManager;
use Zoumi\MiniGame\api\game\TeamManager;
use Zoumi\MiniGame\blocks\Bed;
use Zoumi\MiniGame\Main;
use Zoumi\MiniGame\Manager;
use Zoumi\MiniGame\utils\Functions;

class FormListener implements Listener {

    public static function sendMiniGame(SPlayer $player){
        $ui = new SimpleForm(function (SPlayer $player, $data){
            if ($data === null){
                return;
            }
            switch ($data){
                case 0:
                    FormListener::sendGunPlayMenu($player);
                    break;
                case 1:
                    FormListener::sendHikaMenu($player);
                    break;
                case 2:
                    FormListener::sendCTFMenu($player);
                    break;
                case 3:
                    FormListener::sendBedwarsMenu($player);
                    break;
            }
        });
        $ui->setTitle(Manager::FORM_TITLE);
        $ui->setContent("§fLégende: §6Partie en attente. §aPartie en cours.");
        $ui->addButton("Jeu d'arme\n§8(§6§8) (§a§8)");
        $ui->addButton("Hika Brain\n(§6" . Hikabrain::getGameInWaiting() . "§8) (§a" . Hikabrain::getPartyInPlaying() . "§8)");
        $ui->addButton("Capture du drapeau\n(§6" . CaptureTheFlag::getGameInWaiting() . "§8) (§a" . CaptureTheFlag::getPartyInPlaying() . "§8)");
        /*
        $ui->addButton("Bed Wars\n(§6" . Bedwars::getGameInWaiting() . "§8) (§a" . Bedwars::getPartyInPlaying() . "§8)");
        */
        $ui->sendToPlayer($player);
    }

    public static function sendBedwarsMenu(SPlayer $player){
        $ui = new SimpleForm(function (SPlayer $player, $data){
            if ($data === null){
                return;
            }
            switch ($data){
                case 0:
                    FormListener::sendCreateBedwars($player);
                    break;
                case 1:
                    FormListener::sendBedwarsList($player);
                    break;
                case 2:
                    FormListener::sendBedwarsQuit($player);
                    break;
            }
        });
        $ui->setTitle(Manager::FORM_TITLE);
        $ui->addButton("Créer un bedwars.");
        $ui->addButton("Voir les parties en cours.");
        $ui->addButton("Quitter la partie en cours.");
        $ui->sendToPlayer($player);
    }

    public static function sendCreateBedwars(SPlayer $player){
        $ui = new CustomForm(function (SPlayer $player, $data) {
            if ($data === null) {
                return;
            }
            if (Functions::isInGame($player)) {
                $player->sendMessage(Manager::PREFIX_ALERT . "Impossible de créer une partie de bedwars car vous faites déjà partie d'une partie.");
                return;
            }
            if ($data[1] === true) {
                if ($player->hasPermission(Manager::PERM_CREATE_PRIVATE_PARTIES)) {
                    Bedwars::createBedwars($player, true);
                    return;
                } else {
                    $player->sendMessage(Manager::NOT_PERM);
                    return;
                }
            }
            Bedwars::createBedwars($player, false);
        });
        $ui->setTitle(Manager::FORM_TITLE);
        $ui->addLabel("§fInformation: §7Une partie privée est rejoignable uniquement via le code donner une fois la partie créer.");
        $ui->addToggle("§f- §7Voulez-vous que se soit une partie privée ?", false);
        $ui->sendToPlayer($player);
    }

    public static function sendBedwarsList(SPlayer $player){
        $ui = new SimpleForm(function (SPlayer $player, $data){
            if ($data === null){
                return;
            }
            if (empty(Bedwars::$bedwars)){
                return;
            }else{
                $res = [];
                $i = 0;
                foreach (Bedwars::$bedwars as $id){
                    $res[$i] = $id;
                    $i++;
                }
                FormListener::sendBedwarsInfos($player, $res[$data]["name"]);
            }
        });
        $ui->setTitle(Manager::FORM_TITLE);
        if (empty(Bedwars::$bedwars)){
            $ui->setContent("§f- §7Aucun bedwars en cours.");
            $ui->addButton("§c§l<- QUITTER");
        }else{
            foreach (Bedwars::$bedwars as $id => $value){
                $ui->addButton($id . "\nStatus: " . Bedwars::$bedwars[$id]["status"] . " §8| §e" . Bedwars::getPlayersCount($id) . " §8joueur(s)");
            }
        }
        $ui->sendToPlayer($player);
    }

    public static function sendBedwarsInfos(SPlayer $player, string $id){
        $ui = new SimpleForm(function (SPlayer $player, $data) use ($id){
            if ($data === null){
                return;
            }
            switch ($data){
                case 0:
                    if (!isset(Bedwars::$bedwars[$id])){
                        $player->sendMessage(Manager::PREFIX_ALERT . "Cette partie n'existe plus.");
                        return;
                    }
                    if (Functions::isInGame($player)){
                        $player->sendMessage(Manager::PREFIX_ALERT . "Vous êtes déjà dans une partie.");
                        return;
                    }
                    if (Bedwars::$bedwars[$id]["isPrivate"] === true){
                        $player->sendMessage(Manager::PREFIX_ALERT . "Impossible de rejoindre cette partie car elle est privée.");
                        return;
                    }
                    if (Bedwars::getPlayersCount($id) >= Bedwars::getMaxPlayersCount($id)){
                        $player->sendMessage(Manager::PREFIX_ALERT . "Cette partie est complete.");
                        return;
                    }
                    $player->sendMessage(Manager::PREFIX_INFOS . "Vous êtes en train de rejoindre la partie §e" . $id . "§f.");
                    Bedwars::addPlayer($id, $player);
                    break;
                case 1:
                    break;
            }
        });
        $ui->setTitle(Manager::FORM_TITLE);
        $ui->setContent(
            "§6» §eNom: §7" . $id . "\n" .
            "§6» §eStatus: §7" . Bedwars::$bedwars[$id]["status"] . "\n" .
            "§6» §eJoueurs §f(" . Bedwars::getPlayersCount($id) . "/" . Bedwars::getMaxPlayersCount($id) . ")§e: §7" . Bedwars::getPlayersList($id)
        );
        $ui->addButton("Rejoindre");
        $ui->addButton("§c§l<- QUITTER");
        $ui->sendToPlayer($player);
    }

    public static function sendBedwarsQuit(SPlayer $player){
        $ui = new SimpleForm(function (SPlayer $player, $data){
            if ($data === null){
                return;
            }
            if (isset(Bedwars::$players[$player->getName()])){
                switch ($data){
                    case 0:
                        Bedwars::removePlayer(Bedwars::$players[$player->getName()], $player);
                        $player->sendMessage(Manager::PREFIX_INFOS . "Vous avez bien quitter la partie.");
                        break;
                    case 1:
                        break;
                }
            }
        });
        $ui->setTitle(Manager::FORM_TITLE);
        if (isset(Bedwars::$players[$player->getName()])){
            $ui->setContent("§f- §7Voulez-vous vraiment quitter ?");
            $ui->addButton("§aOui");
            $ui->addButton("§cNon");
        }else{
            $ui->setContent("§f- §7Vous n'êtes dans aucune partie.");
            $ui->addButton("§c§l<- QUITTER");
        }
        $ui->sendToPlayer($player);
    }

    public static function sendCTFMenu(SPlayer $player){
        $ui = new SimpleForm(function (SPlayer $player, $data){
            if ($data === null){
                return;
            }
            switch ($data){
                case 0:
                    FormListener::sendCreateCTF($player);
                    break;
                case 1:
                    FormListener::sendCTFList($player);
                    break;
                case 2:
                    FormListener::sendCTFQuit($player);
                    break;
            }
        });
        $ui->setTitle(Manager::FORM_TITLE);
        $ui->addButton("Créer un capture du drapeau.");
        $ui->addButton("Voir les parties en cours.");
        $ui->addButton("Quitter la partie en cours.");
        $ui->sendToPlayer($player);
    }

    public static function sendCreateCTF(SPlayer $player){
        $ui = new CustomForm(function (SPlayer $player, $data) {
            if ($data === null) {
                return;
            }
            if (Functions::isInGame($player)) {
                $player->sendMessage(Manager::PREFIX_ALERT . "Impossible de créer une partie de capture du drapeau car vous faites déjà partie d'une partie.");
                return;
            }
            if ($data[1] === true) {
                if ($player->hasPermission(Manager::PERM_CREATE_PRIVATE_PARTIES)) {
                    CaptureTheFlag::createCaptureTheFlag($player, true);
                    return;
                } else {
                    $player->sendMessage(Manager::NOT_PERM);
                    return;
                }
            }
            CaptureTheFlag::createCaptureTheFlag($player, false);
        });
        $ui->setTitle(Manager::FORM_TITLE);
        $ui->addLabel("§fInformation: §7Une partie privée est rejoignable uniquement via le code donner une fois la partie créer.");
        $ui->addToggle("§f- §7Voulez-vous que se soit une partie privée ?", false);
        $ui->sendToPlayer($player);
    }

    public static function sendCTFList(SPlayer $player){
        $ui = new SimpleForm(function (SPlayer $player, $data){
            if ($data === null){
                return;
            }
            if (empty(CaptureTheFlag::$ctf)){
                return;
            }else{
                $res = [];
                $i = 0;
                foreach (CaptureTheFlag::$ctf as $id){
                    $res[$i] = $id;
                    $i++;
                }
                FormListener::sendCTFInfos($player, $res[$data]["name"]);
            }
        });
        $ui->setTitle(Manager::FORM_TITLE);
        if (empty(CaptureTheFlag::$ctf)){
            $ui->setContent("§f- §7Aucun capture du drapeau en cours.");
            $ui->addButton("§c§l<- QUITTER");
        }else{
            foreach (CaptureTheFlag::$ctf as $id => $value){
                $ui->addButton($id . "\nStatus: " . CaptureTheFlag::$ctf[$id]["status"] . " §8| §e" . CaptureTheFlag::getPlayersCount($id) . " §8joueur(s)");
            }
        }
        $ui->sendToPlayer($player);
    }

    public static function sendCTFInfos(SPlayer $player, string $id){
        $ui = new SimpleForm(function (SPlayer $player, $data) use ($id){
            if ($data === null){
                return;
            }
            switch ($data){
                case 0:
                    if (!isset(CaptureTheFlag::$ctf[$id])){
                        $player->sendMessage(Manager::PREFIX_ALERT . "Cette partie n'existe plus.");
                        return;
                    }
                    if (Functions::isInGame($player)){
                        $player->sendMessage(Manager::PREFIX_ALERT . "Vous êtes déjà dans une partie.");
                        return;
                    }
                    if (CaptureTheFlag::$ctf[$id]["isPrivate"] === true){
                        $player->sendMessage(Manager::PREFIX_ALERT . "Impossible de rejoindre cette partie car elle est privée.");
                        return;
                    }
                    if (CaptureTheFlag::getPlayersCount($id) >= CaptureTheFlag::getMaxPlayersCount($id)){
                        $player->sendMessage(Manager::PREFIX_ALERT . "Cette partie est complete.");
                        return;
                    }
                    $player->sendMessage(Manager::PREFIX_INFOS . "Vous êtes en train de rejoindre la partie §e" . $id . "§f.");
                    CaptureTheFlag::addPlayer($id, $player);
                    break;
                case 1:
                    break;
            }
        });
        $ui->setTitle(Manager::FORM_TITLE);
        $ui->setContent(
            "§6» §eNom: §7" . $id . "\n" .
            "§6» §eStatus: §7" . CaptureTheFlag::$ctf[$id]["status"] . "\n" .
            "§6» §eJoueurs §f(" . CaptureTheFlag::getPlayersCount($id) . "/" . CaptureTheFlag::getMaxPlayersCount($id) . ")§e: §7" . CaptureTheFlag::getPlayersList($id)
        );
        $ui->addButton("Rejoindre");
        $ui->addButton("§c§l<- QUITTER");
        $ui->sendToPlayer($player);
    }

    public static function sendCTFQuit(SPlayer $player){
        $ui = new SimpleForm(function (SPlayer $player, $data){
            if ($data === null){
                return;
            }
            if (isset(CaptureTheFlag::$players[$player->getName()])){
                switch ($data){
                    case 0:
                        CaptureTheFlag::removePlayer(CaptureTheFlag::$players[$player->getName()], $player);
                        $player->sendMessage(Manager::PREFIX_INFOS . "Vous avez bien quitter la partie.");
                        break;
                    case 1:
                        break;
                }
            }
        });
        $ui->setTitle(Manager::FORM_TITLE);
        if (isset(CaptureTheFlag::$players[$player->getName()])){
            $ui->setContent("§f- §7Voulez-vous vraiment quitter ?");
            $ui->addButton("§aOui");
            $ui->addButton("§cNon");
        }else{
            $ui->setContent("§f- §7Vous n'êtes dans aucune partie.");
            $ui->addButton("§c§l<- QUITTER");
        }
        $ui->sendToPlayer($player);
    }

    public static function sendHikaMenu(SPlayer $player){
        $ui = new SimpleForm(function (SPlayer $player, $data){
            if ($data === null){
                return;
            }
            switch ($data){
                case 0:
                    FormListener::sendCreateHika($player);
                    break;
                case 1:
                    FormListener::sendHikaList($player);
                    break;
                case 2:
                    FormListener::sendHikaQuit($player);
                    break;
            }
        });
        $ui->setTitle(Manager::FORM_TITLE);
        $ui->addButton("Créer un hikabrain.");
        $ui->addButton("Voir les parties en cours.");
        $ui->addButton("Quitter la partie en cours.");
        $ui->sendToPlayer($player);
    }

    public static function sendCreateHika(SPlayer $player){
        $ui = new CustomForm(function (SPlayer $player, $data){
            if ($data === null){
                return;
            }
            if (Functions::isInGame($player)){
                $player->sendMessage(Manager::PREFIX_ALERT . "Impossible de créer une partie d'hikabrain car vous faites déjà partie d'une partie.");
                return;
            }
            if (!isset($data[1])){
                $player->sendMessage(Manager::PREFIX_ALERT . "Vous devez sélectionner un type.");
                return;
            }
            if (!isset($data[2])){
                $player->sendMessage(Manager::PREFIX_ALERT . "Vous devez sélectionner un type.");
                return;
            }
            if ($data[2] === true) {
                if ($player->hasPermission(Manager::PERM_CREATE_PRIVATE_PARTIES)) {
                    Main::$game["Hikabrain"][$player->getName()] = new HikabrainInstance($player, GameManager::$dropdown["Hikabrain"][$data[1]], true);
                    return;
                }else{
                    $player->sendMessage(Manager::NOT_PERM);
                    return;
                }
            }
            Main::$game["Hikabrain"][$player->getName()] = new HikabrainInstance($player, GameManager::$dropdown["Hikabrain"][$data[1]], false);
            return;
        });
        $ui->setTitle(Manager::FORM_TITLE);
        $ui->addLabel("§fInformation: §7Une partie privée est rejoignable uniquement via le code donner une fois la partie créer.");
        $ui->addDropdown("§f- §7Quel type d'hikabrain ?", GameManager::$dropdown["Hikabrain"]);
        $ui->addToggle("§f- §7Voulez-vous que se soit une partie privée ?", false);
        $ui->sendToPlayer($player);
    }

    public static function sendHikaList(SPlayer $player){
        $ui = new SimpleForm(function (SPlayer $player, $data){
            if ($data === null){
                return;
            }
            if (empty(Main::$game["Hikabrain"])){
                return;
            }else{
                $res = [];
                $i = 0;
                foreach (Main::$game["Hikabrain"] as $player => $value){
                    $res[$i] = $value;
                    $i++;
                }
                FormListener::sendHikaInfos($player, $res[$data]);
            }
        });
        $ui->setTitle(Manager::FORM_TITLE);
        if (empty(Main::$game["Hikabrain"])){
            $ui->setContent("§f- §7Aucun hikabrain en cours.");
            $ui->addButton("§c§l<- QUITTER");
        }else{
            foreach (Main::$game["Hikabrain"] as $player => $value){
                $ui->addButton(Main::$game["Hikabrain"][$player]->getId() . "\nStatus: " . Main::$game["Hikabrain"][$player]->getStatus() . " §8| §e" . Main::$game["Hikabrain"][$player]->getPlayersCount() . " §8joueur(s)");
            }
        }
        $ui->sendToPlayer($player);
    }

    public static function sendHikaInfos(SPlayer $player, HikabrainInstance $hikabrain){
        $ui = new SimpleForm(function (SPlayer $player, $data) use ($hikabrain){
            if ($data === null){
                return;
            }
            switch ($data){
                case 0:
                    /*
                    if (!isset(Hikabrain::$hikabrain[$id])){
                        $player->sendMessage(Manager::PREFIX_ALERT . "Cette partie n'existe plus.");
                        return;
                    }
                    */
                    if (Functions::isInGame($player)){
                        $player->sendMessage(Manager::PREFIX_ALERT . "Vous êtes déjà dans une partie.");
                        return;
                    }
                    if ($hikabrain->isPrivate() === true){
                        $player->sendMessage(Manager::PREFIX_ALERT . "Impossible de rejoindre cette partie car elle est privée.");
                        return;
                    }
                    if ($hikabrain->getPlayersCount() >= $hikabrain->getMaxPlayers()){
                        $player->sendMessage(Manager::PREFIX_ALERT . "Cette partie est complete.");
                        return;
                    }
                    $player->sendMessage(Manager::PREFIX_INFOS . "Vous êtes en train de rejoindre la partie §e" . $hikabrain->getId() . "§f.");
                    $hikabrain->addNewPlayer($player);
                    break;
                case 1:
                    break;
            }
        });
        $ui->setTitle(Manager::FORM_TITLE);
        $ui->setContent(
            "§6» §eNom: §7" . $hikabrain->getId() . "\n" .
            "§6» §eStatus: §7" . $hikabrain->getStatus() . "\n" .
            "§6» §eJoueurs §f(" . $hikabrain->getPlayersCount() . "/" . $hikabrain->getMaxPlayers() . ")§e: §7" . $hikabrain->getListOfPlayers()
        );
        $ui->addButton("Rejoindre");
        $ui->addButton("§c§l<- QUITTER");
        $ui->sendToPlayer($player);
    }

    public static function sendHikaQuit(SPlayer $player){
        $ui = new SimpleForm(function (SPlayer $player, $data){
            if ($data === null){
                return;
            }
            if (isset(Hikabrain::$players[$player->getName()])){
                switch ($data){
                    case 0:
                        Hikabrain::removePlayer(Hikabrain::$players[$player->getName()], $player);
                        $player->sendMessage(Manager::PREFIX_INFOS . "Vous avez bien quitter la partie.");
                        break;
                    case 1:
                        break;
                }
            }
        });
        $ui->setTitle(Manager::FORM_TITLE);
        if (isset(Hikabrain::$players[$player->getName()])){
            $ui->setContent("§f- §7Voulez-vous vraiment quitter ?");
            $ui->addButton("§aOui");
            $ui->addButton("§cNon");
        }else{
            $ui->setContent("§f- §7Vous n'êtes dans aucune partie.");
            $ui->addButton("§c§l<- QUITTER");
        }
        $ui->sendToPlayer($player);
    }

    public static function sendGunPlayMenu(SPlayer $player){
        $ui = new SimpleForm(function (SPlayer $player, $data){
            if ($data === null){
                return;
            }
            switch ($data){
                case 0:
                    FormListener::sendCreateGunPlay($player);
                    break;
                case 1:
                    FormListener::sendGunPlayList($player);
                    break;
                case 2:
                    FormListener::sendGunPlayQuit($player);
                    break;
            }
        });
        $ui->setTitle(Manager::FORM_TITLE);
        $ui->addButton("Créer un jeu d'arme.");
        $ui->addButton("Voir les jeux d'armes en cours.");
        $ui->addButton("Quitter la partie actuelle.");
        $ui->sendToPlayer($player);
    }

    public static function sendCreateGunPlay(SPlayer $player){
        $ui = new CustomForm(function (SPlayer $player, $data){
            if ($data === null){
                return;
            }
            if (Functions::isInGame($player)){
                $player->sendMessage(Manager::PREFIX_ALERT . "Impossible de créer une partie de jeu d'arme car vous faites déjà partie d'une partie.");
                return;
            }
            if (isset($data[1])) {
                if ($data[2] === true) {
                    if ($player->hasPermission(Manager::PERM_CREATE_PRIVATE_PARTIES)) {
                        Main::$game["GunPlay"][$player->getName()] = new GunPlay($player, GameManager::$dropdown["GunPlay"][$data[1]], true);
                        $player->setGame(Main::$game["GunPlay"][$player->getName()]);
                        return;
                    } else {
                        $player->sendMessage(Manager::NOT_PERM);
                        return;
                    }
                }
                Main::$game["GunPlay"][$player->getName()] = new GunPlay($player, GameManager::$dropdown["GunPlay"][$data[1]], false);
                $player->setGame(Main::$game["GunPlay"][$player->getName()]);
            }
        });
        $ui->setTitle(Manager::FORM_TITLE);
        $ui->addLabel("§fInformation: §7Une partie privée est rejoignable uniquement via le code donner une fois la partie créer.");
        $ui->addDropdown("§f- §7Combien de joueur voulez-vous ?", GameManager::$dropdown["GunPlay"]);
        $ui->addToggle("§f- §7Voulez-vous que se soit une partie privée ?", false);
        $ui->sendToPlayer($player);
    }

    public static function sendGunPlayList(SPlayer $player){
        $ui = new SimpleForm(function (SPlayer $player, $data) {
            if ($data === null) {
                return;
            }
            if (!empty(Main::$game["GunPlay"])) {
                $res = [];
                $i = 0;
                foreach (Main::$game["GunPlay"] as $instance) {
                    if ($instance instanceof GunPlay) {
                        $res[$i] = $instance;
                        $i++;
                    }
                }
                FormListener::sendGunPlayInfos($player, $res[$data]);
            }
        });
        $ui->setTitle(Manager::FORM_TITLE);
        if (empty(Main::$game["GunPlay"])){
            $ui->setContent("§f- §7Aucun jeu d'arme en cours.");
            $ui->addButton("§c§l<- QUITTER");
        }else{
            foreach (Main::$game["GunPlay"] as $instance){
                if ($instance instanceof GunPlay) {
                    $ui->addButton($instance->getId() . "\nStatus: " . $instance->getStatus() . " §8| §e" . $instance->getPlayersCount() . " §8joueur(s)");
                }
            }
        }
        $ui->sendToPlayer($player);
    }

    public static function sendGunPlayInfos(SPlayer $player, GunPlay $gunplay){
        $ui = new SimpleForm(function (SPlayer $player, $data) use ($gunplay){
            if ($data === null){
                return;
            }
            switch ($data){
                case 0:
                    if (!GameManager::partyExist($gunplay->getId())){
                        $player->sendMessage(Manager::PREFIX_ALERT . "Cette partie n'existe plus.");
                        return;
                    }
                    if (isset(Main::$players[$player->getName()])){
                        $player->sendMessage(Manager::PREFIX_ALERT . "Vous êtes déjà dans une partie.");
                        return;
                    }
                    if ($gunplay->isPrivate()){
                        $player->sendMessage(Manager::PREFIX_ALERT . "Impossible de rejoindre cette partie car elle est privée.");
                        return;
                    }
                    if ($gunplay->getPlayersCount() >= $gunplay->getMaxPlayers()){
                        $player->sendMessage(Manager::PREFIX_ALERT . "Cette partie est complete.");
                        return;
                    }
                    $player->sendMessage(Manager::PREFIX_INFOS . "Vous êtes en train de rejoindre la partie ayant pour id §e" . $gunplay->getId() . "§f.");
                    $gunplay->addNewPlayer($player);
                    break;
                case 1:
                    break;
            }
        });
        $ui->setTitle(Manager::FORM_TITLE);
        $ui->setContent(
            "§6» §eNom: §7" . $gunplay->getId() . "\n" .
            "§6» §eStatus: §7" . $gunplay->getStatus() . "\n" .
            "§6» §eJoueurs §f(" . $gunplay->getPlayersCount() . "/" . $gunplay->getMaxPlayers() . ")§e: §7" . $gunplay->getListOfPlayers() . "\n" .
            "§6» §eJoueur avec le plus de point: §7" . $gunplay->getTopOne()
        );
        $ui->addButton("Rejoindre");
        $ui->addButton("§c§l<- QUITTER");
        $ui->sendToPlayer($player);
    }

    public static function sendGunPlayQuit(SPlayer $player){
        $ui = new SimpleForm(function (SPlayer $player, $data){
            if ($data === null){
                return;
            }
            if (isset(GunPlay::$players[$player->getName()])){
                switch ($data){
                    case 0:
                        GunPlay::removePlayer(GunPlay::$players[$player->getName()], $player);
                        $player->sendMessage(Manager::PREFIX_INFOS . "Vous avez bien quitter la partie.");
                        break;
                    case 1:
                        break;
                }
            }
        });
        $ui->setTitle(Manager::FORM_TITLE);
        if (isset(GunPlay::$players[$player->getName()])){
            $ui->setContent("§f- §7Voulez-vous vraiment quitter ?");
            $ui->addButton("§aOui");
            $ui->addButton("§cNon");
        }else{
            $ui->setContent("§f- §7Vous n'êtes dans aucune partie.");
            $ui->addButton("§c§l<- QUITTER");
        }
        $ui->sendToPlayer($player);
    }

    public static function sendSelectTeam(SPlayer $player){
        $ui = new SimpleForm(function (SPlayer $player, $data){
            if ($data === null){
                return;
            }
            switch ($data){
                case 0:
                    if (isset(Hikabrain::$players[$player->getName()])) {
                        if (TeamManager::isInTeam(Hikabrain::$players[$player->getName()], $player)) {
                            TeamManager::removePlayerInTeam(Hikabrain::$players[$player->getName()], $player, TeamManager::getTeamOfPlayer(Hikabrain::$players[$player->getName()], $player));
                            NavigatorManager::sendWool($player);
                            $player->sendMessage(Manager::PREFIX_INFOS . "Vous êtes désormais en team aléatoire.");
                            return;
                        } else {
                            $player->sendMessage(Manager::PREFIX_ALERT . "Vous êtes déjà en team aléatoire.");
                            return;
                        }
                    }
                    if (isset(CaptureTheFlag::$players[$player->getName()])) {
                        if (TeamManager::isInTeam(CaptureTheFlag::$players[$player->getName()], $player)) {
                            TeamManager::removePlayerInTeam(CaptureTheFlag::$players[$player->getName()], $player, TeamManager::getTeamOfPlayer(CaptureTheFlag::$players[$player->getName()], $player));
                            NavigatorManager::sendWool($player);
                            $player->sendMessage(Manager::PREFIX_INFOS . "Vous êtes désormais en team aléatoire.");
                            return;
                        } else {
                            $player->sendMessage(Manager::PREFIX_ALERT . "Vous êtes déjà en team aléatoire.");
                            return;
                        }
                    }
                    break;
                case 1:
                    if (isset(Hikabrain::$players[$player->getName()])) {
                        if (TeamManager::isInTeam(Hikabrain::$players[$player->getName()], $player)) {
                            if (TeamManager::getPlayerCount(Hikabrain::$players[$player->getName()], "Rouge") < TeamManager::getMaxPlayerCount(Hikabrain::$players[$player->getName()], "Rouge")) {
                                TeamManager::removePlayerInTeam(Hikabrain::$players[$player->getName()], $player, TeamManager::getTeamOfPlayer(Hikabrain::$players[$player->getName()], $player));
                                TeamManager::setTeamForPlayer(Hikabrain::$players[$player->getName()], $player, "Rouge");
                                NavigatorManager::sendWoolRed($player);
                                $player->sendMessage(Manager::PREFIX_INFOS . "Vous êtes désormais en team rouge.");
                                return;
                            } else {
                                $player->sendMessage(Manager::PREFIX_ALERT . "Cette team est au complet.");
                                return;
                            }
                        } else {
                            if (TeamManager::getPlayerCount(Hikabrain::$players[$player->getName()], "Rouge") < TeamManager::getMaxPlayerCount(Hikabrain::$players[$player->getName()], "Rouge")) {
                                TeamManager::setTeamForPlayer(Hikabrain::$players[$player->getName()], $player, "Rouge");
                                NavigatorManager::sendWoolRed($player);
                                $player->sendMessage(Manager::PREFIX_INFOS . "Vous êtes désormais en team rouge.");
                            }else{
                                $player->sendMessage(Manager::PREFIX_ALERT . "Cette team est au complet.");
                                return;
                            }
                        }
                    }
                    if (isset(CaptureTheFlag::$players[$player->getName()])) {
                        if (TeamManager::isInTeam(CaptureTheFlag::$players[$player->getName()], $player)) {
                            if (TeamManager::getPlayerCount(CaptureTheFlag::$players[$player->getName()], "Rouge") < TeamManager::getMaxPlayerCount(CaptureTheFlag::$players[$player->getName()], "Rouge")) {
                                TeamManager::removePlayerInTeam(CaptureTheFlag::$players[$player->getName()], $player, TeamManager::getTeamOfPlayer(CaptureTheFlag::$players[$player->getName()], $player));
                                TeamManager::setTeamForPlayer(CaptureTheFlag::$players[$player->getName()], $player, "Rouge");
                                NavigatorManager::sendWoolRed($player);
                                $player->sendMessage(Manager::PREFIX_INFOS . "Vous êtes désormais en team rouge.");
                                return;
                            } else {
                                $player->sendMessage(Manager::PREFIX_ALERT . "Cette team est au complet.");
                                return;
                            }
                        } else {
                            if (TeamManager::getPlayerCount(CaptureTheFlag::$players[$player->getName()], "Rouge") < TeamManager::getMaxPlayerCount(CaptureTheFlag::$players[$player->getName()], "Rouge")) {
                                TeamManager::setTeamForPlayer(CaptureTheFlag::$players[$player->getName()], $player, "Rouge");
                                NavigatorManager::sendWoolRed($player);
                                $player->sendMessage(Manager::PREFIX_INFOS . "Vous êtes désormais en team rouge.");
                            }else{
                                $player->sendMessage(Manager::PREFIX_ALERT . "Cette team est au complet.");
                                return;
                            }
                        }
                    }
                    break;
                case 2:
                    if (isset(Hikabrain::$players[$player->getName()])) {
                        if (TeamManager::isInTeam(Hikabrain::$players[$player->getName()], $player)) {
                            if (TeamManager::getPlayerCount(Hikabrain::$players[$player->getName()], "Bleu") < TeamManager::getMaxPlayerCount(Hikabrain::$players[$player->getName()], "Bleu")) {
                                TeamManager::removePlayerInTeam(Hikabrain::$players[$player->getName()], $player, TeamManager::getTeamOfPlayer(Hikabrain::$players[$player->getName()], $player));
                                TeamManager::setTeamForPlayer(Hikabrain::$players[$player->getName()], $player, "Bleu");
                                NavigatorManager::sendWoolBlue($player);
                                $player->sendMessage(Manager::PREFIX_INFOS . "Vous êtes désormais en team bleu.");
                                return;
                            } else {
                                $player->sendMessage(Manager::PREFIX_ALERT . "Cette team est au complet.");
                                return;
                            }
                        } else {
                            if (TeamManager::getPlayerCount(Hikabrain::$players[$player->getName()], "Bleu") < TeamManager::getMaxPlayerCount(Hikabrain::$players[$player->getName()], "Bleu")) {
                                TeamManager::setTeamForPlayer(Hikabrain::$players[$player->getName()], $player, "Bleu");
                                NavigatorManager::sendWoolBlue($player);
                                $player->sendMessage(Manager::PREFIX_INFOS . "Vous êtes désormais en team bleu.");
                            }else{
                                $player->sendMessage(Manager::PREFIX_ALERT . "Cette team est au complet.");
                                return;
                            }
                        }
                    }
                    if (isset(CaptureTheFlag::$players[$player->getName()])) {
                        if (TeamManager::isInTeam(CaptureTheFlag::$players[$player->getName()], $player)) {
                            if (TeamManager::getPlayerCount(CaptureTheFlag::$players[$player->getName()], "Bleu") < TeamManager::getMaxPlayerCount(CaptureTheFlag::$players[$player->getName()], "Bleu")) {
                                TeamManager::removePlayerInTeam(CaptureTheFlag::$players[$player->getName()], $player, TeamManager::getTeamOfPlayer(CaptureTheFlag::$players[$player->getName()], $player));
                                TeamManager::setTeamForPlayer(CaptureTheFlag::$players[$player->getName()], $player, "Bleu");
                                NavigatorManager::sendWoolBlue($player);
                                $player->sendMessage(Manager::PREFIX_INFOS . "Vous êtes désormais en team bleu.");
                                return;
                            } else {
                                $player->sendMessage(Manager::PREFIX_ALERT . "Cette team est au complet.");
                                return;
                            }
                        } else {
                            if (TeamManager::getPlayerCount(CaptureTheFlag::$players[$player->getName()], "Bleu") < TeamManager::getMaxPlayerCount(CaptureTheFlag::$players[$player->getName()], "Bleu")) {
                                TeamManager::setTeamForPlayer(CaptureTheFlag::$players[$player->getName()], $player, "Bleu");
                                NavigatorManager::sendWoolBlue($player);
                                $player->sendMessage(Manager::PREFIX_INFOS . "Vous êtes désormais en team bleu.");
                            }else{
                                $player->sendMessage(Manager::PREFIX_ALERT . "Cette team est au complet.");
                                return;
                            }
                        }
                    }
                    break;
            }
        });
        $ui->setTitle(Manager::FORM_TITLE);
        $ui->setContent("§f- §7Dans qu'elle team voudriez-vous aller ?");
        if (isset(Hikabrain::$players[$player->getName()])) {
            if (TeamManager::getMaxTeamCount(Hikabrain::$players[$player->getName()]) === 2) {
                $ui->addButton("§l§fALEATOIRE");
                $ui->addButton("§l§cROUGE");
                $ui->addButton("§l§1BLEU");
            }
        }
        if (isset(CaptureTheFlag::$players[$player->getName()])) {
            if (TeamManager::getMaxTeamCount(CaptureTheFlag::$players[$player->getName()]) === 2) {
                $ui->addButton("§l§fALEATOIRE");
                $ui->addButton("§l§cROUGE");
                $ui->addButton("§l§1BLEU");
            }
        }
        $ui->sendToPlayer($player);
    }

    public static function sendEnterCodeMenu(SPlayer $player){
        $ui = new CustomForm(function (SPlayer $player, $data) {
            if ($data === null) {
                return;
            }
            $code = $data[0];
            if (!$code) {
                $player->sendMessage(Manager::PREFIX_ALERT . "Vous devez entrer un code.");
                return;
            }
            if (Functions::isInGame($player)) {
                $player->sendMessage(Manager::PREFIX_ALERT . "Impossible de rejoindre cette partie car vous êtes déjà dans une partie.");
                return;
            }
            if (!PrivateGameManager::getGameByCode($code)) {
                $player->sendMessage(Manager::PREFIX_ALERT . "Aucune partie trouvée avec ce code.");
                return;
            }
            if (PrivateGameManager::getGameByCode($code)["type"] === "GunPlay") {
                if (GunPlay::getPlayersCount(PrivateGameManager::getGameByCode($code)["game"]) >= GunPlay::getPlayersMaxCount()) {
                    $player->sendMessage(Manager::PREFIX_ALERT . "Impossible de rejoindre cette partie car elle est complete.");
                    return;
                }
                $player->sendMessage(Manager::PREFIX_INFOS . "Partie trouvée, vous êtes en train de la rejoindre.");
                GunPlay::addPlayer(PrivateGameManager::getGameByCode($code)["game"], $player);
                return;
            }
            if (PrivateGameManager::getGameByCode($code)["type"] === "Hikabrain") {
                if (Hikabrain::getPlayersCount(PrivateGameManager::getGameByCode($code)["game"]) >= Hikabrain::getPlayersMaxCount(PrivateGameManager::getGameByCode($code)["game"])) {
                    $player->sendMessage(Manager::PREFIX_ALERT . "Impossible de rejoindre cette partie car elle est complete.");
                    return;
                }
                $player->sendMessage(Manager::PREFIX_INFOS . "Partie trouvée, vous êtes en train de la rejoindre.");
                Hikabrain::addPlayer(PrivateGameManager::getGameByCode($code)["game"], $player);
                return;
            }
        });
        $ui->setTitle(Manager::FORM_TITLE);
        $ui->addInput("§f- §7Entrer le code ci-dessous:", "Votre code.");
        $ui->sendToPlayer($player);
    }

}