<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->add('nelmio_js_logger_log', '/log')
        ->controller('nelmio_js_logger.controller.log::createAction')
        ->methods(['GET', 'POST'])
    ;
};
