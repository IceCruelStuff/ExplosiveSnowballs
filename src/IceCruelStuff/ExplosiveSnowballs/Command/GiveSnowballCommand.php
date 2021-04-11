<?php

namespace IceCruelStuff\ExplosiveSnowballs\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\nbt\JsonNbtParser;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use IceCruelStuff\ExplosiveSnowballs\ExplosiveSnowballs;

class GiveSnowballCommand extends Command implements PluginIdentifiableCommand {

    private $plugin;

    public function __construct(ExplosiveSnowballs $plugin) {
        $this->plugin = $plugin;
        parent::__construct(
            "givesnowball",
            "Gives player an explosive snowball",
            "/givesnowball <player> <name>",
            ["gsb"]
        );
        $this->setPermission("snowballs.give");
    }

    public function getPlugin() : Plugin {
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $label, array $args) {
        if (!$this->testPermission($sender)) {
            return;
        }

        if (isset($args[0])) {
            $name = $args[0];
            $target = $this->plugin->getServer()->getPlayer($name);
            if ($target instanceof Player) {
                $itemName = $this->plugin->config->get("default-snowball-name");
                if (isset($args[1])) {
                    $itemName = implode(array_slice($args, 1), " ");
                }
                $item = Item::get(Item::SNOWBALL, 0, 1, JsonNbtParser::parseJSON("{display:Name:{" . $itemName . "}}"));
                $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(ExplosiveSnowballs::EXPLOSIVE)));
                $target->getInventory()->addItem($item);
                $sender->sendMessage(TextFormat::GREEN . "Gave " . $target->getName() . $item->getName());
            } else {
                $sender->sendMessage(TextFormat::RED . $name . " not found");
            }
        }
    }

}
