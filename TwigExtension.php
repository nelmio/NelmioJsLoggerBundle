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

    window.onerror = function(errorMsg, file, line) {
        var key,
            e = encodeURIComponent,
            customContext = window.nelmio_js_logger_custom_context,
            customContextStr = '';

        if (oldErrorHandler) {
            oldErrorHandler(errorMsg, file, line);
        }

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
