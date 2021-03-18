<?php

namespace HighestDreams\Graves\Entity;

use HighestDreams\Graves\Main;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class Timer extends Task
{

    private static $main;
    private static $api;

    public function __construct(Main $main)
    {
        self::$main = $main;
        self::$api = new Api (Main::getInstance());
    }

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick)
    {
        foreach (Server::getInstance()->getLevels() as $level) {
            foreach ($level->getEntities() as $entity) {
                if (self::$api::isGrave($entity)) {
                    if (isset(Main::$timer[$entity->getId()])) {
                        Main::$timer[$entity->getId()] = Main::$timer[$entity->getId()] - 1;
                        if (Main::$timer[$entity->getId()] <= 0) {
                            if (self::$api::isLimited($entity)) {
                                unset(Main::$limited[self::$api::getLimited($entity)]);
                            }
                            unset(Main::$timer[$entity->getId()]);
                            $entity->close();
                        }
                    } else {
                        $entity->close();
                    }
                }
            }
        }
    }
}