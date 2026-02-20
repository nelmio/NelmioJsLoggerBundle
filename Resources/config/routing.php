<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->add('nelmio_js_logger_log', '/log')
        ->controller('Nelmio\JsLoggerBundle\Controller\LogController::createAction')
        ->methods(['GET', 'POST'])
    ;
};
