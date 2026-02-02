<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Nelmio\JsLoggerBundle\Controller\LogController;
use Nelmio\JsLoggerBundle\Logger;
use Nelmio\JsLoggerBundle\TwigExtension;

return static function (ContainerConfigurator $container) {
    $container->parameters()
        ->set('nelmio_js_logger.logger.class', Logger::class)
        ->set('nelmio_js_logger.twig_extension.class', TwigExtension::class);

    $container->services()
        ->set(LogController::class, LogController::class)
        ->public()
        ->args([service('nelmio_js_logger.logger')]);

    $container->services()
        ->set('nelmio_js_logger.logger', '%nelmio_js_logger.logger.class%')
        ->public()
        ->args([
            service('logger'),
            param('nelmio_js_logger.allowed_levels'),
            param('nelmio_js_logger.ignore_messages'),
            param('nelmio_js_logger.ignore_url_prefixes'),
        ])
        ->tag('monolog.logger', ['channel' => 'frontend']);

    $container->services()
        ->set('nelmio_js_logger.twig_extension', '%nelmio_js_logger.twig_extension.class%')
        ->args([
            service('router'),
            param('nelmio_js_logger.stacktrace_js_path'),
        ])
        ->tag('twig.extension');
};
