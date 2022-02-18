<?php

namespace Zoumi\MiniGame;

use pocketmine\Server;

interface Manager {

    public const POS_GUNPLAY = array(
        0 => "-478.5:32:-347.5",
        1 => "-423.5:32:-402.5",
        2 => "-478.5:32:-457.5",
        3 => "-533.5:32:-402.5",
        4 => "-506.5:36:-376.5",
        5 => "-441.5:32:-426.5",
        6 => "-447.5:36:-377.5",
        7 => "-500.5:35:-436.5",
        8 => "-465.5:32:-404.5",
        9 => "-501.5:32:-413.5",
        10 => "-469.5:32:-382.5",
        11 => "-469.5:32:-433.5"
    );

    public const POS_HIKABRAIN = array(
        "Rouge" => "-501.5:10:-351.5;-501.5:10:-352.5;-501.5:10:-350.5;-500.5:10:-351.5",
        "Bleu" => "-546.5:10:-351.5;-546.5:10:-350.5;-546.5:10:-352.5;-547.5:10:-351.5"
    );

    public const PREFIX_INFOS = "§f(§eSun§fInfos§f) ";

    public const PREFIX_ALERT = "§f(§eSun§cAlert§f) §c";

    public const PREFIX = "§f(§eSun§fParadise§f) ";

    public const SANCTION = "§f(§cSanction§f) ";

    public const FORM_TITLE = "§l§eSun§fParadise";

    public const ANONCEUR = "§f(§bAnnonceur§f) ";

    public const NOT_PERM = "§f(§eSun§cAlert§f) §cVous n'avez pas la permission d'utiliser ceci.";

    public const NOT_DISPONIBLE = "§f(§eSun§cAlert§f) §cPas encore disponible.";

    public const ALREADY_IN_GAME = self::PREFIX_ALERT . "Vous êtes actuellement dans une partie. Pour quitter celle-ci faites §7/menu§c, sélectionner le mode de jeu dans lequel vous êtes et faites §7Quitter la partie§c.";

    public const CREATE_PARTIES = self::ANONCEUR . "Création de la partie en cours... Téléportation vers la salle d'attente.";

    public const VIEW_CODE = self::PREFIX_INFOS . "Pour voir le code de votre partie privée faites §7/code§f.";

    public const PLAYER = 0;

    public const STAFF = 1;

    public const PERM_CREATE_PRIVATE_PARTIES = "create.private.parties";

}