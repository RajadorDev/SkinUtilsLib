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

namespace rajadordev\skinutils\skin\holder;

use rajadordev\skinutils\skin\Skin;
use rajadordev\skinutils\utils\DynamicObject;
use rajadordev\skinutils\utils\PlayerPropertyDynamicObject;

class PlayerSkinHolder extends PlayerPropertyDynamicObject implements SkinHolder
{

    const DATA_SKIN = 'skin';

    /** @var Skin */
    protected $skin;

    public function __construct(
        string $username,
        Skin $skin
    )
    {
        $this->skin = $skin;
        parent::__construct($username);
    }

    public function getHolderIdentifier(): string
    {
        return $this->getUsername(true);
    }

    public function getSkin() : Skin
    {
        return $this->skin;
    }

    public function setSkin(Skin $newSkin) : SkinHolder
    {
        $this->skin = $newSkin;
        return $this;
    }

    public function serializeSkin() : array 
    {
        return $this->skin->jsonSerialize();
    }

    public function save(string $path)
    {
        file_put_contents($path, json_encode($this->jsonSerialize()));
    }

    protected function serializeExtraData(): array
    {
        return [
            self::DATA_SKIN => $this->serializeSkin()
        ];
    }

    public static function unserializeSkin(array $data) : Skin
    {
        /** @var class-string<Skin> */
        $sourceId = $data[DynamicObject::SOURCE_ID];
        return $sourceId::unserialize($data);
    }

    public static function unserialize(array $data): DynamicObject
    {
        return new self(
            self::usernameFromData($data),
            self::unserializeSkin($data)
        );
    }
}