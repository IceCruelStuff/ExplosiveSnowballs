<?php

namespace IceCruelStuff\ExplosiveSnowballs\Event;

use pocketmine\entity\projectile\Snowball as SnowballEntity;
use pocketmine\block\Block;
use pocketmine\item\Snowball;
use pocketmine\math\RayTraceResult;
use IceCruelStuff\ExplosiveSnowballs\Event\SnowballHitEvent;

class SnowballHitBlockEvent extends SnowballHitEvent {

    private $blockHit;

    public function __construct(SnowballEntity $entity, RayTraceResult $rayTraceResult, Snowball $snowball, Block $blockHit) {
        parent::__construct($entity, $rayTraceResult, $snowball);
        $this->blockHit = $blockHit;
    }

    public function getBlockHit() : Block {
        return $this->blockHit;
    }

}

