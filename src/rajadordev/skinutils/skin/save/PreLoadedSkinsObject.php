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

namespace rajadordev\skinutils\skin\save;

use InvalidArgumentException;
use rajadordev\skinutils\skin\Skin;

abstract class PreLoadedSkinsObject 
{

    /** @var array<string,Skin> */
    protected static $skins;

    /**
     * You can register skins here, it will be called once
     *
     * @return void
     */
    abstract protected static function setup();

    protected static function transformName(string $identifier) : string 
    {
        return strtoupper($identifier);
    }

    protected static function register(string $identifier, Skin $skin)
    {
        $identifier = self::transformName($identifier);
        if (isset(self::$skins[$identifier])) {
            throw new InvalidArgumentException("Skin $identifier is already registered");
        }
        self::$skins[$identifier] = $skin;
    }

    public static function __callStatic($name, $arguments)
    {

        if (!isset(self::$skins)) {
            static::setup();
        }

        if (isset(self::$skins[$name])) {
            return self::$skins[$name];
        }
        throw new InvalidArgumentException("Skin $name is not registered");
    }
    
}