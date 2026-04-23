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

namespace rajadordev\skinutils\utils;

use RuntimeException;

class Performance 
{

    /** @var float */
    protected $startedAt;

    /** @var float */
    protected $result;

    public function __construct(float $startedAt)
    {
        $this->startedAt = $startedAt;
    }

    public static function start() : Performance
    {
        return new self(microtime(true));
    }

    public function finish() : Performance
    {
        $this->result = microtime(true) - $this->startedAt;
        return $this;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function getMileseconds() : float
    {
        $this->checkResult();
        return $this->getResult() * 1000;
    }

    public function getMilesecondsFormatted() : string 
    {
        return number_format($this->getMileseconds(), 2) . 'ms';
    }

    public function getSecondsFormatted() : string 
    {
        return number_format($this->getResult(), 2) . 's';
    }

    public function getFormattedResult() : string 
    {
        $result = $this->getResult();
        if ($result >= 1) {
            return $this->getSecondsFormatted();
        }
        return $this->getMilesecondsFormatted();
    }

    protected function checkResult()
    {
        if (!isset($this->result)) {
            throw new RuntimeException("Performance without result");
        }
    }

}