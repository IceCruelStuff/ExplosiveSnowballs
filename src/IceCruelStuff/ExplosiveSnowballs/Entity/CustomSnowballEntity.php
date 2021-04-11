<?php

namespace IceCruelStuff\ExplosiveSnowballs\Entity;

use pocketmine\entity\projectile\Snowball;
use pocketmine\entity\Entity;
use pocketmine\item\Snowball as SnowballItem;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;

class CustomSnowballEntity extends Snowball {

    public $snowball;

    public function __construct(Level $level, CompoundTag $nbt, SnowballItem $snowball = null, ?Entity $shootingEntity = null) {
        $this->snowball = $snowball;
        parent::__construct($level, $nbt);
        if ($shootingEntity !== null) {
            $this->setOwningEntity($shootingEntity);
        }
    }

}
