<?php

namespace IceCruelStuff\ExplosiveSnowballs\Event;

use pocketmine\entity\projectile\Snowball as SnowballEntity;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\item\Snowball;

class SnowballLaunchEvent extends ProjectileLaunchEvent {

    /** @var SnowballEntity */
    private $entity;
    /** @var Snowball */
    private $snowball;

    public function __construct(SnowballEntity $entity, Snowball $snowball) {
        $this->entity = $entity;
        $this->snowball = $snowball;
    }

    /**
     * @return SnowballEntity
     */
    public function getEntity() {
        return $this->entity;
    }

    /**
     * @return Snowball
     */
    public function getSnowball() {
        return $this->snowball;
    }

}
