<?php

namespace Nelmio\JsLoggerBundle;

interface SanitizerInterface
{
    /**
     * @param string $value
     * @return string
     */
    public function sanitize($value);
}
