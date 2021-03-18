<?php

declare(strict_types=1);

namespace HighestDreams\Graves\Entity;

use HighestDreams\Graves\Main;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;

class Grave extends Human {

    public const grave = '{"format_version": "1.8.0","geometry.gravestone": {"texturewidth": 128,"textureheight": 128,"visible_bounds_width": 2,"visible_bounds_height": 1,"visible_bounds_offset": [0, 0.5, 0],"bones": [{"name": "tombe","pivot": [0, 0, 0],"cubes": [{"origin": [-6, -1, 7], "size": [12, 17, 4], "uv": [0, 0]},{"origin": [-6, -2, -11], "size": [12, 2, 18], "uv": [0, 45]},{"origin": [-5, 16, 7], "size": [10, 1, 4], "uv": [2, 0]}]},{"name": "plaque","pivot": [0, 0, 0],"cubes": [{"origin": [3, 7, -8], "size": [1, 4, 1], "uv": [0, 34]},{"origin": [2, 6, -9], "size": [3, 2, 3], "uv": [0, 29]},{"origin": [-6, 0, -11], "size": [12, 6, 18], "uv": [0, 21]}]}]}}';
    private static $Name = 'grave.png';
    private static $Api;

    public function __construct(Level $level, CompoundTag $nbt)
    {
        self::$Api = new Api (Main::getInstance());
        parent::__construct($level, $nbt);
    }

    /**
     * @return string
     */
    protected function getSkinBytes () {
        $path = Main::getInstance()->getDataFolder() . self::$Name;
        $img = @imagecreatefrompng($path);
        $skinbytes = "";
        $s = (int)@getimagesize($path)[1];
        for($y = 0; $y < $s; $y++) {
            for($x = 0; $x < 64; $x++) {
                $colorat = @imagecolorat($img, $x, $y);
                $a = ((~((int)($colorat >> 24))) << 1) & 0xff;
                $r = ($colorat >> 16) & 0xff;
                $g = ($colorat >> 8) & 0xff;
                $b = $colorat & 0xff;
                $skinbytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($img);
        return $skinbytes;
    }

    public function initEntity(): void
    {
        $this->setHealth(20);
        $this->setScale(1.5);
        $this->setSkin(new Skin($this->skin->getSkinId(), $this->getSkinBytes(), "", "geometry.gravestone", self::grave));
        parent::initEntity();
    }

    /**
     * @return bool
     */
    public function canBeMovedByCurrents(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function canPickupXp(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function hasMovementUpdate(): bool
    {
        return false;
    }

    /**
     * @param EntityDamageEvent $source
     */
    public function attack(EntityDamageEvent $source): void{
        $source->setCancelled(true);
    }
}