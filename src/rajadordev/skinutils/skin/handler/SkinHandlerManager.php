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

namespace rajadordev\skinutils\skin\handler;

use InvalidArgumentException;
use rajadordev\skinutils\skin\holder\PlayerSkinHolder;
use rajadordev\skinutils\skin\save\OfflinePlayersSkinsSave;
use rajadordev\skinutils\skin\save\OnlinePlayersSkinsSave;
use SmartCommand\utils\SingletonTrait;

class SkinHandlerManager 
{

    use SingletonTrait;

    /** @var array<string,array<int,SkinHandler>> */
    protected $handlers = [];

    public static function init()
    {
        new self;
    }

    public function __construct()
    {
        self::setInstance($this);
    }

    /**
     * @param SkinHandler $handler
     * @return void
     * @throws InvalidArgumentException
     */
    public function registerHandler(SkinHandler $handler)
    {
        $id = $handler->getHandlerId();
        $playerUsername = strtolower($handler->getTargetUsername());
        if (!isset($this->handlers[$playerUsername])) {
            $this->handlers[$playerUsername] = [];
        }

        if (isset($this->handlers[$playerUsername][$id])) {
            throw new InvalidArgumentException("Handler $id for the player $playerUsername is already registered");
        }
        $this->handlers[$playerUsername][$id] = $handler;

        $onlinePlayerSkin = OnlinePlayersSkinsSave::getInstance()->getHolderSkin($playerUsername);

        if ($onlinePlayerSkin) {
            $handler->onLoad($onlinePlayerSkin);
        } else {
            OfflinePlayersSkinsSave::getInstance()->getSkin($playerUsername)->then(
                function ($skin) use ($handler, $id, $playerUsername) {
                    if (isset($this->handlers[$playerUsername][$id])) {
                        if ($skin) {
                            $handler->onLoad($skin);
                            return;
                        }
                        $handler->onLoad();
                    }
                }
            )->catch(
                function () use ($handler, $playerUsername, $id) {
                    if (isset($this->handlers[$playerUsername][$id])) {
                        $handler->onLoad();
                    }
                }
            );
        }
    }

    public function unregisterSkinHandler(SkinHandler $handler)
    {
        $username = strtolower($handler->getTargetUsername());
        $id = $handler->getHandlerId();
        if (isset($this->handlers[$username][$id])) {
            $handler->onUnregister();
        }
    }

    /**
     * @internal Used by the OfflinePlayersSkinSave
     *
     * @param PlayerSkinHolder $newSkin
     * @return void
     */
    public function callHandlers(PlayerSkinHolder $newSkin)
    {
        if (isset($this->handlers[$username = $newSkin->getHolderIdentifier()])) {
            foreach ($this->handlers[$username] as $handler) {
                $handler->onSkinUpdate($newSkin);
            }
        }
    }


}