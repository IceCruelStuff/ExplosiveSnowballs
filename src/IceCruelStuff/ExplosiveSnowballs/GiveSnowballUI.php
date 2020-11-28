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

use pocketmine\item\Item;
use pocketmine\nbt\JsonNbtParser;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;

class GiveSnowballUI {
/*
    private $plugin;

    public function __construct($plugin) {
        $this->plugin = $plugin;
    }

    public function sendCustomForm($sender) {
        $customForm = new CustomForm(function (Player $customPlayer, array $data = null) {
            if ($data === null) {
                return;
            }

            $name = $data[1];
            $target = $this->plugin->getServer()->getPlayer($name);
            if ($target instanceof Player) {
                $itemName = implode(array_slice($data, 2), " ");
                $item = Item::get(Item::SNOWBALL, 0, 1, JsonNbtParser::parseJSON("{display:Name:{$itemName}}"));
                $target->getInventory()->addItem($item);
                $sender->sendMessage(TextFormat::GREEN . "Gave " . $target->getName() . $item->getName());
            } else {
                $sender->sendMessage(TextFormat::RED . $name . " not found");
            }

            switch ($data) {
                case 0:
                    
            }
        });
        $customForm->setTitle(TextFormat::AQUA . 'Explosive Snowballs');
        $customForm->setLabel('Give Explosive Snowballs to players!');
        $customForm->addInput('Player name', 'Steve');
        $customForm->addInput('Custom name', 'Explosive Snowball');
        $customForm->addSlider('Amount', 1, 100);
        $customForm->sendToPlayer($sender);
    }
*/
}
