<?php

declare(strict_types=1);

namespace HighestDreams\Graves;

use HighestDreams\Graves\Entity\{Api, Grave, Timer};
use pocketmine\entity\Entity;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener
{

    public static $grave;
    public static $settings;
    public static $timer = [];
    public static $limited = [];
    public static $getInstance;

    /**
     * @return mixed
     */
    public static function getInstance()
    {
        return self::$getInstance;
    }

    public function onEnable()
    {
        self::$getInstance = $this;
        self::$grave = new Api ($this);
        Entity::registerEntity(Grave::class, true);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        self::$settings = new Config($this->getDataFolder() . 'Settings.yml');
        if (self::$settings->get('remove-graves') === true) {
            $this->getScheduler()->scheduleRepeatingTask(new Timer ($this), 20);
            foreach (['Settings.yml', 'grave.png'] as $resources) {
                $this->saveResource($resources);
            }
        }
    }

    /**
     * @param PlayerDeathEvent $event
     */
    public function onDeathEvent(PlayerDeathEvent $event)
    {
        $player = $event->getPlayer();
        $level = $player->getLevel();
        if ($player->isOnGround() and !$player->isUnderwater()) {
            if (self::$grave::isAbleToSpawnGrave($player)) {
                if ((empty(self::$settings->get('worlds'))) or (in_array($level->getFolderName(), self::$settings->get('worlds')))) {
                    self::$grave::spawnGrave($player);
                }
            }
        }
    }
}
