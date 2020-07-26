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

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\form\Form;
use pocketmine\Player;
use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use IceCruelStuff\ExplosiveSnowballs\ExplosiveSnowballs as Main;

class SnowballsCommand extends Form {

    public $form;
    public $player;

    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function sendForm($sender) : void {
        $form = new SimpleForm(function (Player $player, data = null) {
            if ($data === null) {
                return;
            }
        });
        $config = $plugin->config;
        @mkdir($this->getDataFolder());
        if (!file_exists($this->getDataFolder() . "config.yml")) {
            $this->saveResource('config.yml');
        }
        if (!$this->config->get("disable-explosive-snowballs")) {
            $this->config->set("disable-explosive-snowballs", false);
        }
        if (!$this->config->get("disable-ui")) {
            $this->config->set("disable-ui", true);
        }

        switch ($data) {
            case 0:
                $this->config->set("disable-explosive-snowballs", false);
                break;
            case 1:
                $this->config->set("disable-explosive-snowballs", true);
                break;
            case 2:
                $sender->sendMessage("Closed");
                break;
        }
        $form->setTitle("ExplosiveSnowballs");
        $form->addButton("Enable");
        $form->addButton("Disable");
        $form->addButton("Close");
        $form->sendToPlayer($sender);
        return $form;
    }

}
