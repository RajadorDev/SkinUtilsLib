<?php

declare(strict_types=1);

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

namespace rajadordev\skinutils\skin\task;

use InvalidArgumentException;
use rajadordev\skinutils\skin\img\SkinImageType;
use rajadordev\skinutils\skin\Skin;
use rajadordev\skinutils\utils\async\AsyncPromiseTask;
use rajadordev\skinutils\utils\DynamicObject;
use rajadordev\skinutils\utils\promise\Promise;

class CreateSkinImageAsyncTask extends AsyncPromiseTask
{

    /**
     * @param Skin $skin
     * @param string $targetFilePath
     * @param class-string<SkinImageType> $imageType
     * @return Promise<bool>
     */
    public static function create(Skin $skin, string $targetFilePath, string $imageType) : Promise
    {
        $task = new self($skin, $targetFilePath, $imageType);
        self::schedule($task);
        return $task->getPromise();
    }

    /**
     * @param Skin $skin
     * @param string $targetFilePath
     * @param class-string<SkinImageType> $imageType
     */
    public function __construct(Skin $skin, string $targetFilePath, string $imageType)
    {
        if (!(is_a($imageType, SkinImageType::class, true))) {
            throw new InvalidArgumentException("Image type $imageType is not valid");
        }
        parent::__construct(
            [
                'skin' => $skin->jsonSerialize(),
                'targetPath' => $targetFilePath,
                'imgType' => $imageType
            ]
        );
    }

    protected function processAndSerializeResult(array $safeVarValues)
    {
        /** @var Skin */
        $skin = DynamicObject::globalUnserialize($safeVarValues['skin']);
        /** @var class-string<SkinImageType> $imageType */
        $imageType = $safeVarValues['imgType'];
        try {
            $skin->syncToImage($safeVarValues['targetPath'], new $imageType);
        } catch (InvalidArgumentException $error) {
            return false;
        }
        return true;
    }
}