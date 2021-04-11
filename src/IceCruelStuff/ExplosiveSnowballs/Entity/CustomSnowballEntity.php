<?php

namespace IceCruelStuff\ExplosiveSnowballs\Entity;

use pocketmine\entity\projectile\Snowball;
use pocketmine\entity\Entity;
use pocketmine\item\Snowball as SnowballItem;
use pocketmine\level\Level;
use pocketmine\math\VoxelRayTrace;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\timings\Timings;
use IceCruelStuff\ExplosiveSnowballs\Event\SnowballHitBlockEvent;
use IceCruelStuff\ExplosiveSnowballs\Event\SnowballHitEntityEvent;
use IceCruelStuff\ExplosiveSnowballs\Event\SnowballHitEvent;

class CustomSnowballEntity extends Snowball {

    public $snowball;

    public function __construct(Level $level, CompoundTag $nbt, SnowballItem $snowball = null, ?Entity $shootingEntity = null) {
        $this->snowball = $snowball;
        parent::__construct($level, $nbt);
        if ($shootingEntity !== null) {
            $this->setOwningEntity($shootingEntity);
        }
    }

    public function move(float $dx, float $dy, float $dz) : void {
        $this->blocksAround = null;

        Timings::$entityMoveTimer->startTiming();

        $start = $this->asVector3();
        $end = $start->add($this->motion);

        $blockHit = null;
        $entityHit = null;
        $hitResult = null;

        foreach (VoxelRayTrace::betweenPoints($start, $end) as $vector3) {
            $block = $this->level->getBlockAt($vector3->x, $vector3->y, $vector3->z);

            $blockHitResult = $this->calculateInterceptWithBlock($block, $start, $end);
            if ($blockHitResult !== null) {
                $end = $blockHitResult->hitVector;
                $blockHit = $block;
                $hitResult = $blockHitResult;
                break;
            }
        }

        $entityDistance = PHP_INT_MAX;

        $newDiff = $end->subtract($start);
        foreach($this->level->getCollidingEntities($this->boundingBox->addCoord($newDiff->x, $newDiff->y, $newDiff->z)->expand(1, 1, 1), $this) as $entity){
            if ($entity->getId() === $this->getOwningEntityId() && $this->ticksLived < 5) {
                continue;
            }

            $entityBB = $entity->boundingBox->expandedCopy(0.3, 0.3, 0.3);
            $entityHitResult = $entityBB->calculateIntercept($start, $end);

            if ($entityHitResult === null) {
                continue;
            }

            $distance = $this->distanceSquared($entityHitResult->hitVector);

            if ($distance < $entityDistance) {
                $entityDistance = $distance;
                $entityHit = $entity;
                $hitResult = $entityHitResult;
                $end = $entityHitResult->hitVector;
            }
        }

        $this->x = $end->x;
        $this->y = $end->y;
        $this->z = $end->z;
        $this->recalculateBoundingBox();

        if ($hitResult !== null) {
            /** @var SnowballHitEvent|null $ev */
            $ev = null;
            if ($entityHit !== null) {
                $ev = new SnowballHitEntityEvent($this, $hitResult, $entityHit, $this->snowball);
            } elseif ($blockHit !== null) {
                $ev = new SnowballHitBlockEvent($this, $hitResult, $blockHit, $this->snowball);
            } else {
                assert(false, "unknown hit type");
            }

            if ($ev !== null) {
                $ev->call();
                $this->onHit($ev);

                if ($ev instanceof SnowballHitEntityEvent) {
                    $this->onHitEntity($ev->getEntityHit(), $ev->getRayTraceResult());
                } elseif ($ev instanceof SnowballHitBlockEvent) {
                    $this->onHitBlock($ev->getBlockHit(), $ev->getRayTraceResult());
                }
            }

            $this->isCollided = $this->onGround = true;
            $this->motion->x = $this->motion->y = $this->motion->z = 0;
        } else {
            $this->isCollided = $this->onGround = false;
            $this->blockHit = $this->blockHitId = $this->blockHitData = null;

            // recalculate angles
            $f = sqrt(($this->motion->x ** 2) + ($this->motion->z ** 2));
            $this->yaw = (atan2($this->motion->x, $this->motion->z) * 180 / M_PI);
            $this->pitch = (atan2($this->motion->y, $f) * 180 / M_PI);
        }

        $this->checkChunks();
        $this->checkBlockCollision();

        Timings::$entityMoveTimer->stopTiming();
    }

    protected function onHit(SnowballHitEvent $event) : void {

    }

}
