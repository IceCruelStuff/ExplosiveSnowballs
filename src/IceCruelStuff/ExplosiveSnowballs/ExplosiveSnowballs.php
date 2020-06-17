<?php

namespace IceCruelStuff\ExplosiveSnowballs;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\entity\projectile\Snowball as SnowballEntity;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\level\Explosion;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

use IceCruelStuff\ExplosiveSnowballs\Explosion;

use function count;

class ExplosiveSnowballs extends PluginBase implements Listener {

    public $block;
    public $config;
    public $player;

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        @mkdir($this->getDataFolder());
        if (!file_exists($this->getDataFolder() . "config.yml")) {
            $this->saveResource('config.yml');
        }
        $this->config = new Config($this->getDataFolder() . 'config.yml', Config::YAML, array(
            "disable-explosive-snowballs" => "false"
        ));
        $this->config->save();
        if (!$this->config->get("disable-explosive-snowballs")) {
            $this->config->set("disable-explosive-snowballs", false);
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
        switch ($command->getName()) {
            case "snowballs":
                if ($sender->hasPermission("snowballs.command")) {
                    
                    switch ($args[0]) {
                        case "enable":
                        case "on":
                            $this->config->set("disable-explosive-snowballs", false);
                            break;
                        case "disable":
                        case "off":
                            $this->config->set("disable-explosive-snowballs", true);
                            break;
                    }
                }
        }
    }

    public function onProjectileHit(ProjectileHitEvent $event) {
        $entity = $event->getEntity();
        if ($entity instanceof SnowballEntity) {
            if ($this->config->get("disable-explosive-snowballs") == true) {
                return;
            }
            $location = $entity->getLocation();
            $explosion = new Explosion($location);
            $explosion->explodeA();
            $explosion->explodeB();
        }
    }

}
