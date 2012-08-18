<?php

namespace Nelmio\JsLoggerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LogController extends Controller
{
    public function createAction(Request $request)
    {
        if ($this->get('nelmio_js_logger.logger')->handle($request)) {
            return new Response('OK', 201);
        }

        return new Response('', 400);
    }
}
