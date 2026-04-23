<?php

declare (strict_types=1);
 
/***
 *   
 * Rajador Developer Diamond API
 * 
 *  ██████╗  █████╗      ██╗ █████╗ ██████╗  ██████╗ ██████╗ 
 *  ██╔══██╗██╔══██╗     ██║██╔══██╗██╔══██╗██╔═══██╗██╔══██╗
 *  ██████╔╝███████║     ██║███████║██║  ██║██║   ██║██████╔╝
 *  ██╔══██╗██╔══██║██   ██║██╔══██║██║  ██║██║   ██║██╔══██╗
 *  ██║  ██║██║  ██║╚█████╔╝██║  ██║██████╔╝╚██████╔╝██║  ██║
    ╚═╝  ╚═╝╚═╝  ╚═╝ ╚════╝ ╚═╝  ╚═╝╚═════╝  ╚═════╝ ╚═╝  ╚═╝
 * 
 * GitHub: https://github.com/rajadordev
 * 
 * Discord: rajadortv
 * 
 * @copyright 2023 - 2027 Rajador Developer
 * 
 * Repository: https://github.com/RajadorDev/SkinUtilsLib
 * 
**/ 

namespace rajadordev\skinutils\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use rajadordev\skinutils\skin\holder\PlayerSkinHolder;
use rajadordev\skinutils\skin\save\OnlinePlayersSkinsSave;
use rajadordev\skinutils\skin\Skin;

class OnlineSkinSaveListener implements Listener
{

    /**
     * @priority LOWEST
     */
    public function join(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        OnlinePlayersSkinsSave::getInstance()->registerHolder(
            new PlayerSkinHolder($player->getName(), Skin::fromHuman($player))
        );
    }

    /**
     * @priority MONITOR
     */
    public function quit(PlayerQuitEvent $event)
    {
        $player = $event->getPlayer();
        $username = strtolower($player->getName());
        $save = OnlinePlayersSkinsSave::getInstance();
        if ($holder = $save->getHolderSkin($username)) {
            $save->unregisterHolder($holder);
        }
    }
}