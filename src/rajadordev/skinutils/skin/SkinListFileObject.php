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

use pocketmine\utils\Config;
use rajadordev\skinutils\skin\holder\SkinHolder;
use rajadordev\skinutils\utils\DynamicObject;
use rajadordev\skinutils\utils\ObjectSerializableList;

class SkinListFileObject extends ObjectSerializableList
{

    use SkinListTrait;

    /** @var boolean */
    protected $autoSave;

    public function __construct(string $filePath, bool $autoSave, int $fileType = Config::JSON)
    {
        $this->autoSave = $autoSave;
        return parent::__construct($filePath, $fileType);
    }

    public function getObjectList(): array
    {
        return $this->skinsHolders;
    }

    protected function onLoad(DynamicObject $obj)
    {
        /** @var SkinHolder $obj */
        $this->registerHolder($obj);
    }

    protected function onHolderRegister(SkinHolder $holder)
    {
        $this->tryAutoSave();
    }

    protected function onHolderUnregister(SkinHolder $holder)
    {
        $this->tryAutoSave();
    }

    protected function tryAutoSave() : bool 
    {
        if ($this->autoSave) {
            $this->save();
            return true;
        }
        return false;
    }

}