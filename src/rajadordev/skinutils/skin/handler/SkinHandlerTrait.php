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

use rajadordev\skinutils\skin\holder\PlayerSkinHolder;

trait SkinHandlerTrait 
{

    /** @var string */
    protected $targetSkinUsername;

    /** @var PlayerSkinHolder|null */
    protected $currentTargetSkin = null;


    protected function setTargetSkinUsername(string $username, bool $registerHandler = true) : self
    {
        $this->targetSkinUsername = $username;
        if ($registerHandler) {
            $manager = SkinHandlerManager::getInstance();
            $manager->unregisterSkinHandler($this);
            $manager->registerHandler($this);
        }
        return $this;
    }

    public function getTargetUsername() : string 
    {
        return $this->targetSkinUsername;
    }

    public function getCurrentSkin()
    {
        return $this->currentTargetSkin;
    }

    /**
     * @param PlayerSkinHolder|null $skin
     * @return self
     */
    public function setCurrentTargetSkin($skin) : self
    {
        assert($skin instanceof PlayerSkinHolder || is_null($skin));
        $this->currentTargetSkin = $skin;
        return $this;
    }

    public function onLoad(PlayerSkinHolder $skin = null)
    {
        $this->setCurrentTargetSkin($skin);
    }

    public function onSkinUpdate(PlayerSkinHolder $skin)
    {
        $this->setCurrentTargetSkin($skin);
    }

    public function onUnregister() 
    {
        $this->currentTargetSkin = null;
    }

    protected function forceUnregisterHandler()
    {
        SkinHandlerManager::getInstance()->unregisterSkinHandler($this);
    }


}