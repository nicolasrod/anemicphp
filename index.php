<?php

declare(strict_types=1);

// TODO: comment the code a little bit 

require_once __DIR__ . '/vendor/autoload.php';

use Anemic\{Config, Lang, Str, Page, View};

// App configuration
Config::$config_file = __DIR__ . "/config.ini";

// Any error in PHP, we throw it as an Exception
set_error_handler(function ($num, $str, $file, $line, $context = null) {
    if (Config::Get("production") === "0") {
        echo ">> ERROR: $str, $num - $file - $line - $context";
    }
    throw new ErrorException($str, 0, $num, $file, $line);
});

// Setting default charset as UTF8
$charset = Config::Get("charset", "UTF-8");
ini_set('default_charset', $charset);
mb_internal_encoding($charset);
mb_http_output($charset);

// Error reporting
$prod = Config::Get("production", "0");
ini_set('display_errors', $prod);
ini_set('display_startup_errors', $prod);
error_reporting($prod === "1" ? 0 : E_ALL ^ E_DEPRECATED);

// Start session
session_start();

if (Config::Get("i18n") === "1") {
    Lang::initFromBrowser();
}

// Routing
$server_folder = Config::Get("server_folder");
$uri = ($_SERVER['REQUEST_URI'] ?? '');

if (! empty($uri)) {
    $uri = Str::GetTo($uri, "?");

    if (Str::HasPrefix($uri, $server_folder)) {
        $uri = Str::RemovePrefix($uri, $server_folder);
    }
}

$path = __DIR__ . Config::Get("pages_folder", "/pages") . $uri;
$folder = dirname($path);
$php_file = $folder . DIRECTORY_SEPARATOR . basename($path) . ".php";

// If URI is empty, go to the default path (if specified in config file)
if (empty($uri) || trim($uri) === "/") {
    $initial_path = Config::Get("initial_path");

    if (! empty($initial_path)) {
        Page::Redirect($initial_path);
    }
}

if (! file_exists($php_file)) {
    Page::ErrorNotFound();
}

// Set folder for locating the views
View::SetBaseFolder(__DIR__ . Config::Get("views_folder", "/views"));
require $php_file;
