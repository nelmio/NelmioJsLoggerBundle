<?php

namespace Nelmio\JsLoggerBundle;

use Psr\Log\LoggerInterface;

class Logger
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SanitizerInterface
     */
    private $sanitizer;

    /**
     * @var array
     */
    private $allowedLevels;

    /**
     * @var array
     */
    private $levelToMethod = array(
        'emergency' => 'emergency',
        'alert' => 'alert',
        'critical' => 'critical',
        'error' => 'error',
        'warning' => 'warning',
        'notice' => 'notice',
        'info' => 'info',
        'debug' => 'debug',
    );

    /**
     * @var array
     */
    private $ignoredMessages;

    /**
     * @var array
     */
    private $ignoredURLs;

    /**
     * Constructor
     *
     * @param LoggerInterface    $logger
     * @param SanitizerInterface $sanitizer
     * @param array              $allowedLevels
     * @param array              $ignoredMessages
     * @param array              $ignoredURLs
     */
    public function __construct(
        LoggerInterface $logger,
        SanitizerInterface $sanitizer,
        array $allowedLevels,
        array $ignoredMessages = array(),
        array $ignoredURLs = array()
    ) {
        $this->logger = $logger;
        $this->sanitizer = $sanitizer;
        $this->allowedLevels = $allowedLevels;
        $this->ignoredMessages = $ignoredMessages;
        $this->ignoredURLs = $ignoredURLs;
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

        if (!empty($context['file'])) {
            foreach ($this->ignoredURLs as $scriptToIgnore) {
                if (strpos($context['file'], $scriptToIgnore) === 0) {
                    return false;
                }
            }
        }

        foreach ($this->ignoredMessages as $errorToIgnore) {
            if (false !== strpos($message, $errorToIgnore)) {
                return false;
            }
        }

        foreach ($context as $key => $value) {
            $context[$key] = $this->sanitizer->sanitize($value);
        }

        $this->logger->{$this->levelToMethod[$level]}($this->sanitizer->sanitize($message), $context);

        return true;
    }
}
