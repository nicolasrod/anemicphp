<?php

declare(strict_types=1);

namespace Anemic {
    /**
     * Page handling helpers
     */
    class Page
    {
        /**
         * Redirect to another URL and finish execution
         * 
         * @param string $url URL to redirect the app to
         * @param bool $exits finish execution after sending the redirect header
         * 
         * @return void
         */
        static function Redirect(string $url, bool $exits = true): void
        {
            header("Location: {$url}");

            if ($exits) {
                exit;
            }
        }

        /**
         * Is the request sent via HTTP POST?
         * 
         * @return bool
         */
        static function IsPOST(): bool
        {
            return Str::ToUpper($_SERVER['REQUEST_METHOD']) === 'POST';
        }

        /**
         * Only allow specified methods to acces the page
         * 
         * @param array<string> $methods list of methods to allow
         * @return void
         */
        static function CheckValidMethod(array $methods): void
        {
            $methods = array_map(fn($it) => Str::ToUpper($it), $methods);

            if (array_search(Str::ToUpper($_SERVER['REQUEST_METHOD']), $methods) === false) {
                die("Invalid HTTP Method");
            }
        }

        /**
         * Is the request sent via HTTP GET?
         * 
         * @return bool
         */
        static function IsGET(): bool
        {
            return Str::ToUpper($_SERVER['REQUEST_METHOD']) === 'GET';
        }

        /**
         * Send an HTTP 404 header and finish execution
         * @return void
         */
        static function ErrorNotFound(): void
        {
            header('HTTP/1.0 404 Not Found');
            die('404 Not Found');
        }

        /**
         * Send an HTTP 500 header and finish execution
         * @return void
         */
        static function ErrorServer(): void
        {
            header('HTTP/1.0 500 Internal Server Error');
            die('500 Internal Server Error');
        }

        /**
         * Send an HTTP 401 header and finish execution
         * @return void
         */
        static function ErrorUnauthorized(): void
        {
            header('HTTP/1.0 401 Unauthorized');
            die('Unauthorized');
        }
    }
}
