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

namespace IceCruelStuff\ExplosiveSnowballs\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\form\Form;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use IceCruelStuff\ExplosiveSnowballs\ExplosiveSnowballs as Main;
use IceCruelStuff\ExplosiveSnowballs\GiveSnowballUI;

class CommandUI {

    public $form;
    public $player;

    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function sendForm($sender) : void {
        $form = new SimpleForm(function (Player $player, $data = null) {
            if ($data === null) {
                return;
            }

            $this->createConfig();

            switch ($data) {
                case 0:
                    $this->plugin->config->set("disable-explosive-snowballs", false);
                    $this->plugin->config->save();
                    $player->sendMessage(TextFormat::RED . 'Explosive Snowballs have been enabled.');
                    break;
                case 1:
                    $this->plugin->config->set("disable-explosive-snowballs", true);
                    $this->plugin->config->save();
                    $player->sendMessage(TextFormat::RED . 'Explosive Snowballs have been disabled.');
                    break;
                case 2:
                    if ($player->hasPermission("snowballs.give")) {
                        $ui = new GiveSnowballUI($this);
                        $ui->sendCustomForm($player);
                        break;
                    }
                    $player->sendMessage(TextFormat::RED . 'You do not have permission to use this command');
                    break;
                case 3:
                    $player->sendMessage('Closed');
                    break;
            }
        });
        $form->setTitle("ExplosiveSnowballs");
        $form->addButton("Enable");
        $form->addButton("Disable");
        $form->addButton("Give Snowball");
        $form->addButton("Close");
        $form->sendToPlayer($sender);
    }

    private function createConfig() {
        $dataFolder = $this->plugin->getDataFolder();
        @mkdir($dataFolder);
        if (!file_exists($dataFolder . "config.yml")) {
            $this->plugin->saveResource('config.yml');
        }

        if (!$this->plugin->config->get("disable-explosive-snowballs")) {
            $this->plugin->config->set("disable-explosive-snowballs", false);
            $this->plugin->config->save();
        }
        if (!$this->plugin->config->get("disable-ui")) {
            $this->plugin->config->set("disable-ui", true);
            $this->plugin->config->save();
        }
    }

}
