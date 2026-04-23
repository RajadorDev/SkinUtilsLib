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

namespace rajadordev\skinutils\skin\save;

use InvalidArgumentException;
use rajadordev\skinutils\SkinUtilsLoader;
use rajadordev\skinutils\utils\promise\Promise;
use rajadordev\skinutils\utils\promise\PromiseResolver;
use SmartCommand\utils\SingletonTrait;
use rajadordev\skinutils\skin\holder\PlayerSkinHolder;
use rajadordev\skinutils\skin\Skin;
use rajadordev\skinutils\skin\task\OfflineSkinAsyncTask;
use rajadordev\skinutils\skin\task\TrySaveSkinAsyncTask;

class OfflinePlayersSkinsSave
{

    use SingletonTrait;

    /** @var array<string,PlayerSkinHolder> */
    protected $skinsCache = [];

    /** @var array<string,Promise> */
    protected $waitingToFind = [];

    public static function init()
    {
        self::setInstance(new self);
    }

    /**
     * @param string $username
     * @return Promise<PlayerSkinHolder|null>
     */
    public function getSkin(string $username) : Promise
    {
        $username = strtolower($username);
        if (isset($this->skinsCache[$username])) {
            $resolver = new PromiseResolver;
            $resolver->resolve($this->skinsCache[$username]);
            return $resolver->getPromise();
        }

        if (isset($this->waitingToFind[$username])) {
            return $this->waitingToFind[$username];
        }

        if (file_exists($path = $this->getOfflineSkinPath($username))) {
            $promise = OfflineSkinAsyncTask::fetch($path);
            $promise->then(
                function ($result) use ($username) {
                    unset($this->waitingToFind[$username]);
                    if ($result instanceof PlayerSkinHolder) {
                        $this->addToCache($result);
                    }
                }
            );
            return $this->waitingToFind[$username] = $promise;
        } else {
            $resolver = new PromiseResolver;
            $resolver->resolve(null);
            return $resolver->getPromise();
        }
    }

    public function addToCache(PlayerSkinHolder $skin)
    {
        $username = $skin->getUsername(true);
        if (isset($this->skinsCache[$username])) {
            throw new InvalidArgumentException("Skin $username is already registered in offline cache");
        }
        $this->skinsCache[$username] = $skin;
    }

    public function clearCache(string $username)
    {
        unset($this->skinsCache[strtolower($username)]);
    }

    /**
     * @param PlayerSkinHolder $skin
     * @return Promise<bool>
     */
    public function save(PlayerSkinHolder $skin) : Promise
    {
        $username = $skin->getUsername();
        $promise = TrySaveSkinAsyncTask::save($this->getOfflineSkinPath($username), $skin)->then(
            function (bool $result) use ($username, $skin) {
                if ($result) {
                    $this->clearCache($username);
                    $this->addToCache($skin);
                }
            }
        );
        return $promise;
    }

    public function getOfflineSkinPath(string $username) : string 
    {
        return $this->getOfflineSkinsFolder() . strtolower($username) . '.json';
    }

    public function getOfflineSkinsFolder() : string 
    {
        return SkinUtilsLoader::getInstance()->getDataFolder() . 'skins' . DIRECTORY_SEPARATOR;
    }

    /**
     * It will return array serialized using php's serialize function. But will not return base64 encoded value.
     * @param string $path
     * @return array{username:string,skinId:string,skinData:string}
     */
    public static function syncOfflineSkinData(string $path) : array
    {
        $data = file_get_contents($path);
        $jsonData = json_decode($data, true);
        return [
            'username' => $jsonData[PlayerSkinHolder::DATA_PLAYER_USERNAME],
            'skinData' => base64_decode($jsonData[PlayerSkinHolder::DATA_SKIN][Skin::DATA_SKIN_DATA]),
            'skinId' => $jsonData[PlayerSkinHolder::DATA_SKIN][Skin::DATA_SKIN_ID]
        ];
    }
}