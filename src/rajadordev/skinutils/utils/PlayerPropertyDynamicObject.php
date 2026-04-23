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

namespace rajadordev\skinutils\utils;

use pocketmine\Player;
use pocketmine\Server;

abstract class PlayerPropertyDynamicObject extends DynamicObject
{

    const DATA_PLAYER_USERNAME = 'player_username';

    /** @var string */
    private $username;

    public function __construct(string $username)
    {
        $this->username = $username;
    }

    public function getUsername(bool $lowercase = false) : string 
    {
        return $lowercase ? strtolower($this->username) : $this->username;
    }

    /** @return Player|null */
    public function getPlayer() 
    {
        return Server::getInstance()->getPlayerExact($this->getUsername());
    }

    public function isOnline() : bool 
    {
        return $this->getPlayer() instanceof Player;
    }


    /** @return array{player_username:string} */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            self::DATA_PLAYER_USERNAME => $this->getUsername()
        ]);
    }

    /**
     * @param array{player_username:string} $data
     * @return string
     */
    public static function usernameFromData(array $data) : string 
    {
        return $data[self::DATA_PLAYER_USERNAME];
    }

}