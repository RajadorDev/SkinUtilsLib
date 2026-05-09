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

use InvalidArgumentException;
use pocketmine\entity\Attribute;
use pocketmine\entity\Human;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\protocol\PlayerListPacket;
use pocketmine\network\protocol\PlayStatusPacket;
use pocketmine\network\protocol\StartGamePacket;
use pocketmine\Player;
use pocketmine\Server;
use rajadordev\skinutils\skin\img\PngSkinImageType;
use rajadordev\skinutils\skin\img\SkinImageType;
use rajadordev\skinutils\skin\task\CreateSkinImageAsyncTask;
use rajadordev\skinutils\utils\DynamicObject;
use rajadordev\skinutils\utils\promise\Promise;

class Skin extends DynamicObject
{

    const DATA_SKIN_ID = 'id';

    const DATA_SKIN_DATA = 'data';

    const DEFAULT_SKIN_ID = 'Standard_Custom';

    const DEFAULT_SKIN_ID_SLIM = 'Standard_CustomSlim';

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

    /**
     * Creates a Skin object from a skin image file (png)
     * @param string $path
     * @param string $skinName The skin id used in the Human's entity
     * @param class-string<SkinImageType>|class-string<SkinImageType>[] $fileTypes
     * @return Skin
     * @throws InvalidArgumentException If the path does not exists or the $fileTypes param is invalid
     */
    public static function syncFromImageFile(
        string $path,
        string $skinName = self::DEFAULT_SKIN_ID,
        $fileTypes = [PngSkinImageType::class]
    ) : Skin
    {
        if (!file_exists($path)) {
            throw new InvalidArgumentException("Path $path does not exists");
        }

        if (is_string($fileTypes)) {
            $imageTypeObject = $fileTypes;
        } else if (is_array($fileTypes)) {
            $imageTypeObject = self::getImageTypeByExtension($path, $fileTypes);
        } else {
            throw new InvalidArgumentException("File type " . gettype($fileTypes) . ' is not valid!');
        }

        $image = $imageTypeObject::createResource($path);

        $maxX = imagesx($image);
        $maxY = imagesy($image);

        $raw = '';

        for ($y = 0; $y < $maxY; $y++) {
            for ($x = 0; $x < $maxX; $x++) {
                $color = imagecolorat($image, $x, $y);
                $a = ($color >> 24) & 0x7F;
                $r = ($color >> 16) & 0xFF;
                $g = ($color >> 8) & 0xFF;
                $b = $color & 0xFF;

                $a = (127 - $a) * 255 / 127;

                $raw .= chr($r);
                $raw .= chr($g);
                $raw .= chr($b);
                $raw .= chr((int) $a);
            }
        }

        return new Skin($skinName, $raw);
    }

    /**
     * @param string $savePath Do not use file extension here!
     * @param class-string<SkinImageType> $imageType
     * @return Promise<boolean>
     */
    public function toImage(string $savePath, string $imageType = PngSkinImageType::class) : Promise
    {
        return CreateSkinImageAsyncTask::create($this, $savePath, $imageType);
    }

    /**
     * Creates a skin image in synchronous mode
     * @param string $savePath Does not use file extension here! Only the file name like: `folder/MySkin` without `.png`
     * @param SkinImageType $type
     * @return void
     * @throws InvalidArgumentException If the skin is invalid
     */
    public function syncToImage(string $savePath, SkinImageType $type) 
    {
        $rawMinecraftSkin = $this->getData();
        $skinLength = strlen($rawMinecraftSkin);
        $x64Length = (64 * 64) * 4;
        $x64x32Length = (64 * 32) * 4;
        if ($skinLength === $x64Length) {
            $maxY = 64;
        } else if ($skinLength === $x64x32Length) {
            $maxY = 32;
        } else {
            throw new InvalidArgumentException("This skin cannot be converted to PNG");
        }

        $maxX = 64;

        $image = imagecreatetruecolor($maxX, $maxY);
        $index = 0;

        $singlePngOpacityPercent = 127 / 100;

        imagealphablending($image, false);
        imagesavealpha($image, true);

        for ($y = 0; $y < $maxY; $y++) {
            for ($x = 0; $x < $maxX; $x++) {
                list($r, $g, $b, $a) = [
                    ord($rawMinecraftSkin[$index++]),
                    ord($rawMinecraftSkin[$index++]),
                    ord($rawMinecraftSkin[$index++]),
                    ord($rawMinecraftSkin[$index++])
                ];
                
                $alphaPercent = ((255 - $a) / 255) * 100;
                $realAlpha = intval($singlePngOpacityPercent * $alphaPercent);
                $color = imagecolorallocatealpha($image, $r, $g, $b, $realAlpha);
                imagesetpixel($image, $x, $y, $color);
            }
        }
        $type::syncSaveSkin($savePath, $image);
    }

    /**
     * @param string $path
     * @param array $fileTypes
     * @return string
     */
    public static function getImageTypeByExtension(string $path, array $fileTypes = [PngSkinImageType::class]) : string
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $extension = strtolower($extension);
        foreach ($fileTypes as $type) {
            foreach ($type::getAvaliableExtensions() as $imgExtensionName) {
                if (strtolower($imgExtensionName) === $extension) {
                    return $type;
                }
            }
        }
        throw new InvalidArgumentException("Path $path has no valid file type");
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