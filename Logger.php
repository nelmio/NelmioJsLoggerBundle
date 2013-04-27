<?php

namespace Nelmio\JsLoggerBundle;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

class Logger
{
    /**
     * @var \Symfony\Component\HttpKernel\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $allowedLevels;

    /**
     * @var array
     */
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

    /**
     * @var array
     */
    private $errorsToIgnore;

    /**
     * @var array
     */
    private $scriptsToIgnore;

    /**
     * Constructor
     * 
     * @param \Symfony\Component\HttpKernel\Log\LoggerInterface $logger
     * @param array                                             $allowedLevels
     * @param array                                             $errorsToIgnore
     * @param array                                             $scriptsToIgnore
     */
    public function __construct(LoggerInterface $logger, array $allowedLevels, array $errorsToIgnore, array $scriptsToIgnore)
    {
        $this->logger          = $logger;
        $this->allowedLevels   = $allowedLevels;
        $this->errorsToIgnore  = $errorsToIgnore;
        $this->scriptsToIgnore = $scriptsToIgnore;
    }

    public function write($level, $message, array $context = array())
    {
        if (!$message) {
            return false;
        }

        $level = strtolower($level);
        if (!in_array($level, $this->allowedLevels)) {
            return false;
        }

        foreach ($this->scriptsToIgnore as $scriptToIgnore) {
            if (strpos($context['file'], $scriptToIgnore) === 0) {
                return false;
            }
        }

        foreach ($this->errorsToIgnore as $errorToIgnore) {
            if (false !== strpos($message, $errorToIgnore)) {
                return false;
            }
        }

        $this->logger->{$this->levelToMethod[$level]}($message, $context);

        return true;
    }
}
