<?php

namespace Nelmio\JsLoggerBundle\Tests;

use Nelmio\JsLoggerBundle\Logger;

class LoggerTest extends \PHPUnit_Framework_TestCase
{
    public function provideMethodMappings()
    {
        return array(
            array('emergency', 'emergency'),
            array('alert', 'alert'),
            array('critical', 'critical'),
            array('error', 'error'),
            array('warning', 'warning'),
            array('notice', 'notice'),
            array('info', 'info'),
            array('debug', 'debug'),

            // also check uppercase works
            array('WARNING', 'warning'),
        );
    }

    /**
     * @dataProvider provideMethodMappings
     */
    public function testMethodMapping($level, $expectedMethod)
    {
        $mock = $this->getMock('Psr\Log\LoggerInterface');
        $mock
            ->expects($this->once())
            ->method($expectedMethod)
        ;

        $logger = new Logger($mock, array('debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'));
        $logger->write($level, 'Message');
    }
}
