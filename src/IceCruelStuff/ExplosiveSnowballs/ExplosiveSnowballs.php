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

namespace IceCruelStuff\ExplosiveSnowballs;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\entity\projectile\Snowball as SnowballEntity;
use pocketmine\entity\Entity;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\ItemFactory;
use pocketmine\item\Item;
use pocketmine\level\Explosion;
use pocketmine\level\Position;
use pocketmine\nbt\JsonNbtParser;
use pocketmine\permission\Permission;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use IceCruelStuff\ExplosiveSnowballs\Command\CommandUI;
use IceCruelStuff\ExplosiveSnowballs\Entity\CustomSnowballEntity;
use IceCruelStuff\ExplosiveSnowballs\Event\SnowballHitEvent;
use IceCruelStuff\ExplosiveSnowballs\Item\ExplosiveSnowball;
use function count;

class ExplosiveSnowballs extends PluginBase implements Listener {

    const EXPLOSIVE = 100;

    public $block;
    public $config;
    public $player;

    public function onEnable() : void {
        Enchantment::registerEnchantment(new Enchantment(self::EXPLOSIVE, "Explosive", Enchantment::RARITY_RARE, Enchantment::SLOT_NONE, Enchantment::SLOT_ALL, 1));
        Entity::registerEntity(CustomSnowballEntity::class, true, ['Snowball', 'minecraft:snowball']);
        ItemFactory::registerItem(new ExplosiveSnowball(), true);
        $this->getServer()->getPluginManager()->addPermission(new Permission("snowballs.command", "Allows player to use ExplosiveSnowballs commands", Permission::DEFAULT_OP));
        $this->getServer()->getPluginManager()->addPermission(new Permission("snowballs.give", "Allows player to use /givesnowball command", Permission::DEFAULT_OP));

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        @mkdir($this->getDataFolder());
        if (!file_exists($this->getDataFolder() . "config.yml")) {
            $this->saveResource('config.yml');
        }

        $this->config = new Config($this->getDataFolder() . 'config.yml', Config::YAML, array(
            "disable-explosive-snowballs" => false,
            "disable-ui" => true,
            "default-snowball-name" => "Explosive Snowball",
            "explosion-size" => 7
        ));
        $this->config->save();

        if (!$this->config->get("disable-explosive-snowballs")) {
            $this->config->set("disable-explosive-snowballs", false);
            $this->config->save();
        }

        if (!$this->config->get("disable-ui")) {
            $this->config->set("disable-ui", false);
            $this->config->save();
        }

        if (!$this->config->get("default-snowball-name")) {
            $this->config->set("default-snowball-name", "Explosive Snowball");
            $this->config->save();
        }

        if (!$this->config->get("explosion-size")) {
            $this->config->set("explosion-size", 7);
            $this->config->save();
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
        switch ($command->getName()) {
            case "snowballs":
                if ($sender->hasPermission("snowballs.command")) {
                    if (!isset($args[0])) {
                        if ($sender instanceof Player) {
                            if ($this->config->get("disable-ui") == false) {
                                $ui = new CommandUI($this);
                                $ui->sendForm($sender);
                                return false;
                            } else {
                                $sender->sendMessage(TextFormat::RED . 'UI is disabled. To enable UI, set "disable-ui" to false in the config.yml');
                            }
                        } else {
                            $sender->sendMessage(TextFormat::RED . "Please use this command in-game");
                        }
                    } else {
                        switch ($args[0]) {
                            case "enable":
                            case "on":
                                if ($this->config->get("disable-explosive-snowballs") == false) {
                                    $sender->sendMessage(TextFormat::RED . "ExplosiveSnowballs are already enabled");
                                } elseif (!$this->config->get("disable-explosive-snowballs") == false) {
                                    $this->config->set("disable-explosive-snowballs", false);
                                    $this->config->save();
                                    $sender->sendMessage(TextFormat::GREEN . "ExplosiveSnowballs are now enabled");
                                }
                                break;
                            case "disable":
                            case "off":
                                if ($this->config->get("disable-explosive-snowballs") == true) {
                                    $sender->sendMessage(TextFormat::RED . "ExplosiveSnowballs are already disabled");
                                } elseif (!$this->config->get("disable-explosive-snowballs") == true) {
                                    $this->config->set("disable-explosive-snowballs", true);
                                    $this->config->save();
                                    $sender->sendMessage(TextFormat::GREEN . "ExplosiveSnowballs are now disabled");
                                }
                                break;
                        }
                    }
                } else {
                    $sender->sendMessage(TextFormat::RED . "You do not have the permission to use this command");
                }
                break;
            case "givesnowball":
                if ($sender->hasPermission("snowballs.give")) {
                    if (isset($args[0])) {
                        $name = $args[0];
                        $target = $this->getServer()->getPlayer($name);
                        if ($target instanceof Player) {
                            $itemName = $this->config->get("default-snowball-name");
                            if (isset($args[1])) {
                                $itemName = implode(array_slice($args, 1), " ");
                            }
                            $item = Item::get(Item::SNOWBALL, 0, 1, JsonNbtParser::parseJSON("{display:Name:{" . $itemName . "}}"));
                            $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(self::EXPLOSIVE)));
                            $target->getInventory()->addItem($item);
                            $sender->sendMessage(TextFormat::GREEN . "Gave " . $target->getName() . $item->getName());
                            return true;
                        } else {
                            $sender->sendMessage(TextFormat::RED . $name . " not found");
                            return true;
                        }
                    }
                    return false;
                } else {
                    $sender->sendMessage(TextFormat::RED . "You do not have permission to use this command");
                }
                break;
        }
        return true;
    }

    public function onSnowballHit(SnowballHitEvent $event) {
        $entity = $event->getEntity();
        $item = $event->getSnowball();
        if ($entity instanceof SnowballEntity) {
            if ($item->hasEnchantment(self::EXPLOSION)) {
                if ($this->config->get("disable-explosive-snowballs") == true) {
                    return;
                }
                $location = $entity->getLocation();
                $size = $this->config->get("explosion-size");

                $explosion = new Explosion($location, $size, null);
                $explosion->explodeA();
                $explosion->explodeB();
            }
        }
    }

}
