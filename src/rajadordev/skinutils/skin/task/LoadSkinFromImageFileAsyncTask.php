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

namespace rajadordev\skinutils\skin\task;

use InvalidArgumentException;
use rajadordev\skinutils\utils\async\AsyncPromiseTask;
use rajadordev\skinutils\skin\img\SkinImageType;
use rajadordev\skinutils\skin\Skin;
use rajadordev\skinutils\utils\promise\Promise;
use Throwable;

class LoadSkinFromImageFileAsyncTask extends AsyncPromiseTask
{

    /**
     * @param string $filePath
     * @param class-string<SkinImageType> $imageType
     * @param string $skinId
     * @return Promise<Skin|false> False when the file does not exists
     */
    public static function loadFromImage(string $filePath, string $imageType, string $skinId = Skin::DEFAULT_SKIN_ID) : Promise
    {
        $task = new self($filePath, $imageType, $skinId);
        self::schedule($task);
        return $task->getPromise();
    }

    /**
     * @param string $filePath
     * @param class-string<SkinImageType> $imageType
     * @param string $skinId
     */
    public function __construct(
        string $filePath,
        string $imageType,
        string $skinId = Skin::DEFAULT_SKIN_ID
    )
    {
        if (!is_a($imageType, SkinImageType::class, true)) {
            throw new InvalidArgumentException("ImageType $imageType is not valid");
        }
        parent::__construct(
            [
                'filePath' => $filePath,
                'imageType' => $imageType,
                'skinId' => $skinId
            ],
            false
        );
    }

    protected function processAndSerializeResult(array $safeVarValues)
    {
        if (!file_exists($path = $safeVarValues['filePath'])) {
            return false;
        }

        $imageType = $safeVarValues['imageType'];

        $skin = Skin::syncFromImageFile($path, $safeVarValues['skinId'], $imageType);

        return $skin->jsonSerialize();
    }
}