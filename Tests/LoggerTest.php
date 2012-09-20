<?php

namespace Nelmio\JsLoggerBundle\Tests;

use Nelmio\JsLoggerBundle\Logger;

class LoggerTest extends \PHPUnit_Framework_TestCase
{
    public function provideMethodMappings()
    {
        return array(
            array('emergency', 'emerg'),
            array('alert', 'alert'),
            array('critical', 'crit'),
            array('error', 'err'),
            array('warning', 'warn'),
            array('notice', 'notice'),
            array('info', 'info'),
            array('debug', 'debug'),

            // also check uppercase works
            array('WARNING', 'warn'),
        );
    }

    /**
     * @dataProvider provideMethodMappings
     */
    public function testMethodMapping($level, $expectedMethod)
    {
        $mock = $this->getMock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        $mock
            ->expects($this->once())
            ->method($expectedMethod)
        ;

        $logger = new Logger($mock, array('debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'));
        $logger->write($level, 'Message');
    }
}
