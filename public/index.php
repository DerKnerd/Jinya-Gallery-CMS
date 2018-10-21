<?php

use Jinya\Kernel;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;

if (function_exists('tideways_xhprof_enable') && array_key_exists('XDEBUG_SESSION', $_COOKIE)) {
    tideways_xhprof_enable(TIDEWAYS_XHPROF_FLAGS_CPU | TIDEWAYS_XHPROF_FLAGS_MEMORY_MU | TIDEWAYS_XHPROF_FLAGS_MEMORY_PMU | TIDEWAYS_XHPROF_FLAGS_MEMORY | TIDEWAYS_XHPROF_FLAGS_NO_BUILTINS);
}

require __DIR__ . '/../vendor/autoload.php';

// The check is to ensure we don't use .env in production
if (!isset($_SERVER['APP_ENV'])) {
    if (!class_exists(Dotenv::class)) {
        throw new \RuntimeException('APP_ENV environment variable is not defined. You need to define environment variables for configuration or add "symfony/dotenv" as a Composer dependency to load variables from a .env file.');
    }
    (new Dotenv())->load(__DIR__ . '/../.env');
}

$env = $_SERVER['APP_ENV'] ?? 'dev';
$debug = $_SERVER['APP_DEBUG'] ?? ('prod' !== $env);

if ($debug) {
    umask(0000);

    Debug::enable();
}

if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? false) {
    Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST);
}

if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts(explode(',', $trustedHosts));
}

$kernel = new Kernel($env, $debug);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);

if (function_exists('tideways_xhprof_disable') && array_key_exists('XDEBUG_SESSION', $_COOKIE)) {
    try {
        $data = Symfony\Component\Yaml\Yaml::dump(tideways_xhprof_disable());
        $xhprofFile = __DIR__ . '/../var/profiler' . $_SERVER['REQUEST_URI'] . '/' . date(DATE_ISO8601) . '.jinya.xhprof';
        if (!file_exists(dirname($xhprofFile))) {
            mkdir(dirname($xhprofFile), 0777, true);
        }
        file_put_contents($xhprofFile, $data);
    } catch (Throwable $exception) {
        $errorLog = __DIR__ . '/../var/profiler/error.log';
        file_put_contents($errorLog, $exception->getMessage() . PHP_EOL . $exception->getTraceAsString(), FILE_APPEND);
    }
}
