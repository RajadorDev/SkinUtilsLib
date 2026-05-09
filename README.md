# SkinUtilsLib ✨

This shared library for `PocketMine 2.0.0` saves player's skin **automatically** in **background** and you can get, use and save when you want.

You also can any player skin as **PNG** file.

## Command 🛠️

- `/skin`: 
  - `info`: Shows info about the library
  - `save <username: string> <fileType: png>`: Saves player's skin

## Dependences 🧱

- `SmartCommand PocketMine 2`: 
  - Here is the latest SmartCommand PM2 version: https://github.com/RajadorDev/SmartCommand/releases/tag/2.3.2

- `AutoPluginUpdater`: 
  - Repo: https://github.com/RajadorDev/AutoPluginUpdater


## Examples 📝

### Get offline player's skin and saving as PNG 🖼️

```php
<?php

use rajadordev\skinutils\skin\img\PngSkinImageType;
use rajadordev\skinutils\skin\save\OfflinePlayersSkinsSave;
use rajadordev\skinutils\skin\holder\PlayerSkinHolder;

OfflinePlayersSkinsSave::getInstance()->getSkin($username)
->then(
    function ($result) {
        if ($result instanceof PlayerSkinHolder) {
            $playerSkin = $result->getSkin();
            /**
             * Here you can get some skin info using:
             * @see Skin::getSkinId()
             * @see Skin::getData()
             * 
             * You can also sets Human's skins using:
             * @see Skin::applyInHuman
             */

            # Saving as PNG (Synchronous)
            $playerSkin->syncToImage(
                'path/where/SkinName', // You don not need to use file extension (like .png)
                new PngSkinImageType
            );

            /**
             * You can also save in asynchronous mode (recomended)
             * This method will return a Promise
             */
            $playerSkin->toImage('path/where/SkinName')->then(
                function (bool $result) {
                    // Do something....
                }
            );
        } else {
            /** Skin not found here... */
        }
    }
);
```

### Loading skin from PNG file 📁
```php
<?php

use rajadordev\skinutils\skin\img\PngSkinImageType;
use rajadordev\skinutils\skin\Skin;
use rajadordev\skinutils\skin\task\LoadSkinFromImageFileAsyncTask;

/**
 * Asynchronous (recomended)
 */
LoadSkinFromImageFileAsyncTask::loadFromImage(
    'path/where/MySkinInPng.png', # Here you need to add the file extension
    PngSkinImageType::class
)->then(
    function ($result) {
        /** @var Skin|false $result */
        if ($result instanceof Skin) {
            # Do something, like apply in some Human/Player
        } else {
            # File don't found
        }
    }
)->catch(
    function () {
        # Some error ocurred, maybe the file given is not a PNG
    }
);

/** Synchronous, it can use a lot of TPS */
$skin = Skin::syncFromImageFile('path/where/MySkinInPng.png');

# ... So you can apply in a Human/Player
```

### Using default skins 📚
```php
<?php

use rajadordev\skinutils\skin\save\DefaultMinecraftSkins;

# Apply default Steve minecraft skin in a Human
DefaultMinecraftSkins::STEVE()->applyInHuman($human);

# Apply default Alex minecraft skin in a Human
DefaultMinecraftSkins::ALEX()->applyInHuman($human);
```

### Skin handler 📡
You can also create a handler and receive the current and the new skin (if the player change his own skin) using a SkinHandler:
```php
<?php

use rajadordev\skinutils\skin\handler\SkinHandler;
use rajadordev\skinutils\skin\handler\SkinHandlerTrait;
use rajadordev\skinutils\skin\holder\PlayerSkinHolder;

class MySkinHandler implements SkinHandler
{

    use SkinHandlerTrait;

    public function __construct(
        string $playerUsername
    )
    {
        # Use this method to set target player name
        $this->setTargetSkinUsername($playerUsername);
    }

    protected function onCurrentTargetSkinChange($skin)
    {
        if ($skin instanceof PlayerSkinHolder) {
            # Do something.....
            # Here i'm saving the skin as PNG 
            $skin->getSkin()->toImage('where/path/skin');
        }
    }

}
```