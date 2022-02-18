<?php

namespace Zoumi\MiniGame\api\game;

use pocketmine\Player;
use Zoumi\MiniGame\utils\Functions;

interface PrivateGameManager {

    public function isPrivate(): bool;

    public function setPrivate(bool $value);

    public function getCode(): string;

    public function genCode(): string;

    public function setCode(string $code);

}