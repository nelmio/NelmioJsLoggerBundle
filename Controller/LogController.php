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
            return new Response(base64_decode('R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs'), 201, array('Content-Type' => 'image/gif'));
        }

        return new Response('', 400);
    }
}
