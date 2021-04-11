<?php

namespace IceCruelStuff\ExplosiveSnowballs\Item;

use pocketmine\entity\EntityIds;
use pocketmine\entity\Entity;
use pocketmine\item\Snowball;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;
use IceCruelStuff\ExplosiveSnowballs\Event\SnowballLaunchEvent;

class ExplosiveSnowball extends Snowball {

    public function onClickAir(Player $player, Vector3 $directionVector) : bool {
        $nbt = Entity::createBaseNBT($player->add(0, $player->getEyeHeight(), 0), $directionVector, $player->yaw, $player->pitch);
        $this->addExtraTags($nbt);
        $projectile = Entity::createEntity($this->getProjectileEntityType(), $player->getLevelNonNull(), $nbt, $player);
        if ($projectile !== null) {
            $projectile->setMotion($projectile->getMotion()->multiply($this->getThrowForce()));
        }

        $item = clone $this;
        $this->pop();

        if ($projectile instanceof Projectile) {
            $projectileEv = new SnowballLaunchEvent($projectile, $item);
            $projectileEv->call();
            if ($projectileEv->isCancelled()) {
                $projectile->flagForDespawn();
            } else {
                $projectile->spawnToAll();

                $player->getLevelNonNull()->broadcastLevelSoundEvent($player, LevelSoundEventPacket::SOUND_THROW, 0, EntityIds::PLAYER);
            }
        } elseif ($projectile !== null) {
            $projectile->spawnToAll();
        } else {
            return false;
        }
        return true;
    }

}
