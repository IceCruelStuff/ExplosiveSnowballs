<?php

namespace IceCruelStuff\ExplosiveSnowballs\Event;

use pocketmine\entity\projectile\Snowball as SnowballEntity;
use pocketmine\entity\Entity;
use pocketmine\item\Snowball;
use pocketmine\math\RayTraceResult;
use IceCruelStuff\ExplosiveSnowballs\Event\SnowballHitEvent;

class SnowballHitEntityEvent extends SnowballHitEvent {

    private $entityHit;

    public function __construct(SnowballEntity $entity, RayTraceResult $rayTraceResult, Snowball $snowball, Entity $entityHit) {
        parent::__construct($entity, $rayTraceResult, $snowball);
        $this->entityHit = $entityHit;
    }

    public function getEntityHit() : Entity {
        return $this->entityHit;
    }

}
