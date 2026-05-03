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

interface SkinHandler 
{

    /**
     * It must to return a unique integer id (from the handler)
     *
     * @return integer
     */
    public function getHandlerId() : int;

    /**
     * It must to return the target player's username
     *
     * @return string
     */
    public function getTargetUsername() : string;

    /**
     * Called when this handler is registered and the current player skin is loaded
     *
     * @param PlayerSkinHolder|null $skinHolder
     * @return void
     */
    public function onLoad(PlayerSkinHolder $skinHolder = null);

    /**
     * Called every time that the player change him skin
     *
     * @param PlayerSkinHolder $playerHolder
     * @return void
     */
    public function onSkinUpdate(PlayerSkinHolder $playerHolder);

    /**
     * Called when the SkinHandlerManager unregister the handler
     *
     * @return void
     */
    public function onUnregister();
}