<?php

namespace Zoumi\MiniGame;

use pocketmine\block\BlockFactory;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\item\ItemBlock;
use pocketmine\item\ItemFactory;
use pocketmine\nbt\tag\ByteArrayTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\NamedTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use Zoumi\MiniGame\api\game\GameManager;
use Zoumi\MiniGame\api\WorldManager;
use Zoumi\MiniGame\blocks\Bed;
use Zoumi\MiniGame\commands\all\Code;
use Zoumi\MiniGame\commands\all\Leave;
use Zoumi\MiniGame\commands\all\Link;
use Zoumi\MiniGame\commands\all\Menu;
use Zoumi\MiniGame\commands\all\Navigator;
use Zoumi\MiniGame\commands\all\remake\Liste;
use Zoumi\MiniGame\commands\all\Spawn;
use Zoumi\MiniGame\commands\all\Vote;
use Zoumi\MiniGame\commands\staff\gamemode\GMA;
use Zoumi\MiniGame\commands\staff\gamemode\GMC;
use Zoumi\MiniGame\commands\staff\gamemode\GMP;
use Zoumi\MiniGame\commands\staff\gamemode\GMS;
use Zoumi\MiniGame\commands\staff\gamemode\GMSP;
use Zoumi\MiniGame\commands\staff\gamemode\GMSTAFF;
use Zoumi\MiniGame\commands\staff\PlaySound;
use Zoumi\MiniGame\commands\staff\XYZ;
use Zoumi\MiniGame\entities\ctf\FlagBlue;
use Zoumi\MiniGame\entities\ctf\FlagRed;
use Zoumi\MiniGame\entities\FireworksRocket;
use Zoumi\MiniGame\entities\generator\Diamond;
use Zoumi\MiniGame\entities\generator\Emerald;
use Zoumi\MiniGame\entities\generator\Gold;
use Zoumi\MiniGame\entities\generator\Iron;
use Zoumi\MiniGame\entities\top\Death;
use Zoumi\MiniGame\entities\top\Kd;
use Zoumi\MiniGame\entities\top\Kill;
use Zoumi\MiniGame\entities\top\Money;
use Zoumi\MiniGame\entities\VillagerBW;
use Zoumi\MiniGame\entities\VillagerTest;
use Zoumi\MiniGame\items\Fireworks;
use Zoumi\MiniGame\listeners\events\BedwarsEvent;
use Zoumi\MiniGame\listeners\events\CaptureTheFlagEvent;
use Zoumi\MiniGame\listeners\events\GunPlayEvent;
use Zoumi\MiniGame\listeners\events\HikabrainEvent;
use Zoumi\MiniGame\listeners\events\WaitingRoomEvent;
use Zoumi\MiniGame\listeners\events\WorldEvent;
use Zoumi\MiniGame\listeners\PlayerListener;
use Zoumi\MiniGame\tasks\CombatLogger;
use Zoumi\MiniGame\tasks\StatsServer;
use Zoumi\MiniGame\tasks\XYZTask;
use Zoumi\MiniGame\utils\Functions;

class Main extends PluginBase {

    /** @var $instance */
    public static $instance;
    /** @var array $scoreboard */
    public static $scoreboard = [];
    /** @var array $game */
    public static $game = ["GunPlay" => [], "Hikabrain" => [], "CaptureTheFlag" => [], "Bedwars" => []];
    /** @var array $players */
    public static $players = [];

    /** @var array $xyz */
    public $xyz = [];
    /** @var array $playerCache */
    public $playerCache = [];
    /** @var int $entityId */
    public $entityId;
    /** @var int $entityCount */
    public $entityCount = 0;

    /** @var $cosmetique */
    public $cosmetique;

    /** @var int $waitingRoomCount */
    public static $waitingRoomCount = 0;

    public static function getInstance(): self{
        return self::$instance;
    }

    public function onEnable()
    {
        self::$instance = $this;
        self::setConfig();

        try {
            DataBase::setupTable();
            $this->getLogger()->warning("Connection à MySQL réussite.");
        }catch (\mysqli_sql_exception $exception){
            $this->getLogger()->warning("Connection à MySQL échoué.");
        }

        /** Entity */
        Entity::registerEntity(Death::class, true);
        Entity::registerEntity(Kill::class, true);
        Entity::registerEntity(Kd::class, true);
        Entity::registerEntity(Money::class, true);
        Entity::registerEntity(Diamond::class, true);
        Entity::registerEntity(Emerald::class, true);
        Entity::registerEntity(Iron::class, true);
        Entity::registerEntity(VillagerBW::class, true);
        Entity::registerEntity(FlagRed::class, true);
        Entity::registerEntity(FlagBlue::class, true);
        Entity::registerEntity(FireworksRocket::class, true);
        Entity::registerEntity(VillagerTest::class, true);

        /* Items */
        ItemFactory::registerItem(new Fireworks(), true);
        Item::initCreativeItems();

        /* Monde */
        $this->getServer()->getDefaultLevel()->setTime(9000);
        $this->getServer()->getDefaultLevel()->stopTime();
        WorldManager::createWorld("spawn", false, false, false, false, false, false, false);

        /* Commands */
        $this->getServer()->getCommandMap()->unregister($this->getServer()->getCommandMap()->getCommand("list"));
        $this->getServer()->getCommandMap()->unregister($this->getServer()->getCommandMap()->getCommand("kill"));
        $this->getServer()->getCommandMap()->registerAll("MiniGame", [
            /** Staff */
            new XYZ("xyz", "Permet de voir vos coordonnées.", "/xyz", ["coords", "coord"]),
            new GMP("gmp", "Permet de se mettre en tant que joueur.", "/gmp", []),
            new GMSTAFF("gmstaff", "Permet de se mettre en tant que staff.", "/gmstaff", []),
            new GMS("gms", "Permet de se mettre en survie.", "/gms", ["gm0"]),
            new GMC("gmc", "Permet de se mettre en créatif.", "/gmc", ["gm1"]),
            new GMA("gma", "Permet de se mettre en aventure.", "/gma", ["gm2"]),
            new GMSP("gmsp", "Permet de se mettre en spectateur.", "/gmsp", ["gm3"]),
            new \Zoumi\MiniGame\commands\staff\Entity("entity", "Permet de faire spawn les leaderboards.", "/entity", []),
            new PlaySound("playsound", "Permet de jouer un son.", "/playsound", ["ps", "play"]),

            /** All */
            new Menu("menu", "Permet d'afficher le menu des minis jeux.", "/menu", []),
            new Liste("list", "Permet de voir la liste des joueurs en ligne.", "/list", ["liste", "players"]),
            new Link("link", "Permet d'obtenir le code pour lié votre compte à discord.", "/link", []),
            new Vote("vote", "Permet d'obtenir votre récompense de vote.", "/vote", []),
            new Spawn("spawn", "Permet de se téléporter au spawn.", "/spawn", ["hub"]),
            new Navigator("navigator", "Permet de redonner les items de navigations.", "/navigator", []),
            new Leave("leave", "Permet de quitter sa partie actuel.", "/leave", ["quit"]),

            /* Ranked */
            new Code("code", "Permet de voir le code de votre partie privée.", "/code", []),
        ]);

        /* Listeners */
        $this->getServer()->getPluginManager()->registerEvents(new PlayerListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new WaitingRoomEvent(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new GunPlayEvent(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new HikabrainEvent(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new CaptureTheFlagEvent(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new BedwarsEvent(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new WorldEvent(), $this);

        /* Task */
        $this->getScheduler()->scheduleRepeatingTask(new XYZTask(), 20);
        $this->getScheduler()->scheduleRepeatingTask(new CombatLogger(), 20);

        /* Blocks */
        BlockFactory::registerBlock(new Bed(), true);
        ItemBlock::initCreativeItems();
    }

    public function onDisable()
    {
        GameManager::closeAllGame();
        foreach ($this->getServer()->getDefaultLevel()->getEntities() as $entity){
            if ($entity instanceof Death or $entity instanceof Kill or $entity instanceof Kd or $entity instanceof Money){
                $entity->close();
            }
        }
        foreach (Functions::$villagerTest as $player => $value){
            $this->getServer()->getDefaultLevel()->getEntity(Functions::$villagerTest[$player]["entityId"])->close();
        }
    }

    public function PNGtoBYTES($path) : string
    {
        $img = @imagecreatefrompng($path);
        $bytes = "";
        $L = (int) @getimagesize($path)[0];
        $l = (int) @getimagesize($path)[1];
        for ($y = 0; $y < $l; $y++) {
            for ($x = 0; $x < $L; $x++) {
                $rgba = @imagecolorat($img, $x, $y);
                $a = ((~((int)($rgba >> 24))) << 1) & 0xff;
                $r = ($rgba >> 16) & 0xff;
                $g = ($rgba >> 8) & 0xff;
                $b = $rgba & 0xff;
                $bytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($img);
        return $bytes;
    }

    public function getSkinTag() : NamedTag
    {
        $skin = str_repeat("\x00", 8192);
        return new CompoundTag("Skin", [
            new StringTag("Name", "Standard_Custom"),
            new ByteArrayTag("Data", $skin),
        ]);
    }

    public function convert($time): string
    {
        if($time >= 60){
            $m = $time / 60;
            $mins = floor($m);
            $s = $m - $mins;
            $secs = floor($s * 60);
            if($mins >= 60){
                $h = $mins / 60;
                $hrs = floor($h);
                $m = $h - $hrs;
                $mins = floor($m * 60);
                return $hrs . " §fheure(s), §3" . $mins . " §fminute(s) et §3$secs §fseconce(s)";
            } else {
                return $mins . " §fminute(s) et §3$secs §fseconde(s)";
            }
        } else {
            return $time . " §fseconde(s)";
        }
    }

    public function convertToInt($time): string
    {
        if($time >= 60){
            $m = $time / 60;
            $mins = floor($m);
            $s = $m - $mins;
            $secs = floor($s * 60);
            if($mins >= 60){
                $h = $mins / 60;
                $hrs = floor($h);
                $m = $h - $hrs;
                $mins = floor($m * 60);
                return $hrs . "h";
            } else {
                return $mins . "m";
            }
        } else {
            return $time . "s";
        }
    }

    public function setConfig(): void{
        /* Basique */
        $this->cosmetique = new Config($this->getDataFolder() . "cosmetique.json", Config::JSON);
    }

    public function convertGame($time){
        if($time >= 60){
            $m = $time / 60;
            $mins = floor($m);
            $s = $m - $mins;
            $secs = floor($s * 60);
            if($mins >= 60){
                $h = $mins / 60;
                $hrs = floor($h);
                $m = $h - $hrs;
                $mins = floor($m * 60);
                return $h . "h:" . $mins . "m:" . $secs . "s";
            } else {
                return $mins . "m:" . $secs . "s";
            }
        } else {
            return $time . "s";
        }
    }

}