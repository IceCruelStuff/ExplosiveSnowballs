<?php

namespace IceCruelStuff\ExplosiveSnowballs\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use IceCruelStuff\ExplosiveSnowballs\Command\CommandUI;
use IceCruelStuff\ExplosiveSnowballs\ExplosiveSnowballs;

class SnowballCommand extends Command implements PluginIdentifiableCommand {

    private $plugin;

    public function __construct(ExplosiveSnowballs $plugin) {
        $this->plugin = $plugin;
        parent::__construct(
            "snowballs",
            "Manages ExplosiveSnowballs plugin",
            "/snowballs <on|off>",
            ["sb"]
        );
        $this->setPermission("snowballs.command");
    }

    public function getPlugin() : Plugin {
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $label, array $args) {
        if (!$this->testPermission($sender)) {
            return;
        }

        if (!isset($args[0])) {
            if ($sender instanceof Player) {
                if ($this->plugin->config->get("disable-ui") == false) {
                    $ui = new CommandUI($this->plugin);
                    $ui->sendForm($sender);
                } else {
                    $sender->sendMessage(TextFormat::RED . "UI is disabled. To enable UI, set 'disable-ui' to false in the config.yml");
                }
            } else {
                $sender->sendMessage(TextFormat::RED . "Please specify an option");
            }
        } else {
            switch ($args[0]) {
                case "on":
                case "enable":
                    if ($this->config->get("disable-explosive-snowballs") == false) {
                        $sender->sendMessage(TextFormat::RED . "ExplosiveSnowballs are already enabled");
                    } elseif (!$this->config->get("disable-explosive-snowballs") == false) {
                        $this->config->set("disable-explosive-snowballs", false);
                        $this->config->save();
                        $sender->sendMessage(TextFormat::GREEN . "ExplosiveSnowballs are now enabled");
                    }
                    break;
                case "off":
                case "disable":
                    if ($this->config->get("disable-explosive-snowballs") == true) {
                        $sender->sendMessage(TextFormat::RED . "ExplosiveSnowballs are already disabled");
                    } elseif (!$this->config->get("disable-explosive-snowballs") == true) {
                        $this->config->set("disable-explosive-snowballs", true);
                        $this->config->save();
                        $sender->sendMessage(TextFormat::GREEN . "ExplosiveSnowballs are now disabled");
                    }
                    break;
                default:
                    $sender->sendMessage(TextFormat::RED . "Please specify an option");
                    break;
            }
        }
    }

}
