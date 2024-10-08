<?php

declare(strict_types=1);

namespace Anemic {
    /**
     * Page handling helpers
     */
    class Page
    {
        static function Trace(string $msg): void
        {
            file_put_contents("trace.log", $msg . "\n", FILE_APPEND | LOCK_EX);
        }

        static function TraceVar(mixed $v): void
        {
            file_put_contents("trace.log", var_export($v, true) . "\n", FILE_APPEND | LOCK_EX);
        }

        static function QS(...$items): string
        {
            $s = "?";
            for ($i = 0; $i < count($items); $i += 2) {
                $s .= $items[$i] . "=" . $items[$i + 1] . "&";
            }
            return $s;
        }

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
            header("Location: {$url}", true, 303);

            if ($exits) {
                exit;
            }
        }

        static function PushState(): void
        {
            $_SESSION["_savedstate_post"] = $_POST;
            $_SESSION["_savedstate_get"] = $_GET;
            var_dump($_SESSION["_savedstate_get"]);
            var_dump($_SESSION["_savedstate_post"]);
        }

        static function PopState(): void
        {
            $r = $_SESSION["_savedstate_post"] ?? [];
            array_map(function ($key) use ($r) {
                $_POST[$key] = $r[$key];
            }, array_keys($r));

            $r = $_SESSION["_savedstate_get"] ?? [];
            array_map(function ($key) use ($r) {
                $_POST[$key] = $r[$key];
            }, array_keys($r));

            unset($_SESSION["_savedstate_post"]);
            unset($_SESSION["_savedstate_get"]);
        }

        static function Self(string $qs = ""): string
        {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ||
                $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
            return "{$protocol}{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}?{$qs}";
        }

        static function RedirectToSelf(string $qs = ""): void
        {
            $url = static::Self($qs);
            header("Location: {$url}", true, 303);
            exit;
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
