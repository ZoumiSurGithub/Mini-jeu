<?php

namespace Zoumi\MiniGame\api\game;

use pocketmine\Player;

class SPlayer extends Player{

    private $game;

    public function getGame(){
        return $this->game;
    }

    public function setGame($object){
        $this->game = $object;
    }

    public function isInGame(): bool{
        if (!empty($this->game)){
            return true;
        }
        return false;
    }

}