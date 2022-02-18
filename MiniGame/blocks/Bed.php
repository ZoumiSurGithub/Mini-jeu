<?php

namespace Zoumi\MiniGame\blocks;

use pocketmine\block\Bed as PMBed;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\item\Item;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\tile\Tile;
use pocketmine\tile\Bed as TileBed;

class Bed extends PMBed{

    protected $id = self::BED_BLOCK;

    protected $itemId = Item::BED;

    public function __construct(int $meta = 0){
        $this->meta = $meta;
    }

    public function getHardness() : float{
        return 0.2;
    }

    public function getName() : string{
        return "Bed Block";
    }

    protected function recalculateBoundingBox() : ?AxisAlignedBB{
        return new AxisAlignedBB(
            $this->x,
            $this->y,
            $this->z,
            $this->x + 1,
            $this->y + 0.5625,
            $this->z + 1
        );
    }

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
        $down = $this->getSide(Vector3::SIDE_DOWN);
        if(!$down->isTransparent()){
            $meta = (($player instanceof Player ? $player->getDirection() : 0) - 1) & 0x03;
            $next = $this->getSide(self::getOtherHalfSide($meta));
            if($next->canBeReplaced() and !$next->getSide(Vector3::SIDE_DOWN)->isTransparent()){
                $this->getLevelNonNull()->setBlock($blockReplace, BlockFactory::get($this->id, $meta), true, true);
                $this->getLevelNonNull()->setBlock($next, BlockFactory::get($this->id, $meta | self::BITFLAG_HEAD), true, true);

                Tile::createTile(Tile::BED, $this->getLevelNonNull(), TileBed::createNBT($this, $face, $item, $player));
                Tile::createTile(Tile::BED, $this->getLevelNonNull(), TileBed::createNBT($next, $face, $item, $player));

                return true;
            }
        }

        return false;
    }

    public function onActivate(Item $item, Player $player = null): bool
    {
        return true;
    }

}