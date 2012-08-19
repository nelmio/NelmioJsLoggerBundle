<?php

namespace Nelmio\JsLoggerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LogController extends Controller
{
    public function createAction(Request $request)
    {
        $level = $request->query->get('level');
        $message = $request->query->get('msg');
        $context = $request->query->get('context', array());

        if ($this->get('nelmio_js_logger.logger')->write($level, $message, $context)) {
            return new Response('OK', 201);
        }

        return new Response('', 400);
    }
}
