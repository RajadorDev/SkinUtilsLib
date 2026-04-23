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

use pocketmine\Server;
use rajadordev\skinutils\skin\holder\PlayerSkinHolder;
use rajadordev\skinutils\skin\save\OfflinePlayersSkinsSave;
use rajadordev\skinutils\skin\Skin;
use rajadordev\skinutils\utils\async\AsyncPromiseTask;
use rajadordev\skinutils\utils\promise\Promise;

class OfflineSkinAsyncTask extends AsyncPromiseTask
{

    /** @var string */
    protected $path;

    public static function fetch(string $path) : Promise
    {
        $task = new self($path);
        self::schedule($task);
        return $task->getPromise();
    }

    public function __construct(
        string $path
    )
    {
        $this->path = $path;
        return parent::__construct([], false);
    }

    protected function processAndSerializeResult(array $safeVarValues)
    {
        return serialize(
            OfflinePlayersSkinsSave::syncOfflineSkinData(
                $this->path
            )
        );
    }

    public function onCompletion(Server $server)
    {
        $result = unserialize($this->getResult());
        $this->getResolver()->resolve(
            new PlayerSkinHolder(
                $result['username'],
                new Skin(
                    $result['skinId'],
                    $result['skinData']
                )
            )
        );
    }
}