<?php

namespace Nelmio\JsLoggerBundle;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TwigExtension extends \Twig_Extension
{
    private $router;

    private $stackTracePath;

    public function __construct(UrlGeneratorInterface $router, $stackTracePath = null)
    {
        $this->router = $router;
        $this->stackTracePath = $stackTracePath;
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

        $addStackTraceJs = <<<JS
var stackTraceJsModule = (function (basicModule) {
    var stackTraceJs = {};

    stackTraceJs.isScriptPresent = function isScriptPresent() {
        return ((typeof StackTrace !== 'undefined') && (typeof StackTrace.fromError === 'function'));
    };
        
    stackTraceJs.sendLogData = function sendLogData(errorMsg, file, line, col, error) {
        StackTrace.fromError(error).then(
            function(stackframes) {
                var req = new XMLHttpRequest();
                    req.onerror = function(err) {
                        if (typeof console !== 'undefined' && typeof console.log === 'function') {
                            console.log('An error occurred while trying to log an error using stacktrace.js!');
                        }
                    };
                    req.onreadystatechange = function onreadystatechange() {
                        if (req.readyState === 4) {
                            if (req.status >= 200 && req.status < 400) {
                                if (typeof console !== 'undefined' && typeof console.log === 'function') {
                                    console.log('Error logged successfully to $url.');
                                }
                            } else {
                                if (typeof console !== 'undefined' && typeof console.log === 'function') {
                                    console.log('POST to $url failed with status: ' + req.status);
                                }
                            }
                        }
                    };
                    req.open('post', '$url');
                    req.setRequestHeader('Content-Type', 'application/json');
                    req.send(JSON.stringify(
                        {
                            stack: stackframes, 
                            msg: errorMsg, 
                            level: '$level', 
                            context: {
                                file: file, 
                                line: line, 
                                column: col, 
                                userAgent: navigator.userAgent, 
                                platform: navigator.platform,
                                customContext: basicModule.fetchCustomContext()
                            }
                        }
                    ));
            }).catch(function(err) {
                if (typeof console !== 'undefined' && typeof console.log === 'function') {
                    console.log('An error occurred while trying to log an error using stacktrace.js!');
                }
                basicModule.callSimpleLogger('An error occurred while trying to log an error using stacktrace.js!', err.fileName, err.lineNumber, err.columnNumber, err);
                basicModule.callSimpleLogger(errorMsg, file, line, col, error);
            });
    };
    
    
    stackTraceJs.callStackTraceJs = function callStackTraceJs(errorMsg, file, line, col, error) {
        if (stackTraceJs.isScriptPresent()) {
            stackTraceJs.sendLogData(errorMsg, file, line, col, error);
            return;
        }

        var script = document.createElement('script');
        script.src = '$this->stackTracePath';
        document.documentElement.appendChild(script);

        script.onload = function() {
            if (stackTraceJs.isScriptPresent()) {
                if (!this.executed) {
                    this.executed = true;
                    stackTraceJs.sendLogData(errorMsg, file, line, col, error);
                } 
            } else {
                console.log(script);
                this.onerror();
            }
        };
    
        script.onerror = function() {
            console.log("StackTrace loading has failed, call default logger");
            basicModule.callSimpleLogger(errorMsg, file, line, col, error)
        };

        script.onreadystatechange = function() {
          var self = this;
          if (this.readyState == "complete" || this.readyState == "loaded") {
            setTimeout(function() {
              self.onload()
            }, 0);
          }
        };
    };

    return stackTraceJs;

}(basicModule));
JS;

        $js = <<<JS
var basicModule = (function () {
    var basic = {};
    var oldErrorHandler = window.onerror;

    window.onerror = function(errorMsg, file, line, col, error) {
        if (oldErrorHandler) {
            oldErrorHandler(errorMsg, file, line, col, error);
        }
        
        if (typeof stackTraceJsModule !== 'undefined') {
            stackTraceJsModule.callStackTraceJs(errorMsg, file, line, col, error);
            return;
        }
        
        basic.callSimpleLogger(errorMsg, file, line, col, error);
    };
    
    basic.callSimpleLogger = function callSimpleLogger(errorMsg, file, line, col, error) {
     var e = encodeURIComponent;

    (new Image()).src = '$url?msg=' + e(errorMsg) +
        '&level=$level' +
        '&context[file]=' + e(file) +
        '&context[line]=' + e(line) +
        '&context[browser]=' + e(navigator.userAgent) +
        '&context[page]=' + e(document.location.href) + basic.fetchCustomContext();
    };
    
    basic.fetchCustomContext = function fetchCustomContext() {
        var key,
            e = encodeURIComponent,
            customContext = window.nelmio_js_logger_custom_context,
            customContextStr = '';
         
        if ('object' === typeof customContext) {
            for (key in customContext) {
                customContextStr += '&context[' + e(key) + ']=' + e(customContext[key]);
            }
        }     
        
        return customContextStr;
    };
    
    return basic;
}());
JS;

        if ($this->stackTracePath) {
            $js .= $addStackTraceJs;
        }

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
