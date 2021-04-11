<?php

namespace IceCruelStuff\ExplosiveSnowballs\Event;

use pocketmine\entity\projectile\SnowballEntity;
use pocketmine\event\ProjectileHitEvent;
use pocketmine\item\Snowball;
use pocketmine\math\RayTraceResult;

class SnowballHitEvent extends ProjectileHitEvent {

    private $snowball;
    private $rayTraceResult;

    public function __construct(SnowballEntity $entity, RayTraceResult $rayTraceResult, Snowball $snowball) {
        $this->entity = $entity;
        $this->rayTraceResult = $rayTraceResult;
        $this->snowball = $snowball;
    }

    public function getEntity() {
        return $this->entity;
    }

    public function getRayTraceResult() : RayTraceResult {
        return $this->rayTraceResult;
    }

    public function getSnowball() {
        return $this->snowball;
    }

}
