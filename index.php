<?php

declare(strict_types=1);

// TODO: comment the code a little bit 

require_once __DIR__ . '/vendor/autoload.php';

// Any error in PHP, we throw it as an Exception
set_error_handler(function ($num, $str, $file, $line, $context = null) {
    throw new ErrorException($str, 0, $num, $file, $line);
});

// App configuration
Anemic\Config::$config_file = __DIR__ . "/config.ini";

// Setting default charset as UTF8
$charset = Anemic\Config::Get("charset", "UTF-8");
ini_set('default_charset', $charset);
mb_internal_encoding($charset);
mb_http_output($charset);

// Error reporting
$prod = Anemic\Config::Get("production", "0");
ini_set('display_errors', $prod);
ini_set('display_startup_errors', $prod);
error_reporting($prod === "1" ? 0 : E_ALL ^ E_DEPRECATED);

// Start session
session_start();

if (Anemic\Config::Get("i18n") === "1") {
    Anemic\Lang::initFromBrowser();
}

// Routing
$server_folder = Anemic\Config::Get("server_folder");
$uri = ($_SERVER['REQUEST_URI'] ?? '');

if (! empty($uri)) {
    if (Anemic\Str::HasPrefix($uri, $server_folder)) {
        $uri = Anemic\Str::RemovePrefix($uri, $server_folder);
    }
}

$path = __DIR__ . Anemic\Config::Get("pages_folder", "/pages") . $uri;
$folder = dirname($path);
$php_file = $folder . DIRECTORY_SEPARATOR . basename($path) . ".php";

// If URI is empty, go to the default path (if specified in config file)
if (empty($uri) || trim($uri) === "/") {
    $initial_path = Anemic\Config::Get("initial_path");

    if (! empty($initial_path)) {
        Anemic\Page::Redirect($initial_path);
    }
}

//
if (! file_exists($php_file)) {
    Anemic\Page::ErrorNotFound();
}

// Set folder for locating the views
Anemic\View::SetBaseFolder(__DIR__ . Anemic\Config::Get("views_folder", "/views"));
require $php_file;
