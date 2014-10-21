NelmioJsLoggerBundle
====================

The **NelmioJsLoggerBundle** bundle allows you to log errors happening in the frontend.

## Installation ##

Require the `nelmio/js-logger-bundle` package in your composer.json and update your dependencies.

    $ composer require nelmio/js-logger-bundle

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

The bundle exposes two twig functions that you should put in your site
template somewhere.

To enable automatic logging of javascript errors, use `nelmio_js_error_logger()`:

    {{ nelmio_js_error_logger() }}

You can optionally change the level (default is ERROR) and remove the surrounding
`<script>..</script>` tags - don't forget to add them manually!:

    <script>
        {{ nelmio_js_error_logger('WARNING', false) }}
    </script>

You can also optionally give some extra context information by defining a global
`window.nelmio_js_logger_custom_context` in the page:

    <script>
        window.nelmio_js_logger_custom_context = { userinfo: 'some info', appinfo: 'another useful info' };
        {{ nelmio_js_error_logger('ERROR', false) }}
    </script>

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

You can also restrict the logging by ignoring some messages or scripts URLs
with this configuration:

    # app/config/config.yml
    nelmio_js_logger:
        ignore_messages:
            - originalCreateNotification
        ignore_url_prefixes:
            - https://graph.facebook.com

The URL matches as a prefix to the script URL, and the message will match if
the ignored string is found anywhere in the message.

## Properly tracking scripts in other domains

If an error occurs in a script from another domain, browser same origin policy will
make it to be logged with a generic message, file and line number (like
`Script error. {"file":"","line":"0", ...}`). To properly track these scripts move
them to your domain or [load them using CORS](https://developer.mozilla.org/en-US/docs/HTML/CORS_settings_attributes):

```html
<script src="//code.jquery.com/jquery-1.9.0.min.js" crossorigin></script>
```

Note that [browser support for `<script crossorigin>` varies](http://blog.errorception.com/2012/12/catching-cross-domain-js-errors.html):

> As of this writing, only Firefox supports reporting errors for cross-domain
> scripts. All WebKit browsers including Chrome is expected to support this very
> soon. This isn't a problem with IE at all, since IE already reports errors
> to window.onerror irrespective of the domain (yay, security!).
