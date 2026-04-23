# SkinUtilsLib 🧍

This shared library for `PocketMine 2.0.0` saves player's skin **automatically** in **background** and you can get, use and save when you want.

## Dependences 🧱

- `SmartCommand PocketMine 2`: 
  - Here is the latest SmartCommand PM2 version: https://github.com/RajadorDev/SmartCommand 

- `AutoPluginUpdater`: 
  - Repo: https://github.com/RajadorDev/AutoPluginUpdater


## Examples 📝

## Get offline player's skin:

```php
<?php

use rajadordev\skinutils\skin\holder\PlayerSkinHolder;
use rajadordev\skinutils\skin\save\OfflinePlayersSkinsSave;
use rajadordev\skinutils\skin\Skin;

$username = 'Rajador';
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
        } else {
            /** Skin not found here... */
        }
    }
);
```