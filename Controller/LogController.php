<?php

namespace Nelmio\JsLoggerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LogController extends Controller
{
    public function createAction(Request $request)
    {
        if(Request::METHOD_GET === $request->getMethod()){
            $level = (string) $request->query->get('level');
            $message = (string) $request->query->get('msg');
            $context = (array) $request->query->get('context', array());

        } else {
            $postData = json_decode($request->getContent());
            $level = (string) $postData->level;
            $message = (string) $postData->msg;
            $context = (array) $postData->context;
            $stacktrace = "";
            foreach ($postData->stack as $stackframe) {
                $fileName = property_exists($stackframe, 'fileName') ? $stackframe->fileName : "-";
                $lineNumber = property_exists($stackframe, 'lineNumber') ? $stackframe->lineNumber : "-";
                $function = property_exists($stackframe, 'functionName') ? $stackframe->functionName : "-";
                $columnNumber = property_exists($stackframe, 'columnNumber') ? $stackframe->columnNumber : "-";
                $stacktrace .= "\n$fileName : $function : $lineNumber : $columnNumber";
            }
            $context['stacktrace'] = $stacktrace;
        }

        if ($this->get('nelmio_js_logger.logger')->write($level, $message, $context)) {
            return new Response(base64_decode('R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs'), 201, array('Content-Type' => 'image/gif'));
        }

        return new Response('', 400);
    }
}
