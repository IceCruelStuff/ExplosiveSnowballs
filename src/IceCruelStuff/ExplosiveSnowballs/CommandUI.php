<?php

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
        }
        $form->setTitle("ExplosiveSnowballs");
        $form->addButton("Enable");
        $form->addButton("Disable");
        $form->sendToPlayer($sender);
        return $form;
    }

}
