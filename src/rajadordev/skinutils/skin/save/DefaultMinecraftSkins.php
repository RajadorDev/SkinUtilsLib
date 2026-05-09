<?php

declare(strict_types=1);

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

use rajadordev\skinutils\skin\Skin;
use rajadordev\skinutils\SkinUtilsLoader;

/**
 * @method static Skin STEVE()
 * @method static Skin ALEX()
 */
final class DefaultMinecraftSkins extends PreLoadedSkinsObject
{

    protected static function setup() {
        if (!file_exists($defaultSkinsFolder = SkinUtilsLoader::getInstance()->getDefaultsSkinsFolder())) {
            mkdir($defaultSkinsFolder);
        }
        self::registerFromResources('steve', 'Standard_Steve');
        self::registerFromResources('alex', 'Standard_Alex');
    }

    protected static function registerFromResources(string $skinName, string $skinId)
    {
        $loader = SkinUtilsLoader::getInstance();
        $fileName = $skinName . '.png';
        $loader->saveResource('defaults/' . $fileName);
        $file = $loader->getDataFolder() . 'defaults' . DIRECTORY_SEPARATOR . $fileName;
        $skin = Skin::syncFromImageFile($file, $skinId);
        self::register($skinName, $skin);
    }
}
