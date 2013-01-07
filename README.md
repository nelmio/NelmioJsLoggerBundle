NelmioJsLoggerBundle
====================

The **NelmioJsLoggerBundle** bundle allows you to log errors happening in the frontend.

## Installation ##

Add this bundle to your `composer.json` file:

    {
        "require": {
            "nelmio/js-logger-bundle": "~1.0"
        }
    }

Register the bundle in `app/AppKernel.php`:

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Nelmio\JsLoggerBundle\NelmioJsLoggerBundle(),
        );
    }

Import the bundle's routing definition in `app/config/routing.yml`:

    # app/config/routing.yml
    NelmioJsLoggerBundle:
        resource: "@NelmioJsLoggerBundle/Resources/config/routing.xml"
        prefix:   /nelmio-js-logger

## Automated Error Logging ##

The bundle exposes two twig functions that you should put in your site template somewhere.

To enable automatic logging of javascript errors, use `nelmio_js_error_logger()`:

    {{ nelmio_js_error_logger() }}

You can optionally change the level (default is ERROR) and remove the surrounding `<script>..</script>` tags:

    {{ nelmio_js_error_logger('WARNING', false) }}

## Manual Logging from JavaScript ##

To expose the `log()` function to your JS code, use `nelmio_js_logger()`:

    {{ nelmio_js_logger() }}

You can also change the function name if `log` is too generic for you:

    {{ nelmio_js_logger('my_log_function') }}

The function signature is as such: `log(level, message, context)`. The level and
message are mandatory. The context is a data object that can contain any additional
details you want to store.

## Configuration ##

You can restrict the logging levels accessible from javascript. The point
is that if some of your logging levels email you or notify you in some way,
you probably do not want to allow anyone to send requests and wake you up
at 2AM.

Here is the default configuration that exposes all levels:

    # app/config/config.yml
    nelmio_js_logger:
        allowed_levels: ['DEBUG', 'INFO', 'NOTICE', 'WARNING', 'ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY']
