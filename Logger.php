<?php

namespace Nelmio\JsLoggerBundle;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

class Logger
{
    protected $logger;
    protected $allowedLevels;

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

        $this->logger->{'add'.$level}($message, $context);

        return true;
    }
}
