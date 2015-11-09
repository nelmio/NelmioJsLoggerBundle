<?php

namespace Nelmio\JsLoggerBundle;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TwigExtension extends \Twig_Extension
{
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('nelmio_js_error_logger', array($this, 'initErrorLogger'), array('is_safe' => array('html', 'js'))),
            new \Twig_SimpleFunction('nelmio_js_logger', array($this, 'initLogger'), array('is_safe' => array('html', 'js'))),
        );
    }

    public function initErrorLogger($level = 'error', $includeScriptTag = true)
    {
        $url = addslashes($this->router->generate('nelmio_js_logger_log'));

        $js = <<<JS
(function () {
    var oldErrorHandler = window.onerror;

    window.onerror = function(errorMsg, file, line, col, error) {
        var key,
            e = encodeURIComponent,
            customContext = window.nelmio_js_logger_custom_context,
            customContextStr = '';

        if (oldErrorHandler) {
            oldErrorHandler(errorMsg, file, line, col, error);
        }

        if( typeof StackTrace !== 'undefined' && typeof StackTrace.fromError === 'function'){
            window.logWithStacktrace(errorMsg, file, line, col, error);
        } else {
            window.logWithoutStacktrace(errorMsg, file, line, col, error);
        }
    };

    window.logWithStacktrace = function(errorMsg, file, line, col, error){
        StackTrace.fromError(error).then(
            function(stackframes){
                    var req = new XMLHttpRequest();
                    req.onerror = function(err){
                                        if(typeof console !== 'undefined' && typeof console.log === 'function'){
                                            console.log('An error occurred while trying to log an error using stacktrace.js!');
                                        }
                                        throw new Error('POST to $url failed.');
                                    };
                    req.onreadystatechange = function onreadystatechange() {
                        if (req.readyState === 4) {
                            if (req.status >= 200 && req.status < 400) {
                                if(typeof console !== 'undefined' && typeof console.log === 'function'){
                                    console.log('Error logged successfully to $url.');
                                }
                            } else {
                                if(typeof console !== 'undefined' && typeof console.log === 'function'){
                                    console.log('POST to $url failed with status: ' + req.status);
                                }
                                throw new Error('POST to $url failed with status: ' + req.status);
                            }
                        }
                    };
                    req.open('post', '$url');
                    req.setRequestHeader('Content-Type', 'application/json');
                    req.send(JSON.stringify({stack: stackframes, msg: errorMsg, level: '$level', context: {file, line, col}}));
            }).catch(function(err){
                if(typeof console !== 'undefined' && typeof console.log === 'function'){
                    console.log('An error occurred while trying to log an error using stacktrace.js!');
                }
                window.logWithoutStacktrace('An error occurred while trying to log an error using stacktrace.js!', err.fileName, err.lineNumber, err.columnNumber, err);
                window.logWithoutStacktrace(errorMsg, file, line, col, error);
            });
    };

    window.logWithoutStacktrace = function(errorMsg, file, line, col, error){
       if ('object' === typeof customContext) {
            for (key in customContext) {
                customContextStr += '&context[' + e(key) + ']=' + e(customContext[key]);
            }
        }

        (new Image()).src = '$url?msg=' + e(errorMsg) +
            '&level=$level' +
            '&context[file]=' + e(file) +
            '&context[line]=' + e(line) +
            '&context[browser]=' + e(navigator.userAgent) +
            '&context[page]=' + e(document.location.href) + customContextStr;
    };
})();
JS;
        $js = preg_replace('{\n *}', '', $js);

        if ($includeScriptTag) {
            $js = "<script>$js</script>";
        }

        return $js;
    }

    public function initLogger($function = 'log', $includeScriptTag = true)
    {
        $url = addslashes($this->router->generate('nelmio_js_logger_log'));

        $js = <<<JS
var $function = function(level, message, contextData) {
    var key,
        context = '',
        customContext = window.nelmio_js_logger_custom_context,
        e = encodeURIComponent;

    if (contextData) {
        for (key in contextData) {
            context += '&context[' + e(key) + ']=' + e(contextData[key]);
        }
    }
    if ('object' === typeof customContext) {
        for (key in customContext) {
            context += '&context[' + e(key) + ']=' + e(customContext[key]);
        }
    }
    (new Image()).src = '$url?msg=' + e(message) + '&level=' + e(level) + context;
};
JS;

        $js = preg_replace('{\n *}', '', $js);

        if ($includeScriptTag) {
            $js = "<script>$js</script>";
        }

        return $js;
    }

    public function getName()
    {
        return 'nelmio_js_logger';
    }
}
