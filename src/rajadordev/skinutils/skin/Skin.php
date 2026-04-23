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

use pocketmine\entity\Attribute;
use pocketmine\entity\Human;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\protocol\PlayerListPacket;
use pocketmine\network\protocol\PlayStatusPacket;
use pocketmine\network\protocol\StartGamePacket;
use pocketmine\Player;
use pocketmine\Server;
use rajadordev\skinutils\utils\DynamicObject;

class Skin extends DynamicObject
{

    const DATA_SKIN_ID = 'id';

    const DATA_SKIN_DATA = 'data';

    /** @var string */
    protected $id, $data;

    public function __construct(
        string $id,
        string $data
    )
    {
        $this->id = $id;
        $this->data = $data;
    }

    public function getSkinId() : string 
    {
        return $this->id;
    }

    public function getData() : string 
    {
        return $this->data;
    }

    public function serializeSkinData() : string 
    {
        return base64_encode($this->data);
    }

    public function saveIn(string $filePath)
    {
        file_put_contents($filePath, json_encode($this));
    }

    public function applyInCompoundTag(CompoundTag &$nbt) : Skin
    {
        $nbt->Skin = new CompoundTag('Skin', [
            new StringTag('Name', $this->getSkinId()),
            new StringTag('Data', $this->getData())
        ]);
        return $this;
    }

    public function applyInHuman(Human $human)
    {
        $human->setSkin($this->getData(), $this->getSkinId());
        $human->respawnToAll();   
    }

    public function applyInPlayer(Player $player) 
    {
        $player->despawnFromAll();
        $player->setSkin($this->getData(), $this->getSkinId());
        $player->spawnToAll();
    }

    public static function fromFile(string $filePath) : Skin
    {
        $fileData = file_get_contents($filePath);
        $fileData = json_decode($fileData, true);
        /** @var class-string<DynamicObject> */
        $objectId = $fileData[DynamicObject::SOURCE_ID];
        return $objectId::unserialize($fileData);
    }

    public static function fromHuman(Human $human) : Skin
    {
        return new self(
            $human->getSkinId(),
            $human->getSkinData()
        );
    }

    protected function serializeExtraData(): array
    {
        return [
            self::DATA_SKIN_ID => $this->id,
            self::DATA_SKIN_DATA => $this->serializeSkinData()
        ];
    }

    public static function unserializeSkinData(array $allData) : string 
    {
        return base64_decode($allData[self::DATA_SKIN_DATA]);
    }

    public static function unserialize(array $data): DynamicObject
    {
        return new self(
            $data[self::DATA_SKIN_ID],
            self::unserializeSkinData($data)
        );
    }
}