### 1.4.1 (2017-12-15)

  * Fixed logging in a loop when logging appears to fail
  * Fixed Symfony 3.4+ deprecation warnings

### 1.4.0 (2017-05-21)

  * Added `use_stacktrace_js` option to send stack traces with the logs
  * Updated Symfony requirement to allow Symfony v4

### 1.3.0 (2016-11-28)

  * Updated Symfony requirement to 2.7+ and switched to using the PSR-3 log interfaces

### 1.2.5 (2016-02-13)

  * Fixed bug in Symfony 3 logger support

### 1.2.4 (2016-01-04)

  * Fixed support for Symfony 3.0

### 1.2.3 (2015-08-20)

  * Fixed support for Twig 2.0

### 1.2.2 (2015-07-31)

  * Fixed deprecation notice with symfony 2.7

### 1.2.1 (2013-11-28)

  * Fixed undefined index error when using window.log() together with ignored script URLs

### 1.2.0 (2013-07-29)

  * Added ability to give more context information by setting window.nelmio_js_logger_custom_context

### 1.1.0 (2013-04-28)

  * Added ability to ignore logs by source (script URL) or message

### 1.0.0 (2013-01-07)

  * Initial release
