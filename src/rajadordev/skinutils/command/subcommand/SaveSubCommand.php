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

namespace rajadordev\skinutils\command\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use rajadordev\skinutils\skin\holder\PlayerSkinHolder;
use rajadordev\skinutils\skin\img\PngSkinImageType;
use rajadordev\skinutils\skin\img\SkinImageType;
use rajadordev\skinutils\skin\save\OfflinePlayersSkinsSave;
use rajadordev\skinutils\skin\Skin;
use rajadordev\skinutils\SkinUtilsLoader;
use rajadordev\skinutils\utils\Performance;
use rajadordev\skinutils\utils\SystemUtils;
use SmartCommand\command\argument\StringArgument;
use SmartCommand\command\argument\StringListArgument;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\subcommand\BaseSubCommand;

class SaveSubCommand extends BaseSubCommand
{

    /** @var array<string,class-string<SkinImageType>> */
    protected $imageTypes = [];

    protected static function getRuntimePermission(): string
    {
        return 'skinutils.command.save';
    }

    protected function prepare()
    {
        foreach ([PngSkinImageType::class] as $classType) {
            $this->imageTypes[strtolower($classType::getName())] = $classType;
        }
        $this->registerArguments(
            [
                new StringArgument('username', false),
                new StringListArgument('type', array_keys($this->imageTypes), false)
            ]
        );
    }

    protected function onRun(CommandSender $sender, string $commandLabel, string $subcommandLabel, CommandArguments $args)
    {
        if ($args->has('username')) {
            $name = $args->getString('username');
        } else if ($sender instanceof Player) {
            $name = $sender->getName();
        } else {
            $this->sendUsage($sender, $commandLabel, $subcommandLabel);
            return;
        }

        $sender->sendMessage(
            SkinUtilsLoader::PREFIX . "Searching for $name's skin..."
        );

        $senderWasPlayer = $sender instanceof Player;

        OfflinePlayersSkinsSave::getInstance()->getSkin($name)->then(
            function ($skin) use ($args, $sender, $senderWasPlayer, $name) {

                if ($senderWasPlayer && !SystemUtils::isValisPlayer($sender)) {
                    return;
                }

                if ($skin instanceof PlayerSkinHolder) {
                    $skin = $skin->getSkin();
                    if ($args->has('type')) {
                        $imageType = $this->imageTypes[$args->getString('type')];
                    } else {
                        $imageType = PngSkinImageType::class;
                    }
                    $typeName = $imageType::getName();
                    $sender->sendMessage(SkinUtilsLoader::PREFIX . "Creating §b$name's §7skin §b{$typeName}§7....");
                    $startted = Performance::start();
                    $skinPath = $skinPath = SkinUtilsLoader::getInstance()->getSkinsImageFolder() . strtolower($name) . '_skin';
                    $skin->toImage(
                        $skinPath,
                        $imageType
                    )->then(
                        function (bool $result) use ($sender, $senderWasPlayer, $skin, $startted, $typeName, $imageType, $name, $skinPath) {
                            if ($senderWasPlayer && !SystemUtils::isValisPlayer($sender)) {
                                return;
                            }

                            if ($result === false) {
                                $sender->sendMessage(SkinUtilsLoader::PREFIX . "§cCan't create a §f$typeName §7for §f$name's§c skin! Invalid skin.");
                                return;
                            }
                            
                            $finishedAt = $startted->finish()->getFormattedResult();

                            $skinPath = str_replace('\\', '/', $skinPath);

                            $sender->sendMessage(SkinUtilsLoader::PREFIX . "Skin was saved at §6§f{$skinPath}§7 in §b{$finishedAt}§7!");
                        }
                    )->catch(
                        function () use ($sender, $senderWasPlayer, $name) {
                            if ($senderWasPlayer && !SystemUtils::isValisPlayer($sender)) {
                                return;
                            }

                            $sender->sendMessage(SkinUtilsLoader::PREFIX . "§cAn error ocurred while §eSkinUtilsLib §cwas saving §f$name's §cskin. Try again later.");
                        }
                    );
                } else {
                    $sender->sendMessage(SkinUtilsLoader::PREFIX . "Skin from §f\"§c{$name}§f\"§7 does §cnot §7found!");
                }
            }
        )->catch(
            function () use ($sender, $senderWasPlayer, $name) {
                if ($senderWasPlayer && !SystemUtils::isValisPlayer($sender)) {
                    return;
                }

                $sender->sendMessage(SkinUtilsLoader::PREFIX . "§cAn error ocurred while §eSkinUtilsLib §cwas searching for §f$name's §cskin. Try again later.");
            }
        );
    }
}