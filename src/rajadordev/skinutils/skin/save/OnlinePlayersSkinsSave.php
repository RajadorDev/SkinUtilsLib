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

use pocketmine\Player;
use rajadordev\skinutils\skin\holder\SkinHolder;
use rajadordev\skinutils\skin\SkinListTrait;
use pocketmine\plugin\PluginLogger;
use rajadordev\skinutils\listener\OnlineSkinSaveListener;
use rajadordev\skinutils\skin\handler\SkinHandlerManager;
use rajadordev\skinutils\SkinUtilsLoader;
use rajadordev\skinutils\skin\holder\PlayerSkinHolder;
use rajadordev\skinutils\utils\Performance;
use SmartCommand\utils\SingletonTrait;

class OnlinePlayersSkinsSave 
{

    use SkinListTrait, SingletonTrait;

    /** @var PluginLogger */
    protected $logger;

    public static function init(SkinUtilsLoader $plugin) : self 
    {
        $instance = new self($plugin->getLogger());
        $plugin->getServer()->getPluginManager()->registerEvents(
            new OnlineSkinSaveListener,
            $plugin
        );
        return $instance;
    }

    public function __construct(PluginLogger $logger)
    {
        $this->logger = $logger;
        self::setInstance($this);
    }

    protected function onHolderRegister(SkinHolder $holder)
    {
        $this->logger->debug("Player skin {$holder->getHolderIdentifier()} registered");
        if ($holder instanceof PlayerSkinHolder) {
            $this->logger->debug("Saving {$holder->getUsername()}'s skin...");
            $performance = Performance::start();
            OfflinePlayersSkinsSave::getInstance()->save($holder)->then(
                function (bool $save) use ($holder, $performance) {
                    $time = $performance->finish()->getFormattedResult();
                    if ($save) {
                        $this->logger->debug("{$holder->getUsername()}'s skin saved sucefully in $time");
                        SkinHandlerManager::getInstance()->callHandlers($holder);
                    } else {
                        $this->logger->debug("{$holder->getUsername()}'s is already saved. Check finished in $time");
                    }
                }
            );
        }
    }

    protected function onHolderUnregister(SkinHolder $holder)
    {
        $this->logger->debug("Player skin {$holder->getHolderIdentifier()} unregistered");
    }

    public function setDefaultSkin(Player $player)
    {
        $this->getHolderSkin(strtolower($player->getName()))->getSkin()->applyInPlayer($player);
    }
}