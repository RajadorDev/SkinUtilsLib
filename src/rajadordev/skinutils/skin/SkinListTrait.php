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

namespace rajadordev\skinutils\skin;

use rajadordev\skinutils\skin\exception\SkinHolderAlreadyRegisteredException;
use rajadordev\skinutils\skin\holder\SkinHolder;

trait SkinListTrait 
{

    /** @var array<string,SkinHolder> */
    protected $skinsHolders = [];

    /**
     * @param SkinHolder $holder
     * @throws SkinHolderAlreadyRegisteredException
     * @return void
     */
    public function registerHolder(SkinHolder $holder)
    {
        $id = $holder->getHolderIdentifier();
        if (isset($this->skinsHolders[$id])) {
            throw new SkinHolderAlreadyRegisteredException("Skin of holder $id is already registered");
        }
        $this->skinsHolders[$id] = $holder;
        $this->onHolderRegister($holder);
    }

    public function unregisterHolder(SkinHolder $holder) : bool 
    {
        if (isset($this->skinsHolders[$id = $holder->getHolderIdentifier()])) {
            unset($this->skinsHolders[$id]);
            $this->onHolderUnregister($holder);
            return true;
        }
        return false;
    }

    public function getHolderSkin(string $holderId)
    {
        return $this->skinsHolders[$holderId] ?? null;
    }

    public function getAllHolders() : array 
    {
        return $this->skinsHolders;
    }

    /**
     * @param SkinHolder $holder
     * @return void
     */
    abstract protected function onHolderRegister(SkinHolder $holder);

    /**
     * @param SkinHolder $holder
     * @return void
     */
    abstract protected function onHolderUnregister(SkinHolder $holder);

}