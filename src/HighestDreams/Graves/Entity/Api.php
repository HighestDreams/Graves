<?php

declare(strict_types=1);

namespace HighestDreams\Graves\Entity;

use HighestDreams\Graves\Main;
use pocketmine\entity\Entity;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Player;

class Api
{

    private static $main;

    public function __construct(Main $main)
    {
        self::$main = $main;
    }

    /**
     * @param Player $player
     */
    public static function spawnGrave(Player $player)
    {
        $nbt = new CompoundTag("", [
            new ListTag("Pos", [
                new DoubleTag("", $player->getX()),
                new DoubleTag("", $player->getY()),
                new DoubleTag("", $player->getZ())
            ]),
            new ListTag("Motion", [
                new DoubleTag("", 0),
                new DoubleTag("", 0),
                new DoubleTag("", 0)
            ]),
            new ListTag("Rotation", [
                new FloatTag("", 0),
                new FloatTag("", 0)
            ])
        ]);
        $nbt->setTag($player->namedtag->getTag("Skin"));
        $npc = new Grave($player->getLevel(), $nbt);
        if (!Main::$settings->get('show-name')) {
            $npc->setNameTagAlwaysVisible(false);
        } else {
            $npc->setNameTag("✞ {$player->getName()} ✞");
            $npc->setNameTagAlwaysVisible(true);
        }
        if (Main::$settings->get('limit-spawn') === true) {
            Main::$limited[$player->getName()] = $npc->getId();
        }
        Main::$timer[$npc->getId()] = Main::$settings->get('timer') ?? 50;
        $npc->spawnToAll();
    }

    /**
     * @param Entity $entity
     * @return bool
     */
    public static function isGrave(Entity $entity): bool
    {
        return $entity instanceof Grave;
    }

    /**
     * @param Entity $entity
     * @return bool
     */
    public static function isLimited(Entity $entity): bool
    {
        return in_array($entity->getId(), Main::$limited);
    }

    /**
     * @param Entity $entity
     * @return false|int|string
     */
    public static function getLimited(Entity $entity)
    {
        return array_search($entity->getId(), Main::$limited);
    }

    /**
     * @param Player $player
     * @return bool
     */
    public static function isAbleToSpawnGrave(Player $player): bool
    {
        return (Main::$settings->get('limit-spawn') === false) or (Main::$settings->get('limit-spawn') === true and !isset(Main::$limited[$player->getName()]));
    }
}