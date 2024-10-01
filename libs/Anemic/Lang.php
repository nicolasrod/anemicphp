<?php

declare(strict_types=1);

namespace Anemic {

    /**
     * i18n handling routines
    */
    class Lang
    {

        static string $varname = "lang";

        /**
         * @var array<string> $acceptedLangs
         */
        static array $acceptedLangs = ['es', 'pt', 'en', 'ru'];

        static function Valid(string $lang): string
        {
            if (empty($lang)) {
                return 'en';
            }

            $t = Str::ToLower($lang);
            return in_array($t, static::$acceptedLangs) ? $t : 'en';
        }

        static function Set(string $lang): void
        {
            if (! empty($lang)) {
                $_SESSION[static::$varname] = Lang::Valid($lang);
            }
        }

        static function Get(): string
        {
            return $_SESSION[static::$varname];
        }

        static function IsSet(): bool
        {
            return ! empty($_SESSION[static::$varname]);
        }

        static function InitFromBrowser(): void
        {
            if (! Lang::IsSet()) {
                Lang::Set(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'enen', 0, 2));
            }

            if (isset($_REQUEST[static::$varname])) {
                Lang::Set($_REQUEST[static::$varname]);
            }
        }

        static function URLFor(string $lang): string
        {
            $lang = Lang::Valid($lang);
            $uri = explode("/", $_SERVER['REQUEST_URI']);
            $page = end($uri);
            return explode(".", $page)[0] . ".php?" . urlencode(static::$varname) . "={$lang}";
        }
    }
}
