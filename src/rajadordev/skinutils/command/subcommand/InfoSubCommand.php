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
use rajadordev\skinutils\command\SkinCommandPermissionTrait;
use rajadordev\skinutils\SkinUtilsLoader;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\rule\defaults\CooldownRule;
use SmartCommand\command\subcommand\BaseSubCommand;
use SmartCommand\utils\CommandUtils;

class InfoSubCommand extends BaseSubCommand
{

    use SkinCommandPermissionTrait;

    protected function prepare()
    {
        $this->registerRule(new CooldownRule(800));
    }

    protected function onRun(CommandSender $sender, string $commandLabel, string $subcommandLabel, CommandArguments $args)
    {
        $pluginDescription = SkinUtilsLoader::getInstance()->getDescription();
        $sender->sendMessage(
            CommandUtils::textLinesWithPrefix([
                '',
                '§8----===(§eSkinUtils§8)===----',
                '',
                'Version: §b' . $pluginDescription->getVersion(),
                '',
                'Created by: §bRajador',
                '',
                '§7Discord: §9rajadortv',
                '',
                'Repository: §ahttps://github.com/RajadorDev/SkinUtilsLib',
                ''
            ])
        );
    }
}