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

namespace rajadordev\skinutils\command;

use pocketmine\command\CommandSender;
use rajadordev\skinutils\command\subcommand\InfoSubCommand;
use rajadordev\skinutils\command\subcommand\SaveSubCommand;
use rajadordev\skinutils\SkinUtilsLoader;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\SmartCommand;

class SkinCommand extends SmartCommand
{

    use SkinCommandPermissionTrait;

    protected function prepare()
    {
        $this->setPrefix(SkinUtilsLoader::PREFIX);
        $this->registerSubCommands([
            new SaveSubCommand($this, 'save', 'Save some player\'s skin', ['salvar', 'salva']),
            new InfoSubCommand($this, 'info', 'Info about this library', ['i', 'information', 'about', 'version', 'v'])
        ]);
    }

    protected function onRun(CommandSender $sender, string $label, CommandArguments $args)
    {
        $this->sendUsage($sender, $label);
    }
}