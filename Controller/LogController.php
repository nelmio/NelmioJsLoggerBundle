<?php

namespace Nelmio\JsLoggerBundle\Controller;

use Nelmio\JsLoggerBundle\Logger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LogController
{
    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function createAction(Request $request)
    {
        if(Request::METHOD_GET === $request->getMethod()){
            $level = (string) $request->query->get('level');
            $message = (string) $request->query->get('msg');
            $context = (array) $request->query->all('context', array());

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

        if ($this->logger->write($level, $message, $context)) {
            return new Response(base64_decode('R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs'), 201, array('Content-Type' => 'image/gif'));
        }

        return new Response('', 400);
    }
}
