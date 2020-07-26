<?php

/*
 *
 *   _____          _____                 _  _____ _          __  __ 
 *  |_   _|        / ____|               | |/ ____| |        / _|/ _|
 *    | |  ___ ___| |     _ __ _   _  ___| | (___ | |_ _   _| |_| |_ 
 *    | | / __/ _ \ |    | '__| | | |/ _ \ |\___ \| __| | | |  _|  _|
 *   _| || (_|  __/ |____| |  | |_| |  __/ |____) | |_| |_| | | | |  
 *  |_____\___\___|\_____|_|   \__,_|\___|_|_____/ \__|\__,_|_| |_|  
 *
 * Licensed under the Apache License, Version 2.0 (the "License")
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at http://www.apache.org/licenses/LICENSE-2.0
 *
 * @author IceCruelStuff
 * @link https://github.com/IceCruelStuff/ExplosiveSnowballs
 *
*/

declare(strict_types=1);

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
use IceCruelStuff\ExplosiveSnowballs\CommandUI;
use function count;

class ExplosiveSnowballs extends PluginBase implements Listener {

    public $block;
    public $config;
    public $player;

    public function onEnable() : void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        @mkdir($this->getDataFolder());
        if (!file_exists($this->getDataFolder() . "config.yml")) {
            $this->saveResource('config.yml');
        }
        $this->config = new Config($this->getDataFolder() . 'config.yml', Config::YAML, array(
            "disable-explosive-snowballs" => false,
            "disable-ui" => true
        ));
        $this->config->save();
        if (!$this->config->get("disable-explosive-snowballs")) {
            $this->config->set("disable-explosive-snowballs", false);
        }
        if (!$this->config->get("disable-ui")) {
            $this->config->set("disable-ui", true);
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
        switch ($command->getName()) {
            case "snowballs":
                if ($sender->hasPermission("snowballs.command")) {
                    if ($this->config->get("disable-ui") == false && $sender instanceof Player) {
                        $ui = new CommandUI();
                        $ui->sendForm($sender);
                        return true;
                    }
                    switch ($args[0]) {
                        case "enable":
                        case "on":
                            if ($this->config->get("disable-explosive-snowballs") == false) {
                                $sender->sendMessage("ExplosiveSnowballs are already enabled");
                                return false;
                            } elseif (!$this->config->get("disable-explosive-snowballs") == false) {
                                $this->config->set("disable-explosive-snowballs", false);
                                $this->config->save();
                                $sender->sendMessage("ExplosiveSnowballs are now enabled");
                            }
                            break;
                        case "disable":
                        case "off":
                            if ($this->config->get("disable-explosive-snowballs") == true) {
                                $sender->sendMessage("ExplosiveSnowballs are already disabled");
                                return false;
                            } elseif (!$this->config->get("disable-explosive-snowballs") == true) {
                                $this->config->set("disable-explosive-snowballs", true);
                                $this->config->save();
                                $sender->sendMessage("ExplosiveSnowballs are now disabled");
                            }
                            break;
                    }
                } else {
                    $sender->sendMessage("You do not have the permission to use this command");
                }
        }
        return true;
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
