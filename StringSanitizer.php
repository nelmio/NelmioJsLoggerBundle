<?php

namespace Nelmio\JsLoggerBundle;

class StringSanitizer implements SanitizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function sanitize($value)
    {
        return filter_var($value, FILTER_SANITIZE_STRING);
    }
}
