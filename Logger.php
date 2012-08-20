<?php

namespace Nelmio\JsLoggerBundle;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

class Logger
{
    private $logger;
    private $allowedLevels;

    private $levelToMethod = array(
        'emergency' => 'emerg',
        'alert' => 'alert',
        'critical' => 'crit',
        'error' => 'err',
        'warning' => 'warn',
        'notice' => 'notice',
        'info' => 'info',
        'debug' => 'debug',
    );

    public function __construct(LoggerInterface $logger, array $allowedLevels)
    {
        $this->logger = $logger;
        $this->allowedLevels = $allowedLevels;
    }

    public function write($level, $message, array $context = array())
    {
        if (!in_array(strtolower($level), $this->allowedLevels)) {
            return false;
        }
        if (!$message) {
            return false;
        }

        $this->logger->{$this->levelToMethod[$level]}($message, $context);

        return true;
    }
}
