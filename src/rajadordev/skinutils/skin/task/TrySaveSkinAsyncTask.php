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

use rajadordev\skinutils\skin\holder\PlayerSkinHolder;
use rajadordev\skinutils\skin\Skin;
use rajadordev\skinutils\utils\async\AsyncPromiseTask;
use rajadordev\skinutils\utils\promise\Promise;

class TrySaveSkinAsyncTask extends AsyncPromiseTask
{

    /** @var string */
    protected $path;

    /** @var string */
    protected $skinArraySerialized;

    /**
     * @param string $path
     * @param PlayerSkinHolder $skin
     * @return Promise<bool>
     */
    public static function save(string $path, PlayerSkinHolder $skin) : Promise
    {
        $task = new self($path, $skin);
        self::schedule($task);
        return $task->getPromise();
    }

    public function __construct(
        string $path,
        PlayerSkinHolder $skin
    )
    {
        $this->path = $path;
        $this->skinArraySerialized = serialize([
            'username' => $skin->getUsername(),
            'skinId' => $skin->getSkin()->getSkinId(),
            'skinData' => $skin->getSkin()->getData()
        ]);
        return parent::__construct([], false);
    }

    protected function processAndSerializeResult(array $safeVarValues)
    {
        $path = $this->path;
        $skinArray = unserialize($this->skinArraySerialized);
        $skin = new PlayerSkinHolder($skinArray['username'], new Skin($skinArray['skinId'], $skinArray['skinData']));
        if (file_exists($path)) {
            $currentSkinData = file_get_contents($path);
            $currentSkinData = json_decode($currentSkinData, true);
            /** @var PLayerSkinHolder */
            $currentSkinData = $currentSkinData[PlayerSkinHolder::DATA_SKIN];
            $skinId = $currentSkinData[Skin::DATA_SKIN_ID];
            $skinData = base64_decode($currentSkinData[Skin::DATA_SKIN_DATA]);
            if ($skinId == $skin->getSkin()->getSkinId() && $skinData == $skin->getSkin()->getData()) {
                return false;
            }
        }
        $skin->save($path);
        return true;
    }

}