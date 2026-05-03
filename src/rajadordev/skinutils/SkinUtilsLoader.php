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

namespace rajadordev\skinutils;

use pocketmine\plugin\PluginBase;
use rajadordev\autoupdater\api\CheckUpdateScheduler;
use rajadordev\autoupdater\api\plugin\defaults\github\GitHubPluginUpdaterAPI;
use rajadordev\autoupdater\api\PluginUpdaterChecker;
use rajadordev\skinutils\command\SaveSkinCommand;
use rajadordev\skinutils\command\SetSkinCommand;
use rajadordev\skinutils\skin\handler\SkinHandlerManager;
use rajadordev\skinutils\skin\save\OfflinePlayersSkinsSave;
use rajadordev\skinutils\skin\save\OnlinePlayersSkinsSave;
use SmartCommand\api\SmartCommandAPI;
use SmartCommand\message\DefaultMessages;
use SmartCommand\utils\SingletonTrait;

class SkinUtilsLoader extends PluginBase
{

    const PREFIX = '§l§eSKIN§6UTILS  §r§7';
 
    use SingletonTrait;
 
    public function onLoad()
    {
        self::setInstance($this);
    }

    public function onEnable()
    {
        if (!file_exists($dir = $this->getDataFolder()))
        {
            mkdir($dir);
        }

        SkinHandlerManager::init();

        OfflinePlayersSkinsSave::init();
        if (!file_exists($skinsDir = OfflinePlayersSkinsSave::getInstance()->getOfflineSkinsFolder())) {
            mkdir($skinsDir);
        }
        
        OnlinePlayersSkinsSave::init($this);

        CheckUpdateScheduler::getInstance()->schedule(
            new PluginUpdaterChecker(
                $this,
                GitHubPluginUpdaterAPI::createFromPlugin(
                    $this,
                    'RajadorDev',
                    'SkinUtilsLib'
                )
            )
        );

    }

}