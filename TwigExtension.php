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
            'nelmio_js_error_logger' => new \Twig_Function_Method($this, 'initErrorLogger', array('is_safe' => array('html', 'js'))),
            'nelmio_js_logger' => new \Twig_Function_Method($this, 'initLogger', array('is_safe' => array('html', 'js'))),
        );
    }

    public function initErrorLogger($level = 'error', $includeScriptTag = true)
    {
        $url = addslashes($this->router->generate('nelmio_js_logger_log'));

        $js = <<<JS
window.onerror = function(errorMsg, file, line) {
    var e = encodeURIComponent;
    (new Image()).src = '$url?msg=' + e(errorMsg) +
        '&level=$level' +
        '&context[file]=' + e(file) +
        '&context[line]=' + e(line) +
        '&context[browser]=' + e(navigator.userAgent) +
        '&context[page]=' + e(document.location.href);
};
JS;

        if ($includeScriptTag) {
            $js = "<script>\n$js\n</script>";
        }

        return $js;
    }

    public function initLogger($function = 'log', $includeScriptTag = true)
    {
        $url = addslashes($this->router->generate('nelmio_js_logger_log'));

        $js = <<<JS
var $function = function(level, message, contextData) {
    var context, key, e = encodeURIComponent;
    context = '';
    if (contextData) {
        for (key in contextData) {
            context += '&context[' + key + ']=' + e(contextData[key]);
        }
    }
    (new Image()).src = '$url?msg=' + e(message) + '&level=' + e(level) + context;
};
JS;

        if ($includeScriptTag) {
            $js = "<script>\n$js\n</script>";
        }

        return $js;
    }

    public function getName()
    {
        return 'nelmio_js_logger';
    }
}
